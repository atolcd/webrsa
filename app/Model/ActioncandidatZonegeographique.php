<?php	
	/**
	 * Code source de la classe ActioncandidatZonegeographique.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ActioncandidatZonegeographique ...
	 *
	 * @package app.Model
	 */
	class ActioncandidatZonegeographique extends AppModel
	{
		public $name = 'ActioncandidatZonegeographique';

		public $actsAs = array(
			'Autovalidate2',
			'Formattable',
			'ValidateTranslate'
		);

		public $belongsTo = array(
			'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'foreignKey' => 'actioncandidat_id',
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
