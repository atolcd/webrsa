<?php	
	/**
	 * Code source de la classe Sitecov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Sitecov58 ...
	 *
	 * @package app.Model
	 */
	class Sitecov58 extends AppModel
	{
		public $name = 'Sitecov58';

		public $order = array( 'Sitecov58.name ASC' );

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate'
		);

		public $hasMany = array(
			'Cov58' => array(
				'className' => 'Cov58',
				'foreignKey' => 'sitecov58_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);
		
		public $hasAndBelongsToMany = array(
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'joinTable' => 'sitescovs58_zonesgeographiques',
				'foreignKey' => 'sitecov58_id',
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
				'with' => 'Sitecov58Zonegeographique'
			)
		);
	}
?>