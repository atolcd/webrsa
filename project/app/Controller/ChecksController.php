<?php
	/**
	 * Code source de la classe ChecksController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaCheckAccess', 'Utility' );
	App::uses( 'CakephpConfigurationParser', 'Configuration.Utility' );

	/**
	 * La classe ChecksController ...
	 *
	 * @package app.Controller
	 */
	class ChecksController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Checks';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Gedooo.Gedooo',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Appchecks.Checks',
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			)
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'WebrsaInstallCheck'
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array();

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array();

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
		);

		/**
		 * Vérification complète de l'application et envoi des résultats à la vue.
		 *
		 * @return void
		 * @access public
		 */
		public function index() {
			$this->Gedooo->makeTmpDir( Configure::read( 'Cohorte.dossierTmpPdfs' ) );

			$this->set( 'results', $this->WebrsaInstallCheck->all() );

			$this->set(
				'configurations',
				CakephpConfigurationParser::parseFile(
					APP.'Config'.DS.'webrsa.cg'.Configure::read('Cg.departement').'.inc')
			);
		}
	}
?>