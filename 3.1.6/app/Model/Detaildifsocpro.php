<?php	
	/**
	 * Code source de la classe Detaildifsocpro.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Detaildifsocpro ...
	 *
	 * @package app.Model
	 */
	class Detaildifsocpro extends AppModel
	{
		public $name = 'Detaildifsocpro';

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'difsocpro' => array(
						'type' => 'difsocpro', 'domain' => 'dsp'
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