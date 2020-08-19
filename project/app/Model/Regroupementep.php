<?php
	/**
	 * Code source de la classe Regroupementep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Regroupementep ...
	 *
	 * @package app.Model
	 */
	class Regroupementep extends AppModel
	{
		public $name = 'Regroupementep';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $order = array( 'Regroupementep.name ASC' );

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $hasMany = array(
			'Ep' => array(
				'className' => 'Ep',
				'foreignKey' => 'regroupementep_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Compositionregroupementep' => array(
				'className' => 'Compositionregroupementep',
				'foreignKey' => 'regroupementep_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		public $validate = array(
			'nbmaxmembre' => array(
				'greaterThanIfNotZero' => array(
					'rule' => array( 'greaterThanIfNotZero', 'nbminmembre' )
				)
			)
		);

		/**
		 * Vérifie la cohérence des niveaux de décision d'un enregistrement.
		 *
		 * @param array $data Les données de l'enregistrement à vérifier.
		 * @return boolean
		 */
		public function verificationNiveauxDecisions( $data ) {
			$return = true;

			$niveaux = array();
			foreach( $this->themes() as $theme ) {
				if( isset( $data[$this->alias][$theme] ) && !empty( $data[$this->alias][$theme] ) && ( $data[$this->alias][$theme] != 'nontraite' ) ) {
					$niveaux[$theme] = $data[$this->alias][$theme];
				}
			}

			$niveauxUniques = array_unique( array_values( $niveaux ) );
			if( count( $niveauxUniques ) > 1 ) {
				foreach( $niveaux as $theme => $niveau ) {
					$this->invalidate( $theme, 'Les niveaux de décisions (EP ou CD) gérés par un regroupement d\'EP doivent être identiques' );
				}
				$return = false;
			}

			return $return;
		}

		/**
		 * Retourne les champs qui ne sont pas validés pour les modèle courant.
		 * On vérifie en plus la cohérence des niveaux de décision.
		 *
		 * @param string $options An optional array of custom options to be made available in the beforeValidate callback
		 * @return array Array of invalid fields
		 */
		public function invalidFields( $options = array() ) {
			parent::invalidFields( $options );

			$this->verificationNiveauxDecisions( $this->data );

			return $this->validationErrors;
		}

		/**
		 * Retourne la liste des thèmes traités par le regroupement.
		 *
		 * @return array
		 */
		public function themes() {
			$enums = $this->enums();
			$blocageThemes = Configure::read( 'Blocage.thematique.ep' );

			foreach( array_keys( $enums[$this->alias] ) as $key ) {
				if( substr( $key, -2 ) != Configure::read( 'Cg.departement' ) &&
					!( Configure::read( 'Commissionseps.sanctionep.nonrespectppae' ) == true && $key == 'sanctionep58') ){
					unset( $enums[$this->alias][$key] );
				}

				if (in_array ($key, (array) $blocageThemes)) {
					unset( $enums[$this->alias][$key] );
				}
			}

			return array_keys( $enums[$this->alias] );
		}

		/**
		 * Retourne les enregistrements pour lesquels une erreur de paramétrage
		 * a été détectée.
		 * Il s'agit des regroupements qui ne traitent aucune thématique ou qui
		 * ont des niveaux de décisions traités tantôt au niveau EP, tantôt au
		 * niveau CG.
		 *
		 * @return array
		 */
		public function storedDataErrors() {
			$themes = $this->themes();

			$conditionsErrors = array(
				'aucune_thematique' => array(),
				'niveau_decision' => array()
			);

			$query = array(
				'fields' => array(
					"{$this->alias}.{$this->primaryKey}",
					"{$this->alias}.{$this->displayField}"
				),
				'conditions' => array(),
				'contain' => false,
			);

			foreach( $themes as $theme ) {
				$query['fields'][] = "{$this->alias}.{$theme}";
				$conditionsErrors['aucune_thematique']["{$this->alias}.{$theme}"] = 'nontraite';

				foreach( $themes as $autreTheme ) {
					if($autreTheme !== $theme) {
						$conditionsErrors['niveau_decision']['OR'][] = array(
							'NOT' => array(
								"{$this->alias}.{$theme}" => 'nontraite',
								"{$this->alias}.{$autreTheme}" => 'nontraite'
							),
							"{$this->alias}.{$theme} <> {$this->alias}.{$autreTheme}"
						);
					}
				}
			}

			// Ajout des champs et des conditions concernant les erreurs
			$Dbo = $this->getDataSource();
			foreach( $conditionsErrors as $errorName => $errorConditions ) {
				$conditions = $Dbo->conditions( $errorConditions, true, false );
				$query['fields'][] = "( {$conditions} ) AS \"{$this->alias}__error_{$errorName}\"";
			}
			$query['conditions']['OR'] = array_values( $conditionsErrors );

			return $this->find( 'all', $query );
		}
	}
?>