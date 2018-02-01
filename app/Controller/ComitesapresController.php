<?php
	/**
	 * Code source de la classe ComitesapresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe ComitesapresController ...
	 *
	 * @package app.Controller
	 */
	class ComitesapresController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Comitesapres';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
					'liste',
				),
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Locale',
			'Xform',
			'Xhtml',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Apre',
			'Comiteapre',
			'Dossier',
			'Option',
			'Participantcomite',
			'Personne',
			'Referent',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Comitesapres:edit',
			'view' => 'Comitesapres:index',
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
			'add' => 'create',
			'edit' => 'update',
			'exportcsv' => 'read',
			'index' => 'read',
			'liste' => 'read',
			'rapport' => 'read',
			'view' => 'read',
		);

		/**		 * *******************************************************************
		 *
		 * ** ****************************************************************** */
		protected function _setOptions() {
			$this->set( 'referent', $this->Referent->find( 'list' ) );
			$options = Hash::merge(
				(array)Hash::get( $this->Comiteapre->ApreComiteapre->enums(), 'ApreComiteapre' ),
				(array)Hash::get( $this->Comiteapre->ComiteapreParticipantcomite->enums(), 'ComiteapreParticipantcomite' )
			);
			$this->set( 'options', $options );
		}

		/**		 * *******************************************************************
		 *
		 * ** ****************************************************************** */
		public function index() {
			$this->_index( 'Comiteapre::index' );
		}

		//---------------------------------------------------------------------

		public function liste() {
			$this->_index( 'Comiteapre::liste' );
		}

		/**		 * *******************************************************************
		 *
		 * ** ****************************************************************** */
		protected function _index( $display = null ) {
			$this->Comiteapre->Apre->deepAfterFind = false;
			if( !empty( $this->request->data ) ) {
				$this->Dossier->begin(); // Pour les jetons
				$comitesapres = $this->Comiteapre->search( $display, $this->request->data );
				$comitesapres['limit'] = Configure::read( 'ResultatsParPage.nombre_par_defaut' );
				if (isset ($this->request->data['Search']['limit'])) {
					$comitesapres['limit'] = $this->request->data['Search']['limit'];
				}
				$comitesapres['recursive'] = 1;
				$this->paginate = $comitesapres;
				$comitesapres = $this->paginate( $this->Comiteapre );
				$this->Dossier->commit();
				$this->_setOptions();
				$this->set( 'comitesapres', $comitesapres );
			}

			switch( $display ) {
				case 'Comiteapre::index':
					$this->set( 'pageTitle', 'Recherche de comités' );
					$this->render( 'index' );
					break;
				case 'Comiteapre::liste':
					$this->set( 'pageTitle', 'Liste des comités' );
					$this->render( 'liste' );
					break;
			}
		}

		/**		 * *************************************************************************************
		 *   Affichage du Comité après sa création permettant ajout des APREs et des Participants
		 * ** ************************************************************************************ */
		public function view( $comiteapre_id = null ) {
			$this->Comiteapre->Apre->deepAfterFind = false;

			$containApre = array( );
			foreach( $this->Apre->WebrsaApre->aidesApre as $modelAideAlias ) {
				$modelPieceAlias = 'Piece'.Inflector::underscore( $modelAideAlias );
				$containApre[$modelAideAlias] = array( $modelPieceAlias );
			}

			$contain = array(
				'Apre' => array_merge(
						$containApre, array(
					'Personne' => array(
						'Foyer' => array(
							'Adressefoyer' => array(
								'Adresse'
							)
						)
					)
						)
				),
				'Participantcomite'
			);

			$comiteapre = $this->Comiteapre->find(
					'first', array(
				'conditions' => array( 'Comiteapre.id' => $comiteapre_id ),
				'contain' => $contain
					)
			);
			$this->assert( !empty( $comiteapre ), 'invalidParameter' );

			$this->set( 'comiteapre', $comiteapre );
			$this->_setOptions();
			$participants = $this->Participantcomite->find( 'list' );
			$this->set( 'participants', $participants );
			$this->set( 'listeAidesApre', $this->Apre->WebrsaApre->aidesApre );
		}

		/**		 * *********************************************************************************************
		 *   Affichage du rapport suite au Comité ( présence / absence des participants + décision APREs)
		 * ** ********************************************************************************************* */
		public function rapport( $comiteapre_id = null ) {
			$this->assert( valid_int( $comiteapre_id ), 'invalidParameter' );

			$this->Comiteapre->Apre->deepAfterFind = false;
			$comiteapre = $this->Comiteapre->find(
					'first', array(
				'conditions' => array( 'Comiteapre.id' => $comiteapre_id ),
				'contain' => array(
					'Apre' => array(
						'Personne' => array(
							'Foyer' => array(
								'Adressefoyer' => array(
									'Adresse'
								)
							)
						)
					),
					'Participantcomite'
				)
					)
			);

			$this->set( 'comiteapre', $comiteapre );
			$this->_setOptions();
			$participants = $this->Participantcomite->find( 'list' );
			$this->set( 'participants', $participants );
		}

		/**		 * *******************************************************************
		 *
		 * ** ****************************************************************** */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**		 * *******************************************************************
		 *
		 * ** ****************************************************************** */
		protected function _add_edit( $id = null ) {
			$this->Comiteapre->begin();

			$isRapport = Set::classicExtract( $this->request->params, 'named.rapport' );

			/// Récupération des id afférents
			if( $this->action == 'add' ) {
				$this->assert( empty( $id ), 'invalidParameter' );
			}
			else if( $this->action == 'edit' ) {
				$comiteapre_id = $id;
				$qd_comiteapre = array(
					'conditions' => array(
						'Comiteapre.id' => $comiteapre_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => 1
				);
				$comiteapre = $this->Comiteapre->find( 'first', $qd_comiteapre );
				$this->assert( !empty( $comiteapre ), 'invalidParameter' );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Comiteapre->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
					$saved = $this->Comiteapre->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) );

					if( $saved ) {
						$this->Comiteapre->commit();
						$this->Flash->success( __( 'Save->success' ) );

						if( !$isRapport ) {
							$this->redirect( array( 'controller' => 'comitesapres', 'action' => 'view', $this->Comiteapre->id ) );
						}
						else if( $isRapport ) {
							$this->redirect( array( 'controller' => 'comitesapres', 'action' => 'rapport', $this->Comiteapre->id ) );
						}
					}
					else {
						$this->Comiteapre->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
			}
			else {
				if( $this->action == 'edit' ) {
					$this->request->data = $comiteapre;
				}
			}
			$this->Comiteapre->commit();
			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		/**
		 *
		 */
		public function exportcsv() {
			$querydata = $this->Comiteapre->search( 'Comiteapre::index', Hash::expand( $this->request->params['named'], '__' ) );
			unset( $querydata['limit'] );
			$comitesapres = $this->Comiteapre->find( 'all', $querydata );

			$this->_setOptions();
			$this->layout = '';
			$this->set( compact( 'comitesapres' ) );
		}

	}
?>