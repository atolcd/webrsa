<?php	
	/**
	 * Code source de la classe EpZonegeographique.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe EpZonegeographique ...
	 *
	 * @package app.Model
	 */
	class EpZonegeographique extends AppModel
	{
		public $name = 'EpZonegeographique';

		public $actsAs = array(
			'Autovalidate2',
			'Formattable',
			'ValidateTranslate'
		);

		public $belongsTo = array(
			'Ep' => array(
				'className' => 'Ep',
				'foreignKey' => 'ep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'foreignKey' => 'zonegeographique_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>
