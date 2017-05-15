<?php	
	/**
	 * Code source de la classe Detailprojpro.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Detailprojpro ...
	 *
	 * @package app.Model
	 */
	class Detailprojpro extends AppModel
	{
		public $name = 'Detailprojpro';

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'projpro' => array(
						'type' => 'projpro', 'domain' => 'dsp'
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