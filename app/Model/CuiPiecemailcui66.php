<?php	
	/**
	 * Code source de la classe CuiPiecemailcui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CuiPiecemailcui66 ...
	 *
	 * @package app.Model
	 */
	class CuiPiecemailcui66 extends AppModel
	{
		public $name = 'CuiPiecemailcui66';

		public $belongsTo = array(
			'Cui' => array(
				'className' => 'Cui',
				'foreignKey' => 'cui_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Piecemailcui66' => array(
				'className' => 'Piecemailcui66',
				'foreignKey' => 'piecemailcui66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>