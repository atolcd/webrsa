<?php	
	/**
	 * Code source de la classe RessourceRessourcemensuelle.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe RessourceRessourcemensuelle ...
	 *
	 * @package app.Model
	 */
	class RessourceRessourcemensuelle extends AppModel {
		public $name = 'RessourceRessourcemensuelle';
		public $validate = array(
			'ressourcemensuelle_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				)
			),
			'ressource_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				)
			)
		);
		//The Associations below have been created with all possible keys, those that are not needed can be removed

		public $belongsTo = array(
			'Ressourcemensuelle' => array(
				'className' => 'Ressourcemensuelle',
				'foreignKey' => 'ressourcemensuelle_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Ressource' => array(
				'className' => 'Ressource',
				'foreignKey' => 'ressource_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>