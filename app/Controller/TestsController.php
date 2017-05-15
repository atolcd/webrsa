<?php	
	/**
	 * Code source de la classe TestsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe TestsController ...
	 *
	 * @package app.Controller
	 */
	class TestsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Tests';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'index',
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
		);

		public function index() {
			$themes = array(
				'Epinay s/ Seine' => 20,
				'Pierrefitte' => 12,
				'Villetaneuse' => 15,
				'Saint Denis' => 10,
				'Ile-St-Denis' => 15,
				'Saint-Ouen' => 8
			);
			$this->set( compact( 'themes' ) );
		}
	}
?>