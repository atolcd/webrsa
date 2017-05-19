<?php	
	/**
	 * Code source de la classe Regroupementep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Regroupementep ...
	 *
	 * @package app.Model
	 */
	class Regroupementep extends AppModel
	{
		public $name = 'Regroupementep';

		public $order = array( 'Regroupementep.name ASC' );

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Enumerable' => array(
				'fields' => array(
					// Thèmes 66
					'saisinebilanparcoursep66',
					'saisinepdoep66',
					'defautinsertionep66',
					// Thèmes 93
					'nonrespectsanctionep93',
					'reorientationep93',
					'nonorientationproep93',
					'signalementep93',
					'contratcomplexeep93',
					// Thèmes 58
					'nonorientationproep58',
					'regressionorientationep58',
					'sanctionep58',
					'sanctionrendezvousep58',
				)
			),
			'Formattable'
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

		// INFO: le behavior Autovalidate2 ne trouve pas les contraintes UNIQUE (17/02/2011)
		public $validate = array(
			'name' => array(
				array(
					'rule' => array( 'isUnique' ),
				)
			),
			'nbmaxmembre' => array(
				array(
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
					$this->invalidate( $theme, 'Les niveaux de décisions (EP ou CG) gérés par un regroupement d\'EP doivent être identiques' );
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
			foreach( array_keys( $enums[$this->alias] ) as $key ) {
				if( substr( $key, -2 ) != Configure::read( 'Cg.departement' ) ) {
					unset( $enums[$this->alias][$key] );
				}
			}
			return array_keys( $enums[$this->alias] );
		}

		/**
		 * Retourne les enregistrements pour lesquels une erreur de paramétrage a été détectée.
		 * Il s'agit des regroupements qui ont des niveaux de décisions traités tantôt au niveau EP, tantôt
		 * au niveau CG.
		 *
		 * @return array
		 */
		public function storedDataErrors() {
			$themes = $this->themes();

			$conditions = array( 'OR' => array() );
			$fields = array(
				"{$this->alias}.{$this->primaryKey}",
				"{$this->alias}.{$this->displayField}"
			);

			foreach( $themes as $theme ) {
				$fields[] = "{$this->alias}.{$theme}";

				// INFO: on a les combinaisons en double
				foreach( $themes as $autreTheme ) {
					if( $theme != $autreTheme ) {
						$conditions['OR'][] = array(
							"{$this->alias}.{$theme} <>" => 'nontraite',
							"{$this->alias}.{$autreTheme} <>" => 'nontraite',
							"{$this->alias}.{$theme} <> {$this->alias}.{$autreTheme}"
						);
					}
				}
			}

			return $this->find(
				'all',
				array(
					'fields' => $fields,
					'conditions' => $conditions,
					'contain' => false,
				)
			);
		}
	}
?>