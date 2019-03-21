<?php
	/**
	 * Code source de la classe AppModel.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Model', 'Model' );
	App::uses( 'Sanitize', 'Utility' );
	App::uses( 'ValidateAllowEmptyUtility', 'Utility' );

	/**
	 * La classe AppModel est la classe parente de toutes les classes de modèle
	 * de l'application.
	 *
	 * @package app.Model
	 */
	class AppModel extends Model
	{
		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array( 'Containable', 'DatabaseTable' );

		/**
		 * Champs virtuels pour ce modèle
		 *
		 * @var array
		 */
		public $virtualFields = array( );

		/**
		 * Permet de forcer l'utilisation des champs virtuels pour les modèles liés
		 */
		public $forceVirtualFields = false;

		/**
		 * Liste des champs où la valeur du notEmpty/allowEmpty est configurable
		 *
		 * @var array
		 */
		public $configuredAllowEmptyFields = array();

		/**
		 * Contient la liste des modules (au sens applicatif) auxquels ce modèle est lié.
		 *
		 * @var array
		 */
		protected $_modules = array( );

		/**
		 * Default fields that are used by the DBO
		 * @see Model/Datasource/DboSource.php
		 *
		 * @var array
		 */
		public $queryDefaults = array(
			'conditions' => array(),
			'fields' => null,
			'order' => null,
			'limit' => null,
			'joins' => array(),
			'group' => null,
			'offset' => null
		);

		/**
		 * Cache "live", notamment utilisé par la méthode enums.
		 *
		 * @var array
		 */
		protected $_appModelCache = array(
			'enums' => array()
		);

		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 *
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array();

		/**
		 * Surcharge du constructeur pour les champs virtuels.
		 * Si un driver a été fourni, on utilise la sous-requête correspondante.
		 *
		 * @param integer|string|array $id Set this ID for this model on startup, can also be an array of options, see above.
		 * @param string $table Name of database table to use.
		 * @param string $ds DataSource connection name.
		 */
		public function __construct( $id = false, $table = null, $ds = null ) {
			parent::__construct( $id, $table, $ds );

			// Champs virtuels, on ne se préoccupe que du driver utilisé.
			if( isset( $this->virtualFields ) && !empty( $this->virtualFields ) ) {
				$driver = $this->driver();

				foreach( $this->virtualFields as $name => $value ) {
					if( is_array( $value ) && isset( $value[$driver] ) ) {
						$this->virtualFields[$name] = $value[$driver];
					}
				}
			}

			// Remplacement des %s par l'alias du modèle dans les attributs order et virtualFields
			foreach( array( 'order', 'virtualFields' ) as $attribute ) {
				if( false === empty( $this->{$attribute} ) ) {
					$this->{$attribute} = alias( $this->{$attribute}, array( '%s' => $this->alias ) );
				}
			}

			ValidateAllowEmptyUtility::initialize( $this );
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les fonctions vides.
		 */
		public function prechargement() {
			return null;
		}

		/**
		 * Débute une transaction.
		 *
		 * INFO: en CakePHP 2.x, il n'y a pas de paramètre à passer.
		 *
		 * @return boolean
		 */
		public function begin() {
			$return = $this->getDataSource()->begin( $this );
			return $return;
		}

		/**
		 * Valide une transaction.
		 *
		 * INFO: en CakePHP 2.x, il n'y a pas de paramètre à passer.
		 *
		 * @return boolean
		 */
		public function commit() {
			$return = $this->getDataSource()->commit( $this );
			return $return;
		}

		/**
		 * Utilisation des champs virtuels dans les modèles liés (même CakePHP 2.x ne les gère pas)
		 * lorsque l'attribut forceVirtualFields est à true.
		 *
		 * @param array $queryData
		 * @return mixed
		 */
		public function beforeFind( $queryData ) {
			$return = parent::beforeFind( $queryData );
			if( is_bool( $return ) ) {
				if( $return === false ) {
					return false;
				}
			}
			else {
				$queryData = $return;
			}

			if( $this->forceVirtualFields ) {
				$dbo = $this->getDataSource();
				$aliases = Hash::combine( $queryData, 'joins.{n}.alias', 'joins.{n}.table' );
				$linkedModels = array_keys( $aliases );
				$contains = Hash::get( $queryData, 'contain' );

				if( !empty( $contains ) ) {
					$linkedModels += array_keys( (array)Hash::normalize( (array)$contains ) );
				}

				if( !empty( $linkedModels ) ) {
					foreach( $linkedModels as $linkedModel ) {
						$settings = $linkedModel;
						if( isset( $aliases[$linkedModel] ) ) {
							$class = Inflector::classify( trim( $aliases[$linkedModel], '"' ) );
							if( $class !== $linkedModel ) {
								$settings = array( 'alias' => $linkedModel, 'class' => $class );
							}
						}

						$linkedModel = ClassRegistry::init( $settings );
						if( !empty( $linkedModel->virtualFields ) ) {
							$replacements = array();
							$replacementsFields = array();

							foreach( $linkedModel->virtualFields as $fieldName => $query ) {
								//$regex = "/(?<!\.)(?<!\w)({$linkedModel->alias}\.){0,1}{$fieldName}(?!\w)/";
								$regex = "/(?<!\.)(?<!\w){$linkedModel->alias}\.{$fieldName}(?!\w)/";
								$alias = "{$query} {$dbo->alias} {$dbo->startQuote}{$linkedModel->alias}__{$fieldName}{$dbo->endQuote}";
								$replacementsFields[$regex] = $alias;

								$replacements[$regex] = $query;
							}

							$queryData['fields'] = recursive_key_value_preg_replace( (array)$queryData['fields'], $replacementsFields );

							foreach( array( 'conditions', 'order', 'group' ) as $type ) {
								if( !empty( $queryData[$type] ) ) {
									$queryData[$type] = recursive_key_value_preg_replace( (array)$queryData[$type], $replacements );
								}
							}
						}
					}
				}
			}

			return $queryData;
		}

		/**
		 * Annule une transaction.
		 *
		 * INFO: en CakePHP 2.x, il n'y a pas de paramètre à passer.
		 *
		 * @return boolean
		 */
		public function rollback() {
			$return = $this->getDataSource()->rollback( $this );
			return $return;
		}

		/**
		 * Remplace le caractère * par le caractère % pour les requêtes SQL.
		 *
		 * @param type $value
		 * @return type
		 */
		public function wildcard( $value ) {
			return str_replace( '*', '%', Sanitize::escape( $value ) );
		}

		/**
		 * Permet d'unbinder toutes les associations d'un modèle en une fois.
		 *
		 * @param type $reset Si true, les associations seront rebindées après le find.
		 * @return void
		 * @see http://bakery.cakephp.org/articles/view/unbindall
		 */
		public function unbindModelAll( $reset = true ) {
			$unbind = array( );
			foreach( $this->belongsTo as $model => $info ) {
				$unbind['belongsTo'][] = $model;
			}
			foreach( $this->hasOne as $model => $info ) {
				$unbind['hasOne'][] = $model;
			}
			foreach( $this->hasMany as $model => $info ) {
				$unbind['hasMany'][] = $model;
			}
			foreach( $this->hasAndBelongsToMany as $model => $info ) {
				$unbind['hasAndBelongsToMany'][] = $model;
			}
			parent::unbindModel( $unbind, $reset );
		}

		/**
		 * Retourne les résultats d'une opération de sauvegarde sous forme d'un
		 * booléen.
		 *
		 * @param mixed $result
		 * @return boolean
		 */
		public function saveResultAsBool( $result ) {
			if( is_array( $result ) ) {
				foreach( Hash::flatten( $result ) as $boolean ) {
					if( $boolean === false ) {
						return false;
					}
				}

				return true;
			}
			else {
				return $result;
			}
		}

		/**
		 * Retourne un booléen dans tous les cas.
		 *
		 * @param array $data Record data to save. This can be either a numerically-indexed array (for saving multiple
		 *     records of the same type), or an array indexed by association name.
		 * @param array $options Options to use when saving record data, See $options above.
		 * @return boolan
		 */
		public function saveAll( $data = array( ), $options = array( ) ) {
			$result = parent::saveAll( $data, $options );
			return $this->saveResultAsBool( $result );
		}

		/**
		 * Filtre zone géographique
		 * FIXME: à supprimer et utiliser le ConditionnableBehavior
		 */
		public function conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee ) {
			if( $filtre_zone_geo ) {
				// Si on utilise la table des cantons plutôt que la table zonesgeographiques
				if( Configure::read( 'CG.cantons' ) ) { // FIXME: est-ce bien la signification de la variable ?
					return ClassRegistry::init( 'Canton' )->queryConditionsByZonesgeographiques( array_keys( $mesCodesInsee ) );
				}
				else {
					$mesCodesInsee = (!empty( $mesCodesInsee ) ? $mesCodesInsee : array( null ) );
					return '( Adresse.numcom IN ( \''.implode( '\', \'', $mesCodesInsee ).'\' ) /*OR ( Situationdossierrsa.etatdosrsa = \'Z\' ) */ )'; ///FIXME: passage de OR à AND car les dossiers à Z mais non présents dans le code insee apparaissaient !!!!!!!
				}
			}
		}

		/**
		 * Permet de vérifier la syntaxe d'intervalles au sens PostgreSQL.
		 *
		 * @param string|array $keys La ou les clés de configuration à vérifier.
		 * @param boolean $asBoolean Si true, retourne le résultat sous forme de
		 *	booléen, sinon retourne un tableau contenant, pour chacune des clés,
		 *  un tableau contenant les clés "success", "message" et "value" utilisé
		 *  dans la vérification de l'application.
		 * @return array|boolean
		 */
		protected function _checkPostgresqlIntervals( $keys, $asBoolean = false ) {
			$result = array();

			foreach( (array)$keys as $key ) {
				$value = Configure::read( $key );
				$result[$key] = $this->getDataSource()->checkPostgresIntervalSyntax( $value );
			}

			if( $asBoolean ) {
				$booleans = Hash::extract( $result, '{s}.success' );
				$result = !in_array( false, $booleans, true );
			}

			return $result;
		}

		/**
		 * Retourne la sous-requête d'un des champs virtuels se trouvant dans
		 * $this->virtualFields.
		 *
		 * @param string $field
		 * @param string $alias
		 * @return string
		 * @throws RuntimeException
		 */
		public function sqVirtualField( $field, $alias = true ) {
			$virtualField = Hash::get( $this->virtualFields, $field );

			if( empty( $virtualField ) ) {
				$message = "Virtual field \"{$field}\" does not exist in model \"{$this->alias}\".";
				throw new RuntimeException( $message, 500 );
				return null;
			}

			$sq = "( $virtualField )";
			if( $alias ) {
				$sq = "{$sq} AS \"{$this->alias}__{$field}\"";
			}

			return $sq;
		}

		/**
		 * Retourne la liste des modules auxquels ce modèle est lié.
		 *
		 * @return array
		 */
		public function modules() {
			return $this->_modules;
		}

		/**
		 * Vérifie si ce modèle appartient à un module donné.
		 *
		 * @param string $name Le nom du module dont l'on veut tester l'appartenance.
		 * @param boolean $only Si vrai, on vérifie en plus que le module testé est
		 * 	le seul auquel appartient ce modèle.
		 * @return boolean
		 */
		public function inModule( $name, $only = false ) {
			if( in_array( $name, $this->_modules ) ) {
				if( $only === true ) {
					return ( count( $this->_modules ) == 1 );
				}
				return true;
			}
			return false;
		}

		/**
		 * Retourne le nom du driver utilisé par le modèle (postgres, mysql, ...).
		 *
		 * @return string
		 */
		public function driver() {
			$Dbo = $this->getDataSource();

			$class = get_class( $Dbo );
			$parent_class = get_parent_class( $class );

			while( !empty( $parent_class ) && ( $parent_class != 'DboSource' ) ) {
				$class = $parent_class;
				$parent_class = get_parent_class( $parent_class );
			}

			return strtolower( $class );
		}

		/**
		 * Retourne la liste des options venant des champs possédant la règle de
		 * validation inList.
		 *
		 * @return array
		 */
		public function enums() {
			$cacheKey = $this->useDbConfig.'_'.__CLASS__.'_enums_'.$this->alias;

			// Dans le cache "live" ?
			if( false === isset( $this->_appModelCache[$cacheKey] ) ) {
				$this->_appModelCache[$cacheKey] = Cache::read( $cacheKey );

				// Dans le cache CakePHP ?
				if( false === $this->_appModelCache[$cacheKey] ) {
					$this->_appModelCache[$cacheKey] = array();

					$domain = Inflector::underscore( $this->alias );

					// D'autres champs avec la règle inList ?
					foreach( $this->validate as $field => $validate ) {
						foreach( $validate as $ruleName => $rule ) {
							if( ( $ruleName === 'inList' ) && !isset( $this->_appModelCache[$cacheKey][$this->alias][$field] ) ) {
								$fieldNameUpper = strtoupper( $field );

								$tmp = $rule['rule'][1];
								$list = array();

								foreach( $tmp as $value ) {
									$list[$value] = __d( $domain, "ENUM::{$fieldNameUpper}::{$value}" );
								}

								$this->_appModelCache[$cacheKey][$this->alias][$field] = $list;
							}
						}
					}

					// D'autres champs avec $fakeInLists
					foreach( $this->fakeInLists as $field => $values ) {
						if( !isset( $this->_appModelCache[$cacheKey][$this->alias][$field] ) ) {
							$fieldNameUpper = strtoupper( $field );

							$list = array();
							foreach( $values as $value ) {
								$list[$value] = __d( $domain, "ENUM::{$fieldNameUpper}::{$value}" );
							}

							$this->_appModelCache[$cacheKey][$this->alias][$field] = $list;
						}
					}

					Cache::write( $cacheKey, $this->_appModelCache[$cacheKey] );
				}
			}

			return (array)$this->_appModelCache[$cacheKey];
		}

		/**
		 * Permet d'obtenir les valeurs d'un enum particulier, avec possibilité
		 * de tri sur les intitulés.
		 *
		 * @param string $field
		 * @param array $params Les clés disponibles sont: sort (false par défaut,
		 * permet de trier par libellé) et filter (array vide par défaut) qui
		 * permet de ne récupérer que certaines options en fonction de leur clé.
		 * @return array
		 */
		public function enum( $field, array $params = array() ) {
			$params += array( 'sort' => false, 'filter' => array() );

			$enums = $this->enums();
			$values = (array)Hash::get( $enums, "{$this->alias}.{$field}" );

			// Filtre-t-on les clés ?
			$accepted = (array)$params['filter'];
			if( !empty( $accepted ) ) {
				foreach( array_keys( $values ) as $key ) {
					if( false === in_array_strings( $key, $accepted ) ) {
						unset( $values[$key] );
					}
				}
			}

			// Trie-t-on les options par valeur ?
			if( $params['sort'] ) {
				asort( $values );
			}

			return $values;
		}

		/**
		 * Suppression des données du cache.
		 *
		 * INFO: on pourrait en faire un behavior / un plugin ?
		 *
		 * @return void
		 */
		protected function _clearModelCache() {
			$keys = ModelCache::read( $this->name );
			if( !empty( $keys ) ) {
				foreach( $keys as $key ) {
					Cache::delete( $key );
					ModelCache::delete( $key );
				}
			}
		}

		/**
		 * Après une sauvegarde, on supprime les données en cache.
		 *
		 * @param boolean $created True if this save created a new record
		 * @return void
		 */
		public function afterSave( $created, $options = array() ) {
			parent::afterSave( $created, $options );
			$this->_clearModelCache();
		}

		/**
		 * Après une suppression, on supprime les données en cache.
		 *
		 * @param boolean $created True if this save created a new record
		 * @return void
		 */
		public function afterDelete() {
			parent::afterDelete();
			$this->_clearModelCache();
		}

		/**
		 * By default, updateAll() will automatically join any belongsTo
		 * association for databases that support joins. To prevent this,
		 * temporarily unbind the associations.
		 *
		 * @see http://book.cakephp.org/2.0/en/models/saving-your-data.html#model-updateall-array-fields-array-conditions
		 *
		 * @param array $fields
		 * @param mixed $conditions
		 * @return boolean
		 */
		public function updateAllUnBound($fields, $conditions = true) {
			$this->unbindModelAll();
			$success = parent::updateAll($fields, $conditions );
			$this->resetAssociations();

			return $success;
		}

		/**
		 * By default, deleteAll() will automatically join any belongsTo
		 * association for databases that support joins. To prevent this,
		 * temporarily unbind the associations.
		 *
	 	 * Deletes multiple model records based on a set of conditions.
	 	 *
		 * @param mixed $conditions Conditions to match
		 * @param boolean $cascade Set to true to delete records that depend on this record
		 * @param boolean $callbacks Run callbacks
		 * @return boolean True on success, false on failure
		 * @link http://book.cakephp.org/2.0/en/models/deleting-data.html#deleteall
		 */
		public function deleteAllUnBound( $conditions, $cascade = true, $callbacks = false ) {
			$this->unbindModelAll();
			$success = $this->deleteAll( $conditions, $cascade, $callbacks );
			$this->resetAssociations();

			return $success;
		}

		/**
		 * Les éléments de la liste sont triés et préfixés par une chaîne de caractères.
         *
         * @todo prefix/suffix pour avoir correctement le tiret et les retours à la ligne
		 *
		 * @param array $querydata
		 * @param string $prefix
		 * @param string $suffix
		 * @return string
		 */
		public function vfListe( array $querydata, $prefix = '\\n\r-', $suffix = '' ) {
            // FIXME: un seul champ est possible
            foreach( $querydata['fields'] as $i => $field ) {
                list( $modelName, $fieldName ) = model_field( $field );
                $fieldAlias = "{$modelName}__{$fieldName}";
                $querydata['fields'][$i] = "'{$prefix}' || \"{$modelName}\".\"{$fieldName}\" || '{$suffix}' AS \"{$fieldAlias}\"";
            }

            $sql = $this->sq( $querydata );
//			return "TRIM( TRAILING '{$suffix}' FROM ARRAY_TO_STRING( ARRAY( {$sql} ), '' ) )";
            return "TRIM( BOTH '\n\r' FROM TRIM( TRAILING '{$suffix}' FROM ARRAY_TO_STRING( ARRAY( {$sql} ), '' ) ) )";
		}

		/**
		 *
		 * @fixme le mettre ailleurs ... et ne pas oublier de lier éventuellement d'autres modèles
		 *
		 * @param string $prefixKeyField
		 * @param string $suffixKeyField
		 * @param string $displayField
		 * @param array $conditions
		 * @param array $modelNames
		 * @return array
		 */
		public function findListPrefixed( $prefixKeyField, $suffixKeyField, $displayField, array $conditions = array(), array $modelNames = array() ) {
			$query = array(
				'fields' => array(
					"{$this->alias}.{$prefixKeyField}",
					"{$this->alias}.{$suffixKeyField}",
					"{$this->alias}.{$displayField}"
				),
				'order' => array(
					"{$this->alias}.{$prefixKeyField}",
					"{$this->alias}.{$suffixKeyField}",
					"{$this->alias}.{$displayField}"
				),
				'contain' => false,
				'conditions' => $conditions
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $query ) );
			$results = Cache::read( $cacheKey );

			if( $results === false ) {
				$results = $this->find( 'all', $query );

				$results = Hash::combine(
					$results,
					array( '%s_%s', "{n}.{$this->alias}.{$prefixKeyField}", "{n}.{$this->alias}.{$suffixKeyField}" ),
					"{n}.{$this->alias}.{$displayField}"
				);

				$modelNames[] = $this->name;
				$modelNames = array_unique( $modelNames );

				Cache::write( $cacheKey, $results );
				ModelCache::write( $cacheKey, $modelNames );
			}

			return $results;
		}

		/**
		 * Retourne une sous-requête permettant de savoir si des enregistrements
		 * sont liés au modèle principal.
		 *
		 * Les relations hasMany, hasOne et hasAndBelongsToMany sont explorées.
		 *
		 * @todo Voir pour Fichiermodule (blacklist ?), mettre en cache (?)
		 *
		 * @param string $fieldName
		 * @return array
		 */
		public function getSqLinkedModelsDepartement( $fieldName = 'linked_records' ) {
			$departement = Configure::read( 'Cg.departement' );
			if( !$this->Behaviors->attached( 'LinkedRecords' ) ) {
				App::uses( 'LinkedRecordsBehavior', 'Model/Behavior' );
				$this->Behaviors->attach( 'LinkedRecords' );
			}

			$exists = array();

			// Associations hasOne et hasMany
			foreach( $this->hasOne + $this->hasMany as $alias => $params ) {
				if( departement_uses_class( $params['className'] ) ) {
					$exists[$params['className']] = $this->linkedRecordVirtualField( $alias );
				}
			}

			// Associations hasAndBelongsToMany
			foreach( $this->hasAndBelongsToMany as $alias => $params ) {
				if( departement_uses_class( $params['className'] ) ) {
					$exists[$params['with']] = $this->linkedRecordVirtualField( $params['with'] );
				}
			}

			if( !empty( $exists ) ) {
				$return = "( ".implode( " OR ", $exists )." )";
			}
			else {
				$return = "( 1 = 0 )";
			}

			if( !empty( $fieldName ) ) {
				$return = "{$return} AS \"{$this->alias}__{$fieldName}\"";
			}

			return $return;
		}

		// ---------------------------------------------------------------------
		// @todo
		// Expérimental: permet l'utilisation de uses (lazy loading) et de loadModel
		// dans les classes de modèles.
		// Utile pour Option, les classes WebrsaRecherche, les classes de logique
		// métier, les classes "de base" devant contenir de la logique métier.
		//
		// @see: http://josediazgonzalez.com/2010/10/05/using-loadmodel-in-the-model/
		// @see http://blog.andolasoft.com/2013/06/why-implement-fat-model-and-skinny-controller-in-cakephp.html#
		// @see http://mark-story.com/posts/view/reducing-requestaction-use-in-your-cakephp-sites-with-fat-models
		// @see http://www.sanisoft.com/blog/2010/05/31/cakephp-fat-models-and-skinny-controllers/
		// @see http://www.waytocode.com/2014/use-model-inside-another-model-without-association-in-cakephp/
		// @see http://trevweb.me.uk/models-without-tables-in-cakephp/
		// ---------------------------------------------------------------------

		/**
		 * Les classes qui seront utilisées.
		 *
		 * @see __isset(), loadModel()
		 *
		 * @var array
		 */
		public $uses = array();

		/**
		 * Lazy loads models using the loadModel() method if declared in $uses
		 *
		 * @param string $name
		 * @return void
		 */
		public function __isset( $name ) {
			if( is_array( $this->uses ) ) {
				foreach( $this->uses as $modelClass ) {
					list($plugin, $class) = pluginSplit( $modelClass, true );
					if( $name === $class ) {
						return $this->loadModel( $modelClass );
					}
				}
			}

			return parent::__isset( $name );
		}

		/**
		 * Loads and instantiates models required by this controller.
		 * If the model is non existent, it will throw a missing database table error, as Cake generates
		 * dynamic models for the time being.
		 *
		 * @info Copié/Collé/adapté du contrôleur
		 * @todo A factoriser dans WebrsaAbstractLogic
		 * @info Mis en "cache mémoire"
		 *
		 * @param string $modelClass Name of model class to load
		 * @param integer|string $id Initial ID the instanced model class should have
		 * @return mixed true when single model found and instance created, false if already createderror returned if model not found.
		 * @throws MissingModelException if the model class cannot be found.
		 */
		public function loadModel( $modelClass, $id = null ) {
			list($plugin, $modelClass) = pluginSplit( $modelClass, true );

			if( !isset( $this->{$modelClass} ) || $this->{$modelClass} === null ) {
				$this->uses = ($this->uses) ? (array) $this->uses : array();
				if( !in_array( $modelClass, $this->uses ) ) {
					$this->uses[] = $modelClass;
				}

				$this->{$modelClass} = ClassRegistry::init(
					array(
						'class' => $plugin.$modelClass,
						'alias' => $modelClass,
						'id' => $id
					)
				);

				if( !$this->{$modelClass} || get_class( $this->{$modelClass} ) === 'AppModel' ) {
					throw new MissingModelException( $modelClass );
				}
				return true;
			}

			return false;
		}

		/**
		 * Lorsque la configuration de AncienAllocataire.enabled est à true, envoie
		 * la liste des dossiers dans lesquels l'allocataire est à présent sans
		 * prestation mais pour lesquels au moins un enregistrement du modèle existe.
		 *
		 * @param integer $personne_id
		 * @param string $modelAlias
		 */
		public function _setEntriesAncienDossier( $personne_id, $modelAlias ) {
			if( Configure::read( 'AncienAllocataire.enabled' ) ) {
				if (!isset($this->helpers['Default3'])) {
					$this->helpers['Default3'] = array('className' => 'Default.DefaultDefault');
				}
				$entriesAncienDossier = ClassRegistry::init( 'Personne' )->WebrsaPersonne->getEntriesAnciensDossiers( $personne_id, $modelAlias );
				$this->set( compact( 'entriesAncienDossier' ) );
			}
		}

		/**
		 * Affiche des messages dans index
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function messages( $personne_id ) {
			return array ();
		}

		/**
		 * Permet de savoir si un ajout est possible à partir des messages
		 * renvoyés par la méthode messages.
		 *
		 * @param array $messages
		 * @return boolean
		 */
		public function addEnabled( array $messages ) {
			return !in_array( 'error', $messages );
		}

	}