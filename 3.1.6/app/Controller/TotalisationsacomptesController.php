<?php	
	/**
	 * Code source de la classe TotalisationsacomptesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe TotalisationsacomptesController ...
	 *
	 * @package app.Controller
	 */
	class TotalisationsacomptesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Totalisationsacomptes';

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
			'Csv',
			'Locale',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Totalisationacompte',
			'Identificationflux',
			'Infofinanciere',
			'Option',
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
			'exportcsv' => 'read',
			'index' => 'read',
		);

		/** ********************************************************************
		*
		*** *******************************************************************/

		public function beforeFilter() {
			parent::beforeFilter();
			// Type_totalisation
			$this->set( 'type_totalisation', ClassRegistry::init('Totalisationacompte')->enum('type_totalisation') );
			$this->set( 'natpfcre', ClassRegistry::init('Infofinanciere')->enum('natpfcre'));
		}

		/** ********************************************************************
		*
		*** *******************************************************************/

		public function index() {
			if( !empty( $this->request->data ) ) {
				$params = $this->Totalisationacompte->search( $this->request->data );
				$totsacoms = $this->Totalisationacompte->find( 'all', $params );
				$this->set('totsacoms', $totsacoms );
			}
		}

		/** ********************************************************************
		*
		*** *******************************************************************/

		public function exportcsv() {
			$params = $this->Totalisationacompte->search( Hash::expand( $this->request->params['named'], '__' ) );
			$totsacoms = $this->Totalisationacompte->find( 'all', $params );

			$identsflux = $this->Identificationflux->find( 'all' );
			$this->set( 'identsflux', $identsflux );

			$this->layout = ''; // FIXME ?
			$this->set( compact( 'totsacoms' ) );
		}
	}

?>