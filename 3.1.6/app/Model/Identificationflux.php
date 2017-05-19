<?php	
	/**
	 * Code source de la classe Identificationflux.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Identificationflux ...
	 *
	 * @package app.Model
	 */
	class Identificationflux extends AppModel
	{
		public $name = 'Identificationflux';

		public $hasMany = array(
			'Totalisationacompte' => array(
				'className' => 'Totalisationacompte',
				'foreignKey' => 'identificationflux_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Transmissionflux' => array(
				'className' => 'Transmissionflux',
				'foreignKey' => 'identificationflux_id',
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
		
		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 * 
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'applieme' => array('CRI', 'AGO', 'NRI', 'NRA', 'IOD', 'GEN', 'IAS', 'PER', '54'),
		);
	}
?>