<?php	
	/**
	 * Code source de la classe Detailmoytrans.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Detailmoytrans ...
	 *
	 * @package app.Model
	 */
	class Detailmoytrans extends AppModel
	{
		public $name = 'Detailmoytrans';

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
					'moytrans' => array(
						'type' => 'moytrans', 'domain' => 'dsp'
					),
				)
			),
		);
	}
?>