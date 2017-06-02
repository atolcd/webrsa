<?php	
	/**
	 * Code source de la classe Detailconfort.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Detailconfort ...
	 *
	 * @package app.Model
	 */
	class Detailconfort extends AppModel
	{
		public $name = 'Detailconfort';

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'confort' => array(
						'type' => 'confort', 'domain' => 'dsp'
					),
				)
			)
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