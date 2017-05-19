<?php
	/**
	 * Code source de la classe ParametragesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ParametragesController ...
	 *
	 * @package app.Controller
	 */
	class ParametragesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Parametrages';

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
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dossier',
			'Structurereferente',
			'Zonegeographique',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'modulefse93' => 'Parametrages:index',
			'view' => 'Parametrages:index',
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
			'cataloguesromesv3' => 'read',
			'fichesprescriptions93' => 'read',
			'index' => 'read',
			'modulefse93' => 'read',
			'view' => 'read',
		);

		/**
		 * Premier niveau du paramétrage, suivant le département.
		 */
		public function index() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			$links = array(
				'Actions d\'insertion' => ( $departement !== 66 )
					? array( 'controller' => 'actions', 'action' => 'index' )
					: null
				,
				'APREs' => array( 'controller' => 'apres'.Configure::read( 'Apre.suffixe' ), 'action' => 'indexparams' ),
				'Cantons' => array( 'controller' => 'cantons', 'action' => 'index' ),
				'CERs' => ( $departement === 93 )
					? array( 'controller' => 'cers93', 'action' => 'indexparams' )
					: null
				,
				__d( 'parametrages', '/Parametrages/cataloguesromesv3/:heading' ) => ( Configure::read( 'Romev3.enabled' ) )
					? array( 'controller' => 'parametrages', 'action' => 'cataloguesromesv3' )
					: null
				,
				__d( 'communautessrs', '/Communautessrs/index/:heading' ) => ( $departement === 93 )
					? array( 'controller' => 'communautessrs', 'action' => 'index' )
					: null
				,
				'CUIs' => ( $departement === 66 )
					? array( 'controller' => 'cuis66', 'action' => 'indexparams' )
					: null
				,
				'DSPs' => array( 'controller' => 'gestionsdsps', 'action' => 'index' ),
				'Editeur de requêtes' => ( Configure::read( 'Requestmanager.enabled' ) )
					? array( 'controller' => 'requestsmanager', 'action' => 'indexparams' )
					: null
				,
				'Équipes pluridisciplinaires' => array( 'controller' => 'gestionseps', 'action' => 'index' ),
				'Fiche de liaisons' => ( $departement === 66 )
					? array( 'controller' => 'fichedeliaisons', 'action' => 'indexparams' )
					: null
				,
				'Fiches de prescription' => ( $departement === 93 )
					? array( 'controller' => 'parametrages', 'action' => 'fichesprescriptions93' )
					: null
				,
				'Liste des sanctions' => ( $departement === 58 )
					? array( 'controller' => 'listesanctionseps58', 'action' => 'index' )
					: null
				,
				'Module FSE' => ( $departement === 93 )
					? array( 'controller' => 'parametrages', 'action' => 'modulefse93' )
					: null
				,
				'Motifs de non validation de CER' => ( $departement === 66 )
					? array( 'controller' => 'motifscersnonvalids66', 'action' => 'index' )
					: null
				,
				'Objets de l\'entretien' => array( 'controller' => 'objetsentretien', 'action' => 'index' ),
				'PDOs' => array( 'controller' => 'pdos', 'action' => 'index' ),
				'Permanences' => array( 'controller' => 'permanences', 'action' => 'index' ),
				'Référents pour les structures' => array( 'controller' => 'referents', 'action' => 'index' ),
				'Rendez-vous' => array( 'controller' => 'gestionsrdvs', 'action' => 'index' ),
				'Services' => ( $departement === 66 )
					? array( 'controller' => 'services66', 'action' => 'index' )
					: null
				,
				'Services instructeurs' => array( 'controller' => 'servicesinstructeurs', 'action' => 'index' ),
				'Sites d\'actions médico-sociale COVs' => ( $departement === 58 )
					? array( 'controller' => 'sitescovs58', 'action' => 'index' )
					: null
				,
				'Structures référentes' => array( 'controller' => 'structuresreferentes', 'action' => 'index' ),
				'Tableau de bord' => Configure::read('Module.Dashboards.enabled') 
					? array('controller' => 'dashboards', 'action' => 'indexparams')
					: array()
				,
				'Tags' => ( $departement === 66 )
					? array( 'controller' => 'tags', 'action' => 'indexparams' )
					: null
				,
				'Types d\'actions' => ( $departement !== 66 )
					? array( 'controller' => 'typesactions', 'action' => 'index' )
					: null
				,
				'Types d\'orientations' => array( 'controller' => 'typesorients', 'action' => 'index' ),
				'Zones géographiques' => array( 'controller' => 'zonesgeographiques', 'action' => 'index' ),
			);

			$links = Hash::filter( $links );
			$this->set( compact( 'links' ) );
		}

		public function view( $param = null ) {
			$zone = $this->Zonegeographique->find(
				'first',
				array(
					'conditions' => array(
					)
				)
			);
			$this->set('zone', $zone);
		}

		public function modulefse93() {
			$links = array(
				__d( 'sortiesaccompagnementsd2pdvs93', '/Sortiesaccompagnementsd2pdvs93/index/:heading' ) => array( 'controller' => 'sortiesaccompagnementsd2pdvs93', 'action' => 'index' ),
			);

			$this->set( compact( 'links' ) );
		}

		public function fichesprescriptions93() {
			$links = array(
				__d( 'cataloguespdisfps93', '/Cataloguespdisfps93/search/:heading' ) => array( 'controller' => 'cataloguespdisfps93', 'action' => 'search' ),
			);
			foreach( ClassRegistry::init( 'Cataloguepdifp93' )->modelesParametrages as $modelName ) {
				$links[__d( 'cataloguespdisfps93', "/Cataloguespdisfps93/index/{$modelName}/:heading" )] = array( 'controller' => 'cataloguespdisfps93', 'action' => 'index', $modelName );
			}

			$this->set( compact( 'links' ) );
			$this->render( 'modulefse93' ); // TODO: une vue plus générique
		}

		public function cataloguesromesv3() {
			$links = array(
				// TODO
				//__d( 'cataloguesromesv3', '/Cataloguesromesv3/search/:heading' ) => array( 'controller' => 'cataloguesromesv3', 'action' => 'search' ),
			);
			$Catalogueromev3 = ClassRegistry::init( 'Catalogueromev3' );
			foreach( $Catalogueromev3->modelesParametrages as $modelName ) {
				$tableName = Inflector::tableize( $modelName );
				$links[__d( 'cataloguesromesv3', "/Cataloguesromesv3/{$tableName}/:heading" )] = array( 'controller' => 'cataloguesromesv3', 'action' => $tableName );
			}

			$this->set( compact( 'links' ) );
			$this->render( 'modulefse93' ); // TODO: une vue plus générique
		}
	}

?>