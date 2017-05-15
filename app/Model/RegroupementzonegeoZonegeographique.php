<?php	
	/**
	 * Code source de la classe RegroupementzonegeoZonegeographique.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe RegroupementzonegeoZonegeographique ...
	 *
	 * @package app.Model
	 */
	class RegroupementzonegeoZonegeographique extends AppModel {

		public $name = 'RegroupementzonegeoZonegeographique';

		//The Associations below have been created with all possible keys, those that are not needed can be removed
		public $belongsTo = array(
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'foreignKey' => 'zonegeographique_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Regroupementzonegeo' => array(
				'className' => 'Regroupementzonegeo',
				'foreignKey' => 'regroupementzonegeo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

	}
?>
