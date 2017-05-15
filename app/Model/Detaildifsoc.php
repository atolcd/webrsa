<?php	
	/**
	 * Code source de la classe Detaildifsoc.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Detaildifsoc ...
	 *
	 * @package app.Model
	 */
	class Detaildifsoc extends AppModel
	{
		public $name = 'Detaildifsoc';

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'difsoc' => array(
						'type' => 'difsoc', 'domain' => 'dsp'
					),
				)
			),
		);

		public $belongsTo = array(
			'Dsp' => array(
				'className' => 'Dsp',
				'foreignKey' => 'dsp_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);
	}
?>