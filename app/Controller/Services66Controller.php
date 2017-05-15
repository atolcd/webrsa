<?php
	/**
	 * Code source de la classe Services66ControllerController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AbstractParametragesController', 'Controller');
	App::import( 'Behaviors', 'Occurences' );

	/**
	 * La classe Services66ControllerController ...
	 *
	 * @package app.Controller
	 */
	class Services66Controller extends AbstractParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Services66';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Service66'
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 * 
		 * @var array
		 */
		public $aucunDroit = array(
			
		);
		
		/**
		 * Formulaire de modification
		 */
		protected function _add_edit($id = null) {
			$this->set('options', $this->Service66->enums());
			
			return parent::_add_edit($id);
		}
	}