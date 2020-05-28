<?php
	/**
	 * Code source de la classe Sitecov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Sitecov58 ...
	 *
	 * @package app.Model
	 */
	class Sitecov58 extends AppModel
	{
		public $name = 'Sitecov58';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $order = array( 'Sitecov58.name ASC' );

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $hasMany = array(
			'Cov58' => array(
				'className' => 'Cov58',
				'foreignKey' => 'sitecov58_id',
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

		public $hasAndBelongsToMany = array(
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'joinTable' => 'sitescovs58_zonesgeographiques',
				'foreignKey' => 'sitecov58_id',
				'associationForeignKey' => 'zonegeographique_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Sitecov58Zonegeographique'
			),
			'Canton' => array(
				'className' => 'Canton',
				'joinTable' => 'cantons_sitescovs58',
				'foreignKey' => 'sitecov58_id',
				'associationForeignKey' => 'canton_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'CantonSitecov58'
			)
		);

		/**
		*	FIXME: docs
		*/
		public function queryConditions( $sitecov58_id ) {
			$cantons = $this->Canton->find(
				'all',
				array(
					'conditions' => array(
						'Canton.id IN (SELECT canton_id FROM cantons_sitescovs58 WHERE sitecov58_id = '.$sitecov58_id.')'
					)
				)
			);

			$_conditions = $this->Canton->constructionConditionAdresses ($cantons);

			return array( 'or' => $_conditions );
		}

		/**
		*	FIXME: docs
		*/
		public function queryConditionsByZonesgeographiques ( $sitecov58_id ) {
			$sq = $this->Sitecov58Zonegeographique->sq (
				array (
					'alias' => 'sitescovs58_zonesgeographiques',
					'fields' => 'zonesgeographiques.codeinsee',
					'contain' => false,
					'joins' => array (
						array_words_replace (
							$this->Sitecov58Zonegeographique->join( 'Zonegeographique', array( 'type' => 'INNER' ) ),
							array (
								'Sitecov58Zonegeographique' => 'sitescovs58_zonesgeographiques',
								'Zonegeographique' => 'zonesgeographiques'
							)
						)
					),
					'conditions' => array (
						'sitescovs58_zonesgeographiques.sitecov58_id' => $sitecov58_id
					)
				)
			);

			return "Adresse.numcom IN ( {$sq} )";
		}

	}
?>