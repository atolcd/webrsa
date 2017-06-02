<?php
	/**
	 * Code source de la classe AllosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe AllosController permet des dialogues REST avec Allo.
	 *
	 * @package app.Controller
	 */
	class AllosController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Allos';

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
		public $uses = false;

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
			'version',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'version' => 'read',
		);

		/**
		 * Le nom exact de la référence client, tel que défini dans le CMS.
		 *
		 * @var array
		 */
		public $refsClients = array(
			58 => 'CG58_58194',
			66 => 'CG66_66136',
			93 => 'CG93_93008',
			976 => 'CG976_97611'
		);

		/**
		 * Retourne la base des informations concernant le produit, la version
		 * et le client utilisant l'application.
		 */
		public function version() {
			$json = array(
				'produit' => 'web-rsa',
				'version' => app_version(),
				'refClient' => Hash::get( $this->refsClients, Configure::read( 'Cg.departement' ) )
			);

			$this->set( compact( 'json' ) );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}
	}
?>
