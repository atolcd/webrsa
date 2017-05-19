<?php	
	/**
	 * Code source de la classe Amenaglogt.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Amenaglogt ...
	 *
	 * @package app.Model
	 */
	class Amenaglogt extends AppModel
	{
		public $name = 'Amenaglogt';

		public $actsAs = array(
			'Aideapre',
			'Enumerable' => array(
				'fields' => array(
					'typeaidelogement' => array( 'type' => 'typeaidelogement', 'domain' => 'apre' ),
				)
			),
			'Frenchfloat' => array( 'fields' => array( 'montantaide' ) )
		);

		public $validate = array(
			'apre_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'typeaidelogement' => array(
				'rule' => 'notEmpty',
				'message' => 'Champ obligatoire'
			),
			'montantaide' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				array(
					'rule' => 'numeric',
					'message' => 'Veuillez entrer une valeur numérique.',
					'allowEmpty' => true
				)
			)
		);

		public $belongsTo = array(
			'Apre' => array(
				'className' => 'Apre',
				'foreignKey' => 'apre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasAndBelongsToMany = array(
			'Pieceamenaglogt' => array(
				'className' => 'Pieceamenaglogt',
				'joinTable' => 'amenagslogts_piecesamenagslogts',
				'foreignKey' => 'amenaglogt_id',
				'associationForeignKey' => 'pieceamenaglogt_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'AmenaglogtPieceamenaglogt'
			)
		);
	}
?>