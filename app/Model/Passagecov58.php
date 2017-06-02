<?php
	/**
	 * Code source de la classe Passagecov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Passagecov58 ...
	 *
	 * @package app.Model
	 */
	class Passagecov58 extends AppModel
	{
		/**
		*
		*/
		public $virtualFields = array(
			'chosen' => array(
				'type'      => 'boolean',
				'postgres'  => '(CASE WHEN "%s"."id" IS NOT NULL THEN true ELSE false END )'
			),
		);

		/**
		*
		*/

		public $actsAs = array(
			'Allocatairelie' => array(
				'joins' => array( 'Dossiercov58' )
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		*
		*/

		public $belongsTo = array(
			'Cov58' => array(
				'className' => 'Cov58',
				'foreignKey' => 'cov58_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Dossiercov58' => array(
				'className' => 'Dossiercov58',
				'foreignKey' => 'dossiercov58_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Decisionnonorientationprocov58' => array(
				'className' => 'Decisionnonorientationprocov58',
				'foreignKey' => 'passagecov58_id',
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
			'Decisionpropoorientsocialecov58' => array(
				'className' => 'Decisionpropoorientsocialecov58',
				'foreignKey' => 'passagecov58_id',
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
			'Decisionpropoorientationcov58' => array(
				'className' => 'Decisionpropoorientationcov58',
				'foreignKey' => 'passagecov58_id',
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
			'Decisionpropocontratinsertioncov58' => array(
				'className' => 'Decisionpropocontratinsertioncov58',
				'foreignKey' => 'passagecov58_id',
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
			'Decisionpropononorientationprocov58' => array(
				'className' => 'Decisionpropononorientationprocov58',
				'foreignKey' => 'passagecov58_id',
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
			'Decisionregressionorientationcov58' => array(
				'className' => 'Decisionregressionorientationcov58',
				'foreignKey' => 'passagecov58_id',
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
		);

		/**
		 * Sous-requête permettant d'obtenir l'id du dernier passage en COV (par-rapport à la date de la
		 * COV) d'un dossier COV.
		 *
		 * @param string $dossiercov58Id Le champ de la requête principale correspondant
		 *	à la clé primaire de la table dossierscovs58
		 * @return string
		 */
		public function sqDernier( $dossiercov58Id = 'Dossiercov58.id' ) {
			$passageAlias = Inflector::tableize( $this->alias );
			$covAlias = Inflector::tableize( $this->Cov58->alias );

			return $this->sq(
				array(
					'fields' => array(
						"{$passageAlias}.id"
					),
					'alias' => $passageAlias,
					'conditions' => array(
						"{$passageAlias}.dossiercov58_id = {$dossiercov58Id}"
					),
					'joins' => array(
						array_words_replace(
							$this->join( 'Cov58', array( 'type' => 'INNER' ) ),
							array(
								$this->alias => $passageAlias,
								$this->Cov58->alias => $covAlias
							)
						)
					),
					'order' => array( "{$covAlias}.datecommission DESC" ),
					'limit' => 1
				)
			);
		}
	}
?>