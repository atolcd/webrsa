<?php
	/**
	 * Code source de la classe Dureeemploi.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Dureeemploi ...
	 *
	 * @package app.Model
	 */
	class Dureeemploi extends AppModel
	{
		/**
		 * Associations "Has one".
		 *
		 * @var array
		 */
		public $hasOne = array(
			'Questionnaireb7pdv93' => array(
				'className' => 'Questionnaireb7pdv93',
				'foreignKey' => 'emptrouvromev3_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);

		/**
		 * Retourne les options à utiliser dans le moteur de recherche, le
		 * formulaire d'ajout / de modification, etc.. suivant le CG connecté.
		 *
		 * @return array
		 */
		public function options() {
			return array (get_class($this) => $this->find ('list', array ('order' => array ('name ASC'))));
		}
	}
?>