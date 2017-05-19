<?php	
	/**
	 * Code source de la classe ActioncandidatMotifsortie.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ActioncandidatMotifsortie ...
	 *
	 * @package app.Model
	 */
	class ActioncandidatMotifsortie extends AppModel
	{
		public $name = 'ActioncandidatMotifsortie';
        
        public $actsAs = array(
            'Autovalidate2',
            'Formattable'
        );

		public $validate = array(
			'actioncandidat_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'motifsortie_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			)
		);

		public $belongsTo = array(
			'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'foreignKey' => 'actioncandidat_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Motifsortie' => array(
				'className' => 'Motifsortie',
				'foreignKey' => 'motifsortie_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>