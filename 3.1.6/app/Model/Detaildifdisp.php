<?php	
	/**
	 * Code source de la classe Detaildifdisp.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Detaildifdisp ...
	 *
	 * @package app.Model
	 */
	class Detaildifdisp extends AppModel
	{
		public $name = 'Detaildifdisp';

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
					'difdisp' => array(
						'type' => 'difdisp', 'domain' => 'dsp'
					),
				)
			),
		);
	}
?>