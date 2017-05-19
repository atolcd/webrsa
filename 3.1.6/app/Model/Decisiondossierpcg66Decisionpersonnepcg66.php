<?php	
	/**
	 * Code source de la classe Decisiondossierpcg66Decisionpersonnepcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Decisiondossierpcg66Decisionpersonnepcg66 ...
	 *
	 * @package app.Model
	 */
	class Decisiondossierpcg66Decisionpersonnepcg66 extends AppModel
	{
		public $name = 'Decisiondossierpcg66Decisionpersonnepcg66';

		public $belongsTo = array(
			'Decisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'foreignKey' => 'decisiondossierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Decisionpersonnepcg66' => array(
				'className' => 'Decisionpersonnepcg66',
				'foreignKey' => 'decisionpersonnepcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>