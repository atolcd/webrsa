<?php
	/**
	 * Code source de la classe SuperFixture.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	App::uses('FakerManager', 'SuperFixture.Utility');

	/**
	 * La classe SuperFixture permet le chargement de "Super Fixtures", des fixtures
	 * regroupant les données nécésaire à un test.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class BakeSuperFixture
	{
		/**
		 * Stock les foreign key par Nom d'objet
		 * ex: array('Monmodel_789' => array(1, 2, 3))
		 *
		 * @var array
		 */
		protected $_foreignkeys = array();

		/**
		 * Stock le dernier id par Model
		 * ex: array('Monmodel' => 1)
		 *
		 * @var array
		 */
		protected $_modelId = array();

		/**
		 * Stock par Model, les données à y insérer
		 * ex: array('Monmodel' => array(0 => array('un_champ' => 'une valeur', 'un_autre_champ' => 'une autre valeur')))
		 *
		 * @var array
		 */
		protected $_savedData = array();

		/**
		 * Paramêtres à envoyer au Modèle lors de la sauvegarde
		 *
		 * @var array array('validate' => false, 'fieldList' => array(), 'callbacks' => false)
		 */
		public $saveParams = array('validate' => false, 'fieldList' => array(), 'callbacks' => false, 'atomic' => false);

		/**
		 * Constructeur de classe
		 */
		public function __construct() {
			$this->Faker = FakerManager::getInstance('Baker');
		}

		/**
		 * Permet la creation du contenu de la SuperFixture
		 *
		 * @param array $data
		 * @return array
		 */
		public function create($data, $save = false) {
			foreach ((array)$data as $obj) {
				$this->_recursiveCreateFromObject($obj, null, $save);
			}

			return $this->_savedData;
		}

		/**
		 * Produit un rapport sous forme d'array sur les modèles utilisés et le nombre d'enregistrements créé
		 *
		 * @return array
		 */
		public function report() {
			$report = array();
			foreach ($this->_savedData as $modelName => $datas) {
				$report[$modelName] = count($datas);
			}

			return $report;
		}

		/**
		 * Permet la création en arbre
		 * Rempli l'attribu $_savedData
		 *
		 * @param BSFObject $obj
		 * @param BSFObject $from
		 */
		protected function _recursiveCreateFromObject(BSFObject $obj, BSFObject $from = null, $saveInDb = false) {
			$Model = ClassRegistry::init($obj->modelName);
			$fields = $obj->fields;
			$fieldsName = array_keys($Model->schema());

			if (!is_array($fields)) {
				trigger_error("L'attribut 'fields' d'un BSFObject doit être un Array !");
			}

			// On ajoute des informations avec le schema
			foreach ($Model->schema() as $fieldName => $params) {
				if (Hash::get($params, 'key') === 'primary') {
					continue;
				}

				if (!isset($fields[$fieldName])) {
					$fields[$fieldName] = array();
				}

				if (!is_array($fields[$fieldName])) {
					// Erreur detectée, proposition pour la réparer :
					if (is_string($fields[$fieldName])) {
						$key =  preg_match('/^([\w_]+)_[\d]+$/', $fields[$fieldName], $matches)
							? 'foreignkey'
							: 'value'
						;
						$value = $key === 'foreignkey' ? '$'.$matches[1].'->getName()' : "'".$fields[$fieldName]."'";
						$msgsup = "Vous avez probablement voulu faire '$fieldName' => array('$key' => $value)";
					} else {
						$msgsup = '';
					}

					trigger_error("La valeur de la clef '$fieldName' dans '".$obj->modelName."' doit être un array ! ".$msgsup);
				}

				$fields[$fieldName] += $params;
				$fields[$fieldName] += array(
					'value' => '',
					'auto' => !$fields[$fieldName]['null'],
					'faker' => array()
				);
			}

			// Reset ou defini la liste de variable pour l'objet actif
			$this->_foreignkeys[$obj->getName()] = array();

			for ($i=0; $i<$obj->runs; $i++) {
				$save = array();

				// On creer l'array de sauvegarde
				foreach ($fields as $fieldName => $params) {
					if (!in_array($fieldName, $fieldsName)) {
						$msgstr = "Le champ ".$fieldName." n'existe pas dans ".$obj->modelName." ! Champs disponibles: ".implode( ', ', $fieldsName );
						trigger_error( $msgstr );
					}

					// Si une valeur par defaut existe et qu'aucune valeur n'est defini
					if ($params['default'] !== '' && $params['value'] === '' && !Hash::get($params, 'foreignkey')) {
						continue;
					}

					// On cast dans le cas d'un attribut faker
					$params['faker'] = (array)$params['faker'];

					// Tente de trouver une foreignkey
					if ($params['type'] === 'integer' && !Hash::get($params, 'foreignkey')
						&& preg_match('/^([\w]+)_id$/', $fieldName, $matches)
					) {
						$modelName = Inflector::camelize($matches[1]);
						if ($from !== null && $from->modelName === $modelName) {
							$params['value'] = end($this->_foreignkeys[$from->getName()]);
						} elseif ($lastValue = $this->_findForeignkey($modelName)) {
							$params['value'] = $lastValue;
						}
					}

					// Si auto est activé, on genère une valeur
					if ($params['auto'] && !Hash::get($params, 'foreignkey') && $params['value'] === '') {
						$params['value'] = $this->_generateFakeValue($params);
					}

					// Cas du foreignkey
					if (Hash::get($params, 'foreignkey') && $params['value'] === '') {
						if (isset($this->_foreignkeys[$params['foreignkey']])) {
							$params['value'] = end($this->_foreignkeys[$params['foreignkey']]);
						} else {
							trigger_error("La clef étrangère '{$params['foreignkey']}' n'existe pas au moment de la création de {$obj->getName()}. Assurez-vous que l'enregistrement existe avant de tenter de l'attribuer à une table.");
						}
					}

					// Si une valeur est defini, on ajoute à l'array de sauvegarde
					if ($params['value'] !== '') {
						if (Hash::get($params, 'length')) {
							$save[$fieldName] = substr((string)$params['value'], 0, $params['length']);
						} else {
							$save[$fieldName] = $params['value'];
						}
					} else {
						$save[$fieldName] = null;
					}
				}

				// On simule un id
				if (!isset($this->_modelId[$Model->alias])) {
					$this->_modelId[$Model->alias] = 1;
				} else {
					$this->_modelId[$Model->alias]++;
				}

				// Sauvegarde des données à enregistrer
				if (!isset($this->_savedData[$Model->alias])) {
					$this->_savedData[$Model->alias] = array();
				}
				$this->_savedData[$Model->alias][$this->_modelId[$Model->alias]] = $save;

				// Si on sauvegarde en base, on récupère l'id, sinon on utilise l'id simulé
				if ($saveInDb) {
					$Model->create($save);
					$success = $Model->save(null, $this->saveParams);
					$this->_foreignkeys[$obj->getName()][] = $Model->id;
				} else {
					$this->_foreignkeys[$obj->getName()][] = $this->_modelId[$Model->alias];
				}

				// Si l'enregistrement en base échoue, on affiche les erreurs pour une correction facile
				if ($saveInDb && !$success) {
					$msgstr = "L'enregistrement sur ".$obj->modelName." a échoué !";
					$msgstr .= "\nValidation errors: ".json_encode( $Model->validationErrors );
					$msgstr .= "\nContenu des données: ".json_encode( $save );
					trigger_error( $msgstr );
				}

				// Si l'objet a un contain, on l'utilise pour créer d'autres enregistrements
				if (!empty($obj->contain)) {
					foreach ((array)$obj->contain as $contain) {
						$this->_recursiveCreateFromObject($contain, $obj, $saveInDb);
					}
				}
			}
		}

		/**
		 * Appel dynamique aux fonctions selon le type du champ (la fonction doit exister dans cette classe)
		 * ou selon les paramêtres fake, dans ce cas c'est un appel dynamique aux fonctions de Fake
		 *
		 * @see https://github.com/fzaninotto/Faker
		 *
		 * @param array $params
		 * @return String
		 */
		protected function _generateFakeValue(array $params) {
			$Faker = Hash::get($params, 'unique') ? $this->Faker->unique(Hash::get($params, 'reset')) : $this->Faker;

			if (Hash::get($params, 'in_array')) {
				return $Faker->randomElement((array)$params['in_array']);
			}

			if (empty($params['faker'])) {
				$functionName = '_genByType_'.$params['type'];
				return $this->{$functionName}($params, $Faker);
			}

			if (isset($params['faker']['rule'])) {
				$ruleName = $params['faker']['rule'];
				unset($params['faker']['rule']);

				return call_user_func_array(array($Faker, $ruleName), $params['faker']);

			} else {
				return $Faker->{$params['faker'][0]}();
			}
		}

		/**
		 * Génère une phrase pour les varchar
		 *
		 * @param array $params
		 * @param Object $Faker
		 * @return String
		 */
		protected function _genByType_string(array $params, $Faker) {
			return $Faker->sentence;
		}

		/**
		 * Genere une année aléatoire en fonction de minYear et de maxYear
		 * Par defaut, la période s'étand de <NOW()-10> à <NOW()>
		 *
		 * @param array $params
		 * @param object $Faker
		 * @return String
		 */
		protected function _genYear($params, $Faker) {
			$minYear = Hash::get($params, 'minYear') ? $params['minYear'] : date('Y') -10;
			$maxYear = Hash::get($params, 'maxYear') ? $params['maxYear'] : date('Y');

			// Pour stabilité dans les tests unitaires, ne pas utiliser rand()
			return $Faker->numberBetween($minYear, $maxYear);
		}

		/**
		 * Génère une phrase pour les date
		 *
		 * @param array $params
		 * @param Object $Faker
		 * @return String
		 */
		protected function _genByType_date(array $params, $Faker, $format = 'Y-m-d') {
			$dateString = preg_replace('/^[0-9]{4}/', $this->_genYear($params, $Faker), $Faker->dateTime('2015-12-30 00:00:00')->format($format));
			// Converti un 29 février en 01 mars si l'année a été changé par une année non bissextile
			return date_format(new DateTime($dateString), $format);
		}

		/**
		 * Génère une phrase pour les datetime
		 *
		 * @param array $params
		 * @param Object $Faker
		 * @return String
		 */
		protected function _genByType_datetime(array $params, $Faker) {
			return $this->_genByType_date($params, $Faker, 'Y-m-d H:i:s');
		}

		/**
		 * Génère une phrase pour les integer
		 *
		 * @param array $params
		 * @param Object $Faker
		 * @return String
		 */
		protected function _genByType_integer(array $params, $Faker) {
			return $Faker->randomNumber;
		}

		/**
		 * Génère un texte pour les text
		 *
		 * @param array $params
		 * @param Object $Faker
		 * @return String
		 */
		protected function _genByType_text(array $params, $Faker) {
			return $Faker->text;
		}

		/**
		 * Génère un boolean pour les boolean
		 *
		 * @param array $params
		 * @param Object $Faker
		 * @return String
		 */
		protected function _genByType_boolean(array $params, $Faker) {
			return (integer)$Faker->boolean(50);
		}

		/**
		 * Permet de trouver la derniere foreignkey pour un nom de modèle particulier
		 *
		 * @param String $modelName
		 * @return integer
		 */
		protected function _findForeignkey($modelName) {
			foreach (array_keys($this->_foreignkeys) as $objName) {
				if (preg_match("/^{$modelName}_[0-9]+$/", $objName)) {
					return end($this->_foreignkeys[$objName]);
				}
			}

			return false;
		}
	}