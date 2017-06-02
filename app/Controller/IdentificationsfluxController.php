<?php	
	/**
	 * Code source de la classe IdentificationsfluxController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe IdentificationsfluxController ...
	 *
	 * @package app.Controller
	 */
	class IdentificationsfluxController  extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Identificationsflux';

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
			'Identificationflux',
			'Option',
			'Totalisationacompte',
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

		public function index( $id = null ) {
			// Vérification du format de la variable
			$this->assert( valid_int( $id ), 'error404' );

			// Recherche des adresses du foyer
			$identflux = $this->Identificationflux->find(
				'all',
				array(
					'conditions' => array( 'Identificationflux.id' => $id ),
					'recursive' => -1
				)
			);

			// Assignations à la vue
			$this->set( 'identflux', $identflux );
		}
	}
?>