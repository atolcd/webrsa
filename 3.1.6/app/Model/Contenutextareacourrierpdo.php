<?php
	/**
	 * Code source de la classe Contenutextareacourrierpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Contenutextareacourrierpdo ...
	 *
	 * @package app.Model
	 */
	class Contenutextareacourrierpdo extends AppModel
	{
		public $name = 'Contenutextareacourrierpdo';

		public $actsAs = array(
			'Autovalidate2'
		);

		public $belongsTo = array(
			'CourrierpdoTraitementpdo' => array(
				'className' => 'CourrierpdoTraitementpdo',
				'foreignKey' => 'courrierpdo_traitementpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Textareacourrierpdo' => array(
				'className' => 'Textareacourrierpdo',
				'foreignKey' => 'textareacourrierpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

	}
?>