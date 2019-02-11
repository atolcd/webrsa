<?php
	/**
	 * Code source de la classe IntegrationfichiersapreController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe IntegrationfichiersapreController ...
	 *
	 * @package app.Controller
	 */
	class IntegrationfichiersapreController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Integrationfichiersapre';

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
			'download' => 'read',
			'index' => 'read',
		);

		public function index() {
			$this->paginate = array(
				'Integrationfichierapre' => array(
					'fields' => array(
						'id',
						'date_integration',
						'nbr_atraiter',
						'nbr_succes',
						'nbr_erreurs',
						'fichier_in'
					),
					'order' => array( 'Integrationfichierapre.date_integration DESC' )
				)
			);

			$integrationfichiersapre = $this->paginate( 'Integrationfichierapre' );

			$this->set( compact( 'integrationfichiersapre' ) );
		}

		/**
		*
		*/

		public function download( $id = null ) {
			$qd_integrationfichierapre = array(
				'conditions' => array(
					'Integrationfichierapre.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$integrationfichierapre= $this->Integrationfichierapre->find( 'first', $qd_integrationfichierapre );

			$this->assert( !empty( $integrationfichierapre ), 'invalidParameter' );

			Configure::write( 'debug', 0 );
			header('Content-Type: application/octet-stream; charset='.Configure::read( 'App.encoding' ) );
			header('Content-Disposition: attachment; filename="rejets-'.Set::classicExtract( $integrationfichierapre, 'Integrationfichierapre.fichier_in' ).'"');
			echo Set::classicExtract( $integrationfichierapre, 'Integrationfichierapre.erreurs' );
			die();
		}
	}
?>