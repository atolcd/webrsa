<?php	
	/**
	 * Code source de la classe Decdospcg66Orgdospcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Decdospcg66Orgdospcg66 ...
	 *
	 * @package app.Model
	 */
	class Decdospcg66Orgdospcg66 extends AppModel
	{
		public $name = 'Decdospcg66Orgdospcg66';

		public $belongsTo = array(
			'Decisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'foreignKey' => 'decisiondossierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Orgtransmisdossierpcg66' => array(
				'className' => 'Orgtransmisdossierpcg66',
				'foreignKey' => 'orgtransmisdossierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>