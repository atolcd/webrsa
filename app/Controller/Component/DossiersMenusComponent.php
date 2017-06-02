<?php
	/**
	 * Code source de la classe DossiersMenusComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * Classe DossiersMenusComponent.
	 *
	 * @package app.Controller.Component
	 */
	class DossiersMenusComponent extends Component
	{
		/**
		 * Contrôleur utilisant ce component.
		 *
		 * @var Controller
		 */
		public $Controller = null;

		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array( );

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array( 'Session' );

		/**
		 * Appelée avant Controller::beforeFilter().
		 *
		 * @param Controller $controller Controller with components to initialize
		 * @return void
		 */
		public function initialize( Controller $controller ) {
			parent::initialize( $controller );
			$this->Controller = $controller;
		}

		/**
		 *
		 * @param array $params
		 * @return array
		 * @throws error500Exception
		 */
		public function getDossierMenu( $params ) {
			// Récupération du menu du dossier
			$dossierMenu = ClassRegistry::init( 'Dossier' )->menu(
				$params,
				$this->Controller->Jetons2->qdLockParts()
			);

			if( empty( $dossierMenu ) ) {
				throw new error500Exception( null );
			}

			return $dossierMenu;
		}

		/**
		 * En fonction des CG lorsqu'on tape dans l'URL
		 *
		 * @param mixed $params
		 * @return array
		 */
		public function getAndCheckDossierMenu( $params ) {
			$dossierMenu = $this->getDossierMenu( $params );

			$this->_checkDossierMenu( $dossierMenu );

			return $dossierMenu;
		}

		/**
		 *
		 * @param array $data
		 * @throws Error403Exception
		 */
		protected function _checkDossierMenu( $dossierData ) {
			if( !WebrsaPermissions::checkDossier( $this->Controller->name, $this->Controller->action, $dossierData ) ) {
				throw new Error403Exception( null );
			}
		}

		/**
		 *
		 * @param array $data
		 * @throws Error403Exception
		 */
		public function checkDossierMenu( $params ) {
			$dossierMenu = $this->getDossierMenu( $params );

			$this->_checkDossierMenu( $dossierMenu );
		}
	}
?>