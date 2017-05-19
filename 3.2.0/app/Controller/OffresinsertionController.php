<?php
	/**
	 * Code source de la classe OffresinsertionController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe OffresinsertionController ...
	 *
	 * @package app.Controller
	 */
	class OffresinsertionController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Offresinsertion';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Fileuploader' => array(
                'colonneModele' => 'Actioncandidat',
            ),
			'Search.SearchPrg' => array(
				'actions' => array(
					'index' => array(
						'filter' => 'Search'
					),
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
			'Default2',
			'Fileuploader',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Offreinsertion',
			'Actioncandidat',
			'Contactpartenaire',
			'Option',
			'Partenaire',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'view' => 'Offresinsertion:index',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajaxfiledelete',
			'ajaxfileupload',
			'download',
			'fileview',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'create',
			'download' => 'read',
			'exportcsv' => 'read',
			'fileview' => 'read',
			'index' => 'read',
			'view' => 'read',
		);

		public function _setOptions() {
			$options = array();
			$options = array_merge(
				$this->Actioncandidat->enums(),
				$this->Partenaire->enums()
			);
			$listeActions = $this->Actioncandidat->find( 'list', array( 'order' => 'Actioncandidat.name ASC' ) );
			$listePartenaires = $this->Actioncandidat->Partenaire->find( 'list', array( 'order' => 'Partenaire.libstruc ASC' ) );
			$listeContacts = $this->Actioncandidat->Contactpartenaire->find( 'list', array( 'order' => 'Contactpartenaire.nom ASC' ) );
			$correspondants = $this->Actioncandidat->ActioncandidatPersonne->Referent->find('list', array( 'order' => 'Referent.nom ASC' ) );
			$query = array(
				'fields' => 'Partenaire.codepartenaire',
				'contain' => false,
				'group' => 'Partenaire.codepartenaire',
				'order' => 'Partenaire.codepartenaire'
			);
			foreach (Hash::extract($this->Actioncandidat->Partenaire->find('all', $query), '{n}.Partenaire.codepartenaire') as $code) {
				$options['Partenaire']['codepartenaire'][$code] = $code;
			}

			$query = array(
				'fields' => 'Actioncandidat.themecode',
				'contain' => false,
				'group' => 'Actioncandidat.themecode',
				'order' => 'Actioncandidat.themecode'
			);
			foreach (Hash::extract($this->Actioncandidat->find('all', $query), '{n}.Actioncandidat.themecode') as $code) {
				$options['Actioncandidat']['themecode'][$code] = $code;
			}

			$query = array(
				'fields' => 'Actioncandidat.codefamille',
				'contain' => false,
				'group' => 'Actioncandidat.codefamille',
				'order' => 'Actioncandidat.codefamille'
			);
			foreach (Hash::extract($this->Actioncandidat->find('all', $query), '{n}.Actioncandidat.codefamille') as $code) {
				$options['Actioncandidat']['codefamille'][$code] = $code;
			}

			$this->set( compact( 'options', 'listeActions', 'listePartenaires', 'listeContacts', 'correspondants' ) );
		}


		/**
		* http://valums.com/ajax-upload/
		* http://doc.ubuntu-fr.org/modules_php
		* increase post_max_size and upload_max_filesize to 10M
		* debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		*/

		public function ajaxfileupload() {
			$this->Fileuploader->ajaxfileupload();
		}

		/**
		* http://valums.com/ajax-upload/
		* http://doc.ubuntu-fr.org/modules_php
		* increase post_max_size and upload_max_filesize to 10M
		* debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		* FIXME: traiter les valeurs de retour
		*/

		public function ajaxfiledelete() {
			$this->Fileuploader->ajaxfiledelete();
		}

		/**
		*   Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		*/

		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		*   Téléchargement des fichiers préalablement associés à un traitement donné
		*/

		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}


		public function index() {
			if( !empty( $this->request->data ) ) {
                $querydatas = array(
                    'global' => $this->Offreinsertion->searchGlobal( $this->request->data ),
                    'actions' => $this->Offreinsertion->searchActions( $this->request->data ),
                    'contactpartenaires' => $this->Offreinsertion->searchContactpartenaires( $this->request->data ),
                    'partenaires' => $this->Offreinsertion->searchPartenaires( $this->request->data )
                );

                $results = array();
                foreach( $querydatas as $key => $querydata ) {
                    $results[$key] = $this->Actioncandidat->find( 'all', $querydata );
                }
                $results['actions_par_partenaires'] = $results['partenaires'];


                // FIXME: liste des actions par partenaires
                foreach( $results['actions_par_partenaires'] as $i => $result ) {
                    $partenaire_id = Set::classicExtract( $result, 'Partenaire.id' );

                    $partenairesParContacts = $this->Contactpartenaire->find(
                        'all',
                        array(
                            'conditions' => array(
                                'Contactpartenaire.partenaire_id' => $partenaire_id
                            ),
                            'contain' => array(
                                'Actioncandidat' => array(
                                    'order' => array( 'Actioncandidat.name ASC', 'Actioncandidat.id ASC'  )
                                )
                            )
                        )
                    );

                    $listeActions = Hash::extract( $partenairesParContacts, '{n}.Actioncandidat.{n}.name' );

                    sort($listeActions, SORT_REGULAR);

                    $partenairesParContacts = $listeActions;
                    $results['actions_par_partenaires'][$i]['Partenaire']['listeactions'] = $partenairesParContacts;
                }

				$this->set( compact( 'results' ) );
			}
			$this->_setOptions();
		}

		public function view( $actioncandidat_id = null ) {
			$this->assert( is_numeric( $actioncandidat_id ), 'error404' );

            $fichiers = array();
			$actioncandidat = $this->Actioncandidat->find(
				'first',
				array(
					'conditions' => array(
						'Actioncandidat.id' => $actioncandidat_id
					),
					'contain' => array(
						'Fichiermodule'
					)
				)
			);
			$this->assert( !empty( $actioncandidat ), 'invalidParameter' );

			// Retour à l'entretien en cas de retour
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'offresinsertion', 'action' => 'index' ) );
			}

            if( !empty( $this->request->data ) ) {
				$this->Actioncandidat->begin();

                $saved = $this->Actioncandidat->updateAllUnBound(
					array( 'Actioncandidat.haspiecejointe' => '\''.$this->request->data['Actioncandidat']['haspiecejointe'].'\'' ),
					array(
						'"Actioncandidat"."id"' => $actioncandidat_id
					)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une action
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "Actioncandidat.haspiecejointe" ), $actioncandidat_id ) && $saved;
				}

				if( $saved ) {
					$this->Actioncandidat->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( $this->referer() );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $actioncandidat_id );
					$this->Actioncandidat->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				$this->request->data = $actioncandidat;
				$fichiers = $this->Fileuploader->fichiers( $actioncandidat['Actioncandidat']['id'] );
			}
			$this->Actioncandidat->commit();


			$this->_setOptions();
			$this->set( compact( 'fichiers', 'actioncandidat' ) );
		}

        /**
         * Fonction permettant d'exporter le tableau de résultats de la refcherche
		 * globale au format CSV.
         */
        public function exportcsv() {

            $queryData = $this->Offreinsertion->searchGlobal( Hash::expand( $this->request->params['named'], '__' ) );
			unset( $queryData['limit'] );

			$actionscandidat = $this->Actioncandidat->find( 'all', $queryData );

			$this->layout = '';
			$this->set( compact( 'headers', 'actionscandidat' ) );
			$this->_setOptions();
		}
	}
?>