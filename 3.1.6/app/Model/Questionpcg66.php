<?php	
	/**
	 * Code source de la classe Questionpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Questionpcg66 ...
	 *
	 * @package app.Model
	 */
	class Questionpcg66 extends AppModel
	{
		public $name = 'Questionpcg66';

		public $recursive = -1;

		public $actsAs = array(
			'Autovalidate2',
			'Enumerable' => array(
				'fields' => array(
					'defautinsertion',
					'recidive',
					'phase'
				)
			)
		);

		public $belongsTo = array(
			'Decisionpcg66' => array(
				'className' => 'Decisionpcg66',
				'foreignKey' => 'decisionpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Compofoyerpcg66' => array(
				'className' => 'Compofoyerpcg66',
				'foreignKey' => 'compofoyerpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>