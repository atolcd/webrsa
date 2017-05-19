<?php	
	/**
	 * Code source de la classe Liberalite.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Liberalite ...
	 *
	 * @package app.Model
	 */
	class Liberalite extends AppModel
	{
		public $name = 'Liberalite';

		public $validate = array(
			'avispcgpersonne_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
		);

		public $belongsTo = array(
			'Avispcgpersonne' => array(
				'className' => 'Avispcgpersonne',
				'foreignKey' => 'avispcgpersonne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>