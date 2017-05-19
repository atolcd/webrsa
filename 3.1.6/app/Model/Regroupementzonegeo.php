<?php	
	/**
	 * Code source de la classe Regroupementzonegeo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Regroupementzonegeo ...
	 *
	 * @package app.Model
	 */
	class Regroupementzonegeo extends AppModel
	{
		public $name = 'Regroupementzonegeo';

		public $hasAndBelongsToMany = array(
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'joinTable' => 'regroupementszonesgeo_zonesgeographiques',
				'foreignKey' => 'regroupementzonegeo_id',
				'associationForeignKey' => 'zonegeographique_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'RegroupementzonegeoZonegeographique'
			)
		);

		public $validate = array(
			'lib_rgpt' => array(
				array(
					'rule' => 'isUnique',
					'message' => 'Cette valeur est déjà utilisée'
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			)
		);
	}
?>
