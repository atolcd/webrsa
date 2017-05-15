<?php	
	/**
	 * Code source de la classe Suspensionversement.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Suspensionversement ...
	 *
	 * @package app.Model
	 */
	class Suspensionversement extends AppModel
	{
		public $name = 'Suspensionversement';

		public $validate = array(
			'situationdossierrsa_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'motisusversrsa' => array(
				'inList' => array(
					'rule' => array( 'inList', array('01','02','03','04','05','06','09','19','31','34','35','36','44','70','78','84','85','97','AB','CV','CG','CZ','DA','DB','DC') ),
				)
			)
		);

		public $belongsTo = array(
			'Situationdossierrsa' => array(
				'className' => 'Situationdossierrsa',
				'foreignKey' => 'situationdossierrsa_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>