<?php	
	/**
	 * Code source de la classe Detailnatmob.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Detailnatmob ...
	 *
	 * @package app.Model
	 */
	class Detailnatmob extends AppModel
	{
		public $name = 'Detailnatmob';

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
					'natmob' => array(
						'type' => 'natmob', 'domain' => 'dsp'
					),
				)
			),
		);
	}
?>