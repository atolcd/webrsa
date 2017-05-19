<?php	
	/**
	 * Code source de la classe Detaildiflog.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Detaildiflog ...
	 *
	 * @package app.Model
	 */
	class Detaildiflog extends AppModel
	{
		public $name = 'Detaildiflog';

		public $belongsTo = array(
			'Dsp' => array(
				'className' => 'Dsp',
				'foreignKey' => 'dsp_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'diflog' => array(
						'type' => 'diflog', 'domain' => 'dsp'
					),
				)
			),
		);
	}
?>