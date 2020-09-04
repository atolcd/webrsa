<?php
	/**
	 * Code source de la classe Configuration.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Configuration ...
	 *
	 * @package app.Model
	 */
	class Configuration extends AppModel
	{
		public $name = 'Configuration';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'configurations';

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'ConfigurationCategorie' => array(
				'className' => 'ConfigurationCategorie',
				'foreignKey' => 'configurationscategorie_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Requête à utiliser pour lister les enregistrements par parent puis par
		 * ordre alphabétique.
		 *
		 * @return array
		 */
 		public function _query($search) {
			$query = array(
				'recursive' => 1,
				'order' => array(
					'ConfigurationCategorie.lib_categorie ASC',
					'Configuration.lib_variable ASC'
				)
			);

			// Recherche sur le nom de la variable
			if(isset($search['Configuration']['lib_variable'])) {
				$query['conditions'][] = array('Configuration.lib_variable ILIKE' => '%'.$search['Configuration']['lib_variable'].'%');
			}

			// Recherche sur la valeur de la variable
			if(isset($search['Configuration']['value_variable'])) {
				$query['conditions'][] = array('Configuration.value_variable ILIKE' => '%'.$search['Configuration']['value_variable'].'%');
			}

			// Recherche sur la catégorie
			if($search['ConfigurationCategorie']['lib_categorie'] !== '') {
				$query['conditions'][] = array('ConfigurationCategorie.id' => $search['ConfigurationCategorie']['lib_categorie']);
			}

			return $query;
		}

		/*
		 *  Lit en BDD les configurations liés à la catégorie passée en paramètre
		 *  @param string $nomCategorie
		 *
		 *  @return array
		*/
		public function getConfiguration($nomCategorie) {
			$param = array();
			if($nomCategorie !== 'all') {
				$idCat = $this->ConfigurationCategorie->getCategorie($nomCategorie);
				$param['conditions'] = array(
					'Configuration.configurationscategorie_id' => $idCat
				);
			}
			$param['fields'] = array(
					'Configuration.lib_variable',
					'Configuration.value_variable',
					'Configuration.comments_variable'
			);
			return $this->find('all', $param);

		}

		/*
		 *  Écrit toutes les configurations liées à toutes les catégories passées en paramètre dans la configuration de l'application
		 *  @param string $nomCategorie
		 *
		*/
		public function setAllConfigurationsAllCategories() {
			$categories = $this->ConfigurationCategorie->find('list', array('fields' => 'lib_categorie' ));
			foreach ($categories as $categorie) {
				$this->setAllConfigurations($categorie);
			}
		}

		/*
		 *  Appelle à écrire les configurations liées à la catégorie passée en paramètre dans la configuration de l'application
		 *  @param string $nomCategorie
		 *
		*/
	 	public function setAllConfigurations($nomCategorie) {
			$confsToWrite = $this->getConfiguration($nomCategorie);
			foreach ($confsToWrite as $conf) {
				$this->setConfiguration($conf);
			}
		}

		/*
		 *  Ecrit les configurations liées à la catégorie passée en paramètre dans la configuration de l'application
		 *  @param string $nomCategorie
		 *
		*/
		public function setConfiguration($configuration) {
				$contenuVariable = $configuration['Configuration']['value_variable'];
				if(strpos($contenuVariable, 'TAB::') != false){
					$contenuVariable = $this->getDate($contenuVariable, 'TAB::');
				}
				if(strpos($contenuVariable, 'TEXT::') != false){
					$contenuVariable = $this->getDate($contenuVariable, 'TEXT::');
				}
				Configure::write( $configuration['Configuration']['lib_variable'], json_decode($contenuVariable, true));
		}

		/*
		 *  Change une date en code BDD et vice versa dans une chaine passée en parametre
		 *  @param string $chaineCode
		 *  @param string $subCode
		 *
		 *  @return $chaineCode
		*/
		protected function getDate($chaineCode, $subCode) {
			$searchCode = 1;
			while($searchCode != false) {
				$posChaine = strpos($chaineCode, $subCode, $searchCode);
				if($posChaine !== false){
					$posFin = strpos($chaineCode, '"', $posChaine);
					$chaineToDecode = substr($chaineCode, $posChaine, $posFin-$posChaine);

					$chaineToDecode = $this->convertCodetoDate($chaineToDecode);

					$chaineDecode = substr_replace($chaineCode, json_encode($chaineToDecode, JSON_UNESCAPED_UNICODE), $posChaine-1, $posFin-$posChaine+2);
					$chaineCode = $chaineDecode;
					$searchCode = $posFin;

				} else {
					$searchCode = false;
				}

			}
			return $chaineDecode;
		}

		/*
		 *  Change un code BDD en date dans une chaine passée en parametre
		 *  @param string $chaineCode
		 *  @param string $subCode
		 *
		 *  @return $chaineCode
		*/
		public function convertDate($chaineCode, $subCode = NULL){
			return $this->convertCodetoDate($chaineCode) ;
		}

		/*
		 *  Change un code BDD en date dans une chaine passée en parametre
		 *  @param string $chaineCode
		 *  @param string $subCode
		 *
		 *  @return $chaineCode
		*/
		private function convertCodetoDate($chaineToDecode){
			switch ($chaineToDecode) {
				// Cas des tableaux
					case 'TAB::-1WEEK' :
						$chaineToDecode = date_sql_to_cakephp( date( 'Y-m-d', strtotime( '-1 week' ) ) );
						break;

					case 'TAB::NOW' :
						$chaineToDecode = date_sql_to_cakephp( date( 'Y-m-d', strtotime( 'now' ) ) );
						break;

					case 'TAB::FDOTM' :
						$chaineToDecode = date_sql_to_cakephp( date( 'Y-m-d', strtotime( 'first day of this month' ) ) );
						break;

					case 'TAB::-1MONTH' :
						$chaineToDecode = date_sql_to_cakephp( date( 'Y-m-d', strtotime( '-1 month' ) ) );
						break;

					case 'TAB::-3MONTHS' :
						$chaineToDecode = date_sql_to_cakephp( date( 'Y-m-d', strtotime( '-3 months' ) ) );
						break;

					case 'TAB::+1DAY' :
						$chaineToDecode = date_sql_to_cakephp( date( 'Y-m-d', strtotime( '+1 day' ) ) );
						break;

				// Cas des chaines de caracteres
					case 'TEXT::+3MONTHS' :
						$chaineToDecode = date_format(date_add(new DateTime(), date_interval_create_from_date_string('+3 months')), 'Y-m-d');
						break;
					case 'TEXT::-1MONTH' :
						$chaineToDecode =  date( 'Y-m-d', strtotime( '-1 month' ) );
						break;

					case 'TEXT::+1DAY' :
						$chaineToDecode = date( 'Y-m-d', strtotime( '+1 day' ) );
						break;

					case 'TEXT::NOW' :
						$chaineToDecode = date('Y-m-d');
						break;

					case 'TEXT::ONLYYEAR' :
						$chaineToDecode = date( 'Y' );
						break;

					case 'STRTOTIME::-1WEEK' :
						$chaineToDecode = strtotime( '-1 week' );
						break;

					case 'STRTOTIME::NOW' :
						$chaineToDecode = strtotime( 'now' );
						break;

					default:
						break;
				}
			 return $chaineToDecode;
		}


	}