<?php
	/**
	 * Code source de la classe Propodecisioncer66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Propodecisioncer66 ...
	 *
	 * @package app.Model
	 */
	class Propodecisioncer66 extends AppModel
	{
		public $name = 'Propodecisioncer66';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
            'motifficheliaison' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'isvalidcer', true, array( 'N' ) ),
					'message' => 'Champ obligatoire',
				),
			),
            'motifnotifnonvalid' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'isvalidcer', true, array( 'N' ) ),
					'message' => 'Champ obligatoire',
				),
			),
		);

		public $belongsTo = array(
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'contratinsertion_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasAndBelongsToMany = array(
			'Motifcernonvalid66' => array(
				'className' => 'Motifcernonvalid66',
				'joinTable' => 'motifscersnonvalids66_proposdecisioncers66',
				'foreignKey' => 'propodecisioncer66_id',
				'associationForeignKey' => 'motifcernonvalid66_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Motifcernonvalid66Propodecisioncer66'
			)
		);


		/**
		 * BeforeSave
		 */
		public function beforeSave( $options = array( ) ) {
			$return = parent::beforeSave( $options );

			//  MAJ de la position du cER
			if( !empty( $this->data ) ) {

				$contratinsertion_id = $this->data['Propodecisioncer66']['contratinsertion_id'];
				$contratinsertion = $this->Contratinsertion->find(
					'first',
					array(
						'conditions' => array(
							'Contratinsertion.id' => $contratinsertion_id
						),
						'contain' => false,
						'recursive' => -1
					)
				);

				$return = $return && $this->Contratinsertion->WebrsaContratinsertion->updatePositionsCersById( $contratinsertion_id );
			}
			return $return;
		}
	}
?>