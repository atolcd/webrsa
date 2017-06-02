<?php	
	/**
	 * Code source de la classe Decisiondossierpcg66Decisiontraitementpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Decisiondossierpcg66Decisiontraitementpcg66 ...
	 *
	 * @package app.Model
	 */
	class Decisiondossierpcg66Decisiontraitementpcg66 extends AppModel
	{
		public $name = 'Decisiondossierpcg66Decisiontraitementpcg66';

		public $belongsTo = array(
			'Decisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'foreignKey' => 'decisiondossierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Decisiontraitementpcg66' => array(
				'className' => 'Decisiontraitementpcg66',
				'foreignKey' => 'decisiontraitementpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>