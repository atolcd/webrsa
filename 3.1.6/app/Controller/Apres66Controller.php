<?php
    /**
	 * Code source de la classe Apres66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'CakeEmail', 'Network/Email' );
	App::uses( 'WebrsaEmailConfig', 'Utility' );

	/**
	 * La classe Apres66Controller permet de lister, voir, ajouter, supprimer, ...  des APREs (CG 66).
	 *
	 * @package app.Controller
	 */
	class Apres66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Apres66';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Apre66',
			'Adressefoyer',
			'Aideapre66',
			'Foyer',
			'Fraisdeplacement66',
			'Option',
			'Personne',
			'Pieceaide66',
			'Pieceaide66Typeaideapre66',
			'Piececomptable66',
			'Piececomptable66Typeaideapre66',
			'Prestation',
			'Referent',
			'Structurereferente',
			'Themeapre66',
			'Typeaideapre66',
			'WebrsaApre66'
		);

		/**
		 * Helpers utilisés
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Locale',
			'Xform',
			'Xhtml',
		);

		/**
		 * Components utilisés
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes',
			'Default',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'cohorte_validation' => array(
						'filter' => 'Search'
					),
					'cohorte_imprimer' => array(
						'filter' => 'Search'
					),
					'cohorte_notifiees' => array(
						'filter' => 'Search'
					),
					'cohorte_transfert' => array(
						'filter' => 'Search'
					),
					'cohorte_traitement' => array(
						'filter' => 'Search'
					),
				)
			),
			'WebrsaAccesses'
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Apres66:edit',
			'cohorte_imprimer' => 'Cohortesvalidationapres66::validees',
			'cohorte_notifiees' => 'Cohortesvalidationapres66::notifiees',
			'cohorte_traitement' => 'Cohortesvalidationapres66::traitement',
			'cohorte_transfert' => 'Cohortesvalidationapres66::transfert',
			'cohorte_validation' => 'Cohortesvalidationapres66::apresvalider',
			'view66' => 'Apres66:index',
		);

		/**
		 * Actions non soumises aux droits.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajax_get_nb_fichiers_lies',
			'ajaxfiledelete',
			'ajaxfileupload',
			'ajaxpiece',
			'ajaxref',
			'ajaxstruct',
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
			'add' => 'create',
			'ajax_get_nb_fichiers_lies' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'ajaxpiece' => 'read',
			'ajaxref' => 'read',
			'ajaxstruct' => 'read',
			'cancel' => 'update',
			'cohorte_imprimer' => 'read',
			'cohorte_imprimer_impressions' => 'update',
			'cohorte_notifiees' => 'read',
			'cohorte_notifiees_impressions' => 'update',
			'cohorte_traitement' => 'update',
			'cohorte_transfert' => 'update',
			'cohorte_validation' => 'update',
			'download' => 'read',
			'edit' => 'update',
			'exportcsv_imprimer' => 'read',
			'exportcsv_notifiees' => 'read',
			'exportcsv_traitement' => 'read',
			'exportcsv_transfert' => 'read',
			'exportcsv_validation' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'impression' => 'read',
			'index' => 'read',
			'indexparams' => 'read',
			'maillink' => 'read',
			'notifications' => 'read',
			'view66' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$options = (array)Hash::get( $this->{$this->modelClass}->enums(), $this->modelClass );
			$this->set( 'typevoie', $this->Option->typevoie() );
			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'sitfam', $this->Option->sitfam() );
			$this->set( 'sect_acti_emp', ClassRegistry::init('Contratinsertion')->enum('sect_acti_emp') );
			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
			$this->set( 'typeservice', ClassRegistry::init( 'Serviceinstructeur' )->find( 'first' ) );

			$this->set( 'themes', $this->Themeapre66->find( 'list' ) );
			$this->set( 'nomsTypeaide', $this->Typeaideapre66->find( 'list' ) );

			$options = Hash::merge( $options, (array)Hash::get( $this->{$this->modelClass}->Aideapre66->enums(), 'Aideapre66' ) );

			$this->set( 'options', $options );
			$pieceadmin = $this->Pieceaide66->find(
					'list', array(
				'fields' => array(
					'Pieceaide66.id',
					'Pieceaide66.name'
				),
				'contain' => false
					)
			);
			$this->set( 'pieceadmin', $pieceadmin );
			$piececomptable = $this->Piececomptable66->find(
					'list', array(
				'fields' => array(
					'Piececomptable66.id',
					'Piececomptable66.name'
				),
				'contain' => false
					)
			);
			$this->set( 'piececomptable', $piececomptable );
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
		 * Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 * Téléchargement des fichiers préalablement associés à un traitement donné
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 * Fonction permettant d'accéder à la page pour lier les fichiers.
		 *
		 * @param integer $id
		 */
		public function filelink( $id ) {
			$this->WebrsaAccesses->check($id);
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->{$this->modelClass}->personneId( $id ) ) ) );

			$fichiers = array( );
			$apre = $this->{$this->modelClass}->find(
					'first', array(
				'conditions' => array(
					"{$this->modelClass}.id" => $id
				),
				'contain' => array(
					'Fichiermodule' => array(
						'fields' => array( 'name', 'id', 'created', 'modified' )
					)
				)
					)
			);

			$personne_id = $apre[$this->modelClass]['personne_id'];
			$dossier_id = $this->{$this->modelClass}->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
                $this->Jetons2->release( $dossier_id );
				$redirect_url = $this->Session->read( "Savedfilters.{$this->name}.{$this->action}" );
				if( !empty( $redirect_url ) ) {
					$this->Session->delete( "Savedfilters.{$this->name}.{$this->action}" );
					$this->redirect( $redirect_url );
				}
				else {
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
			}

			if( !empty( $this->request->data ) ) {
                $this->{$this->modelClass}->begin();

				$saved = $this->{$this->modelClass}->updateAllUnBound(
						array( "{$this->modelClass}.haspiecejointe" => '\''.$this->request->data[$this->modelClass]['haspiecejointe'].'\'' ), array(
					"{$this->modelClass}.personne_id" => $personne_id,
					"{$this->modelClass}.id" => $id
						)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "{$this->modelClass}.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->{$this->modelClass}->commit();
                    $this->Jetons2->release( $dossier_id );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( $this->referer() );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->{$this->modelClass}->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}

			$this->_setOptions();
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'apre' ) );
			$this->render( (CAKE_BRANCH == '1.2' ? '/apres/' : '/Apres/') .'filelink' );
		}

		/**
		 * Permet de regrouper l'ensemble des paramétrages pour l'APRE
		 */
		public function indexparams() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'parametrages', 'action' => 'index' ) );
			}

			$compteurs = array(
				'Pieceaide66' => ClassRegistry::init( 'Pieceaide66' )->find( 'count' ),
				'Themeapre66' => ClassRegistry::init( 'Themeapre66' )->find( 'count' )
			);
			$this->set( compact( 'compteurs' ) );

			$this->render( (CAKE_BRANCH == '1.2' ? '/apres/' : '/Apres/') .'indexparams_'.Configure::read( 'nom_form_apre_cg' ) );
		}

		/**
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$qd_personne = array(
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$personne = $this->{$this->modelClass}->Personne->find( 'first', $qd_personne );
			$this->assert( !empty( $personne ), 'invalidParameter' );
			$this->set( 'personne', $personne );

			$this->_setEntriesAncienDossier( $personne_id, 'Apre' );
			
			$apres = $this->WebrsaAccesses->getIndexRecords(
				$personne_id, array(
					'fields' => array_merge(
						$this->{$this->modelClass}->fields(),
						$this->{$this->modelClass}->Personne->fields(),
						$this->{$this->modelClass}->Aideapre66->fields(),
						array(
							$this->{$this->modelClass}->Fichiermodule->sqNbFichiersLies($this->{$this->modelClass}, 'nombre'),
						)
					),
					'contain' => array(
						'Personne',
						'Aideapre66'
					),
                    'conditions' => array(
                        "{$this->modelClass}.personne_id" => $personne_id
                    ),
                    'order' => array( "Aideapre66.datedemande DESC"  )
                )
			);
			$this->set( 'apres', $apres );

			$referents = $this->Referent->find( 'list' );
			$this->set( 'referents', $referents );

			$this->set( 'personne_id', $personne_id );


			/// La personne a-t'elle bénéficié d'aides trop importantes ?
			$alerteMontantAides = false;
			$montantMaxComplementaires = Configure::read( 'Apre.montantMaxComplementaires' );
			$periodeMontantMaxComplementaires = Configure::read( 'Apre.periodeMontantMaxComplementaires' );

			$montantaccorde = $this->Apre66->WebrsaApre66->getMontantApreEnCours($personne_id);

			if( $montantaccorde > Configure::read( "Apre.montantMaxComplementaires" ) ) {
				$alerteMontantAides = true;
			}
			$this->set( 'apresPourCalculMontant', $montantaccorde === null ? 0 : $montantaccorde );
			$this->set( 'apres', $apres );
			$this->set( 'alerteMontantAides', $alerteMontantAides );
			$this->_setOptions();
			$this->render( (CAKE_BRANCH == '1.2' ? '/apres/' : '/Apres/') .'index66' );
		}

		/**
		 * Ajax pour les coordonnées de la structure référente liée
		 *
		 * @param integer $structurereferente_id
		 */
		public function ajaxstruct( $structurereferente_id = null ) { // FIXME
			Configure::write( 'debug', 0 );
			$dataStructurereferente_id = Set::extract( $this->request->data, "{$this->modelClass}.structurereferente_id" );
			$structurereferente_id = ( empty( $structurereferente_id ) && !empty( $dataStructurereferente_id ) ? $dataStructurereferente_id : $structurereferente_id );
			$qd_struct = array(
				'conditions' => array(
					'Structurereferente.id' => $structurereferente_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$struct = $this->{$this->modelClass}->Structurereferente->find( 'first', $qd_struct );

			$this->set( 'struct', $struct );
			$this->render( (CAKE_BRANCH == '1.2' ? '/apres/' : '/Apres/') .'ajaxstruct', 'ajax' );
		}

		/**
		 * Ajax pour les coordonnées du référent APRE
		 *
		 * @param integer $referent_id
		 */
		public function ajaxref( $referent_id = null ) { // FIXME
			Configure::write( 'debug', 0 );
			if( !empty( $referent_id ) ) {
				$referent_id = suffix( $referent_id );
			}
			else {
				$referent_id = suffix( Set::extract( $this->request->data, "{$this->modelClass}.referent_id" ) );
			}
			// INFO: éviter les requêtes erronées du style ... WHERE "Referent"."id" = ''
			$referent = array( );
			if( !empty( $referent_id ) ) {
				$qd_referent = array(
					'conditions' => array(
						'Referent.id' => $referent_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$referent = $this->{$this->modelClass}->Referent->find( 'first', $qd_referent );
			}
//             $referent = $this->{$this->modelClass}->Referent->findbyId( $referent_id, null, null, -1 );
			$this->set( 'referent', $referent );
			$this->render( (CAKE_BRANCH == '1.2' ? '/apres/' : '/Apres/') .'ajaxref', 'ajax' );
		}

		/**
		 * Ajax pour les coordonnées du référent APRE
		 *
		 * @param integer $apre_id
		 */
		public function ajaxpiece( $apre_id = null ) {
			$typeaideapre66_id = Set::classicExtract( $this->request->data, 'Aideapre66.typeaideapre66_id' );
			$isapre = Hash::get($this->request->data, 'Apre66.isapre');
			$typeaideapre = array();

			if( !empty( $typeaideapre66_id ) ) {
				$typeaideapre66_id = suffix( $typeaideapre66_id );
			}
			else {
				$typeaideapre66_id = suffix( Set::extract( $this->request->data, 'Aideapre66.typeaideapre66_id' ) );
			}

			if( !empty( $typeaideapre66_id ) ) {
				$piecesadmin = $this->{$this->modelClass}->Aideapre66->Typeaideapre66->Pieceaide66->find(
						'list', array(
					'fields' => array( 'Pieceaide66.id', 'Pieceaide66.name' ),
					'joins' => array(
						array(
							'table' => 'piecesaides66_typesaidesapres66',
							'alias' => 'Pieceaide66Typeaideapre66',
							'type' => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Pieceaide66Typeaideapre66.pieceaide66_id = Pieceaide66.id',
								'Pieceaide66Typeaideapre66.typeaideapre66_id' => $typeaideapre66_id,
							)
						)
					),
					'order' => array( 'Pieceaide66.name' ),
					'recursive' => -1
						)
				);

				$piecescomptable = $this->{$this->modelClass}->Aideapre66->Typeaideapre66->Piececomptable66->find(
						'list', array(
					'fields' => array( 'Piececomptable66.id', 'Piececomptable66.name' ),
					'joins' => array(
						array(
							'table' => 'piecescomptables66_typesaidesapres66',
							'alias' => 'Piececomptable66Typeaideapre66',
							'type' => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Piececomptable66Typeaideapre66.piececomptable66_id = Piececomptable66.id',
								'Piececomptable66Typeaideapre66.typeaideapre66_id' => $typeaideapre66_id,
							)
						)
					),
					'order' => array( 'Piececomptable66.name' ),
					'recursive' => -1
						)
				);
				$typeaideapre = $this->request->data = $this->{$this->modelClass}->Aideapre66->Typeaideapre66->find(
					'first', array('conditions' => array('Typeaideapre66.id' => $typeaideapre66_id),)
				);
			}
			
			$this->request->data = array( );

			if( !empty( $apre_id ) ) {
				$aideapre66_existante = $this->{$this->modelClass}->Aideapre66->find(
						'first', array(
					'conditions' => array(
						'Aideapre66.apre_id' => $apre_id
					),
					'contain' => array(
						'Pieceaide66',
						'Piececomptable66'
					)
						)
				);

				if( !empty( $typeaideapre66_id ) ) {
					$typeaideapre = $this->request->data = $this->{$this->modelClass}->Aideapre66->find(
							'first', array(
						'conditions' => array(
							'Aideapre66.typeaideapre66_id' => $typeaideapre66_id
						),
						'contain' => array( 'Typeaideapre66' )
							)
					);
				}

				if( !empty( $typeaideapre66_id ) && ( $aideapre66_existante['Aideapre66']['typeaideapre66_id'] == $typeaideapre66_id ) ) {
					$this->request->data = array(
						'Pieceaide66' => array(
							'Pieceaide66' => Set::extract( '/Pieceaide66/id', $aideapre66_existante )
						),
						'Piececomptable66' => array(
							'Piececomptable66' => Set::extract( '/Piececomptable66/id', $aideapre66_existante )
						),
					);
				}
			}

			$this->set( compact( 'piecesadmin', 'piecescomptable', 'typeaideapre', 'isapre' ) );

			$this->render( (CAKE_BRANCH == '1.2' ? '/apres/' : '/Apres/') .'ajaxpiece', 'ajax' );
		}

		/**
		 * Visualisation de l'APRE
		 *
		 * @param integer $apre_id
		 */
		public function view66( $apre_id = null ) {
			$this->WebrsaAccesses->check($apre_id);
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->{$this->modelClass}->personneId( $apre_id ) ) ) );

			$apre = $this->Apre66->find(
					'first', array(
				'conditions' => array(
					'Apre66.id' => $apre_id
				),
				'contain' => array(
					'Personne',
					'Referent',
					'Structurereferente',
					'Aideapre66'
				)
					)
			);

			$this->assert( !empty( $apre ), 'invalidParameter' );

			$this->set( 'apre', $apre );
// 			debug( $apre );
			$this->set( 'personne_id', $apre['Apre66']['personne_id'] );
			$this->_setOptions();
			$this->set( 'urlmenu', '/apres66/index/'.$apre['Personne']['id'] );
			$this->render( (CAKE_BRANCH == '1.2' ? '/apres/' : '/Apres/') .'view66' );
		}

		/**
		 *
		 */
		public function add($personne_id) {
			$this->WebrsaAccesses->check(null, $personne_id);
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */
		public function edit($id) {
			$this->WebrsaAccesses->check($id);
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 * @param integer $id
		 */
		protected function _add_edit( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			// Récupération des id afférents
			if( $this->action == 'add' ) {
				$personne_id = $id;
				$dossier_id = $this->Personne->dossierId( $personne_id );

				$qd_foyer = array(
					'conditions' => array(
						'Foyer.dossier_id' => $dossier_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$foyer = $this->Foyer->find( 'first', $qd_foyer );
				$foyer_id = Set::classicExtract( $foyer, 'Foyer.id' );
			}
			else if( $this->action == 'edit' ) {
				$apre_id = $id;

				$apre = $this->{$this->modelClass}->find(
					'first',
					array(
						'conditions' => array(
							'Apre66.id' => $apre_id
						),
						'contain' => array(
							'Personne',
							'Referent',
							'Structurereferente',
							'Aideapre66' => array(
								'Themeapre66',
								'Typeaideapre66',
								'Fraisdeplacement66',
								'Pieceaide66',
								'Piececomptable66'
							)
						)
					)
				);

				$this->assert( !empty( $apre ), 'invalidParameter' );

				$personne_id = $apre[$this->modelClass]['personne_id'];
				$dossier_id = $this->{$this->modelClass}->dossierId( $apre_id );

				$foyer_id = Set::classicExtract( $apre, 'Personne.foyer_id' );
			}
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'dossier_id', $dossier_id );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

            $this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}
			/**
			 *   Liste des APREs de la personne pour l'affichage de l'historique
			 *   lors de l'add/edit
			 * */
			$conditionsListeApres = array( "{$this->modelClass}.personne_id" => $personne_id );
			if( $this->action == 'edit' ) {
				$conditionsListeApres["{$this->modelClass}.id <>"] = $apre_id;
			}

			$listApres = $this->{$this->modelClass}->find(
				'all',
				array(
					'conditions' => $conditionsListeApres,
					'recursive' => -1
				)
			);
			$this->set( compact( 'listApres' ) );
			if( !empty( $listApres ) ) {
				$listesAidesSelonApre = $this->{$this->modelClass}->Aideapre66->find(
						'all', array(
					'conditions' => array(
						'Aideapre66.apre_id' => Set::extract( $listApres, "/{$this->modelClass}/id" ),
						'Aideapre66.decisionapre' => 'ACC'
					),
					'recursive' => -1
						)
				);
				$this->set( compact( 'listesAidesSelonApre' ) );
			}


			///Récupération de la liste des structures référentes liés uniquement à l'APRE
//			$structs = $this->Structurereferente->listeParType( array( 'apre' => true ) );
            $structs = $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'list', 'conditions' => array( 'Structurereferente.apre' => 'O' ) + $this->InsertionsBeneficiaires->conditions['structuresreferentes'], 'prefix' => false ) );
			$this->set( 'structs', $structs );
			///Récupération de la liste des référents liés à l'APRE
			$referents = $this->Referent->WebrsaReferent->listOptions();
			$this->set( 'referents', $referents );
			///Récupération de la liste des référents liés à l'APRE
			$typesaides = $this->Typeaideapre66->listOptions();
			$this->set( 'typesaides', $typesaides );


			///Personne liée au parcours
			$personne_referent = $this->Personne->PersonneReferent->find(
					'first', array(
				'conditions' => array(
					'PersonneReferent.personne_id' => $personne_id,
					'PersonneReferent.dfdesignation IS NULL'
				),
				'recursive' => -1
					)
			);


			///On ajout l'ID de l'utilisateur connecté afind e récupérer son service instructeur
			$personne = $this->{$this->modelClass}->Personne->WebrsaPersonne->detailsApre( $personne_id, $this->Session->read( 'Auth.User.id' ) );
			$this->set( 'personne', $personne );

			///Nombre d'enfants par foyer
			$nbEnfants = $this->Foyer->nbEnfants( Set::classicExtract( $personne, 'Foyer.id' ) );
			$this->set( 'nbEnfants', $nbEnfants );

			if( !empty( $this->request->data ) ) {
                $this->Apre66->begin();
				/// Pour le nombre de pièces afin de savoir si le dossier est complet ou non
				$valide = false;
				$nbNormalPieces = array( );

				$typeaideapre66_id = suffix( Set::classicExtract( $this->request->data, 'Aideapre66.typeaideapre66_id' ) );
				$typeaide = array( );
				if( !empty( $typeaideapre66_id ) ) {

					$qd_typeaide = array(
						'conditions' => array(
							'Typeaideapre66.id' => $typeaideapre66_id
						),
						'fields' => null,
						'order' => null,
						'contain' => array(
							'Pieceaide66'
						)
					);
					$typeaide = $this->{$this->modelClass}->Aideapre66->Typeaideapre66->find( 'first', $qd_typeaide );
// debug($typeaide);
// die();

					$nbNormalPieces['Typeaideapre66'] = count( Set::extract( $typeaide, '/Pieceaide66/id' ) );

					$key = 'Pieceaide66';
					if( isset( $this->request->data['Aideapre66'] ) && isset( $this->request->data[$key] ) && isset( $this->request->data[$key][$key] ) ) {
						$nbpieces = 0;
						if( !empty( $this->request->data[$key][$key] ) ) {
							foreach( $this->request->data[$key][$key] as $piece_key ) {
								if( !empty( $piece_key ) )
									$nbpieces++;
							}
						}
						$valide = ( $nbpieces == $nbNormalPieces['Typeaideapre66'] );
					}
				}
				$fields = array( 'isbeneficiaire', 'hascer', 'respectdelais' );
				foreach( $fields as $field ) {
					$valide = $this->request->data['Apre66'][$field] && $valide;
				}

				$this->request->data['Apre66']['etatdossierapre'] = ( $valide ? 'COM' : 'INC' );


				// Tentative d'enregistrement de l'APRE complémentaire
				$this->{$this->modelClass}->create( $this->request->data );
				$this->{$this->modelClass}->set( 'statutapre', 'C' );
				$success = $this->{$this->modelClass}->save();

				// Tentative d'enregistrement de l'aide liée à l'APRE complémentaire
				$this->{$this->modelClass}->Aideapre66->create( $this->request->data );

				if( !empty( $this->request->data['Fraisdeplacement66'] ) ) {

					$Fraisdeplacement66 = Hash::filter( (array)$this->request->data['Fraisdeplacement66'] );
					if( !empty( $Fraisdeplacement66 ) ) {
						$this->{$this->modelClass}->Aideapre66->Fraisdeplacement66->create( $this->request->data );
					}
				}

				if( $this->action == 'add' ) {
					$this->{$this->modelClass}->Aideapre66->set( 'apre_id', $this->{$this->modelClass}->getLastInsertID() );
				}
				$success = $this->{$this->modelClass}->Aideapre66->save() && $success;


				if( $this->action == 'add' ) {
					if( !empty( $Fraisdeplacement66 ) ) {
						$this->{$this->modelClass}->Aideapre66->Fraisdeplacement66->set( 'aideapre66_id', $this->{$this->modelClass}->Aideapre66->getLastInsertID() );
					}
				}
				if( !empty( $Fraisdeplacement66 ) ) {
					$success = $this->{$this->modelClass}->Aideapre66->Fraisdeplacement66->save() && $success;
				}


				/*
				  $Modecontact = Hash::expand( Hash::filter( (array)Hash::flatten( $this->request->data['Modecontact'] ) ) );
				  debug($Modecontact);
				  die();
				  if( !empty( $Modecontact ) ){
				  $success = $this->{$this->modelClass}->Personne->Foyer->Modecontact->saveAll( $Modecontact, array( 'validate' => 'first', 'atomic' => false ) ) && $success;
				  } */

				// Tentative d'enregistrement des pièces liées à une APRE selon ne aide donnée
				if( !empty( $this->request->data['Pieceaide66'] ) ) {
					$linkedData = array(
						'Aideapre66' => array(
							'id' => $this->{$this->modelClass}->Aideapre66->id
						),
						'Pieceaide66' => $this->request->data['Pieceaide66']
					);
					$success = $this->{$this->modelClass}->Aideapre66->save( $linkedData ) && $success;
				}


				// SAuvegarde des numéros ed téléphone si ceux-ci ne sont pas présents en amont
				$isDataPersonne = Hash::filter( (array)$this->request->data['Personne'] );
				if( !empty( $isDataPersonne ) ) {
					$success = $this->{$this->modelClass}->Personne->save( array( 'Personne' => $this->request->data['Personne'] ) ) && $success;
				}


				if( $success ) {
                    $this->{$this->modelClass}->commit();
                    $this->Jetons2->release( $dossier_id );
                    $this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'apres'.Configure::read( 'Apre.suffixe' ), 'action' => 'index', $personne_id ) );
				}
				else {
					$this->{$this->modelClass}->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $apre;
				$this->request->data = Hash::insert(
								$this->request->data, "{$this->modelClass}.referent_id", Set::extract( $this->request->data, "{$this->modelClass}.structurereferente_id" ).'_'.Set::extract( $this->request->data, "{$this->modelClass}.referent_id" )
				);

				$typeaideapre66_id = Set::classicExtract( $this->request->data, 'Aideapre66.typeaideapre66_id' );
				$themeapre66_id = $this->{$this->modelClass}->Aideapre66->Typeaideapre66->field( 'themeapre66_id', array( 'id' => $typeaideapre66_id ) );

				$this->request->data = Hash::insert( $this->request->data, 'Aideapre66.themeapre66_id', $themeapre66_id );
				$this->request->data = Hash::insert( $this->request->data, 'Aideapre66.typeaideapre66_id', "{$themeapre66_id}_{$typeaideapre66_id}" );

				///FIXME: doit faire autrement
				if( !empty( $this->request->data['Aideapre66']['Fraisdeplacement66'] ) ) {
					$this->request->data['Fraisdeplacement66'] = $this->request->data['Aideapre66']['Fraisdeplacement66'];
				}
				if( !empty( $this->request->data['Modecontact'] ) ) {
					$this->request->data['Modecontact'] = $personne['Foyer']['Modecontact'];
				}

				$this->request->data['Pieceaide66']['Pieceaide66'] = Set::extract( $apre, '/Aideapre66/Pieceaide66/id' );
				$this->request->data['Piececomptable66']['Piececomptable66'] = Set::extract( $apre, '/Aideapre66/Piececomptable66/id' );
			}

			// Doit-on setter les valeurs par défault ?
			$dataStructurereferente_id = Set::classicExtract( $this->request->data, "{$this->modelClass}.structurereferente_id" );
			$dataReferent_id = Set::classicExtract( $this->request->data, "{$this->modelClass}.referent_id" );

			// Si le formulaire ne possède pas de valeur pour ces champs, on met celles par défaut
			if( empty( $dataStructurereferente_id ) && empty( $dataReferent_id ) ) {
				$structurereferente_id = $referent_id = null;


				$structPersRef = Set::classicExtract( $personne_referent, 'PersonneReferent.structurereferente_id' );
				// Valeur par défaut préférée: à partir de personnes_referents
				if( !empty( $personne_referent ) && array_key_exists( $structPersRef, $structs ) ) {
					$structurereferente_id = Set::classicExtract( $personne_referent, 'PersonneReferent.structurereferente_id' );
					$referent_id = Set::classicExtract( $personne_referent, 'PersonneReferent.referent_id' );
				}

				if( !empty( $structurereferente_id ) ) {
					$this->request->data = Hash::insert( $this->request->data, "{$this->modelClass}.structurereferente_id", $structurereferente_id );
				}
				if( !empty( $structurereferente_id ) && !empty( $referent_id ) ) {
					$this->request->data = Hash::insert( $this->request->data, "{$this->modelClass}.referent_id", preg_replace( '/^_$/', '', "{$structurereferente_id}_{$referent_id}" ) );
				}
			}

			$this->set('typeaideOptions', $this->_typeaideOptions());
			
			$struct_id = Set::classicExtract( $this->request->data, "{$this->modelClass}.structurereferente_id" );
			$this->set( 'struct_id', $struct_id );

			$referent_id = Set::classicExtract( $this->request->data, "{$this->modelClass}.referent_id" );
			$referent_id = preg_replace( '/^[0-9]+_([0-9]+)$/', '\1', $referent_id );
			$this->set( 'referent_id', $referent_id );

			$this->set( 'personne_id', $personne_id );
			$this->_setOptions();
			$this->render( (CAKE_BRANCH == '1.2' ? '/apres/' : '/Apres/') .'add_edit_'.Configure::read( 'nom_form_apre_cg' ) );
		}

		/**
		 * Génère l'impression d'une APRE pour le CG 66.
		 * On prend la décision de ne pas le stocker.
		 *
		 * @param integer $id L'id de l'APRE que l'on veut imprimer.
		 * @return void
		 */
		public function impression( $id = null ) {
			$this->WebrsaAccesses->check($id);
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->{$this->modelClass}->personneId( $id ) ) );

			$pdf = $this->Apre66->getDefaultPdf( $id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'apre_%d-%s.pdf', $id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer l\'impression de l\'APRE.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Imprime une notification d'APRE.
		 *
		 * @param integer $id L'id de l'APRE pour laquelle imprimer la notification.
		 * @return void
		 */
		public function notifications( $id = null ) {
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->{$this->modelClass}->personneId( $id ) ) );

			$pdf = $this->Apre66->WebrsaApre66->getNotificationAprePdf( $id );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'Notification_APRE_%d-%s.pdf', $id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer la notification d\'APRE.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Permet d'envoyer un mail au référent de la demande d'APRE pour lui indiquer
		 * qu'il manque des pièces à cette demande.
		 *
		 * @param integer $id
		 */
		public function maillink( $id = null ) {
			$this->WebrsaAccesses->check($id);
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->{$this->modelClass}->personneId( $id ) ) );

			$apre = $this->Apre66->find(
					'first', array(
				'conditions' => array(
					"Apre66.id" => $id
				),
				'contain' => array(
					'Personne',
					'Referent'
				)
					)
			);

			$this->assert( !empty( $apre ), 'error404' );

			if( !isset( $apre['Referent']['email'] ) || empty( $apre['Referent']['email'] ) ) {
				$this->Session->setFlash( "Mail non envoyé: adresse mail du référent ({$apre['Referent']['nom']} {$apre['Referent']['prenom']}) non renseignée.", 'flash/error' );
				$this->redirect( $this->referer() );
			}

			$success = true;
			try {
				$configName = WebrsaEmailConfig::getName( 'apre66_piecesmanquantes' );
				$Email = new CakeEmail( $configName );

				// Choix du destinataire suivant suivant l'environnement
				if( !WebrsaEmailConfig::isTestEnvironment() ) {
					$Email->to( $apre['Referent']['email'] );
				}
				else {
					$Email->to( WebrsaEmailConfig::getValue( 'apre66_piecesmanquantes', 'to', $Email->from() ) );
				}

				$Email->subject( WebrsaEmailConfig::getValue( 'apre66_piecesmanquantes', 'subject', 'Demande d\'Apre' ) );
				$mailBody = "Bonjour,\n\nle dossier de demande APRE de {$apre['Personne']['qual']} {$apre['Personne']['nom']} {$apre['Personne']['prenom']} ne peut être validé car les fichiers ne sont pas joints dans WEBRSA.\n\nLaurence COSTE.";

				$result = $Email->send( $mailBody );
				$success = !empty( $result ) && $success;
			} catch( Exception $e ) {
				$this->log( $e->getMessage(), LOG_ERROR );
				$success = false;
			}

			if( $success ) {
				$this->Session->setFlash( 'Mail envoyé', 'flash/success' );
			}
			else {
				$this->Session->setFlash( 'Mail non envoyé', 'flash/error' );
			}

			$this->redirect( $this->referer() );
		}


		/**
		 * Fonction pour annuler une APRE pour le CG66
		 *
		 * @param integer $id
		 */
		public function cancel( $id ) {
			$this->WebrsaAccesses->check($id);
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->{$this->modelClass}->personneId( $id ) ) ) );

			$qd_apre = array(
				'conditions' => array(
					$this->modelClass.'.id' => $id
				),
				'fields' => null,
				'order' => null,
                'contain' => array(
                    'Aideapre66'
                ),
//				'recursive' => -1
			);
			$apre = $this->{$this->modelClass}->find( 'first', $qd_apre );

			$personne_id = Set::classicExtract( $apre, 'Apre66.personne_id' );
			$this->set( 'personne_id', $personne_id );

			$dossier_id = $this->{$this->modelClass}->dossierId( $id );
			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->{$this->modelClass}->begin();

				$saved = $this->{$this->modelClass}->save( $this->request->data );

                $saved = $this->Aideapre66->updateAllUnBound(
					array(
                        'Aideapre66.montantaccorde' => NULL,
                        'Aideapre66.decisionapre' => NULL,
                        'Aideapre66.datemontantaccorde' => NULL
                    ),
					array(
						'"Aideapre66"."apre_id"' => $apre['Apre66']['id'],
						'"Aideapre66"."id"' => $apre['Aideapre66']['id']
					)
				) && $saved;

                $saved = $this->{$this->modelClass}->updateAllUnBound(
					array( 'Apre66.etatdossierapre' => '\'ANN\'' ),
					array(
						'"Apre66"."personne_id"' => $apre['Apre66']['personne_id'],
						'"Apre66"."id"' => $apre['Apre66']['id']
					)
				) && $saved;

				if( $saved ) {
					$this->{$this->modelClass}->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					$this->{$this->modelClass}->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement.', 'flash/erreur' );
				}
			}
			else {
				$this->request->data = $apre;
			}
			$this->set( 'urlmenu', '/apres66/index/'.$personne_id );

            $this->render( (CAKE_BRANCH == '1.2' ? '/apres/' : '/Apres/') .'cancel' );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_validation() {
			$Recherches = $this->Components->load( 'WebrsaCohortesApres66' );
			$Recherches->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteApre66Validation' ) );

			$this->Aideapre66->validate = array();
			$this->Apre66->validate = array();
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_validation() {
			$Recherches = $this->Components->load( 'WebrsaCohortesApres66' );
			$Recherches->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteApre66Validation' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_imprimer() {
			$Recherches = $this->Components->load( 'WebrsaCohortesApres66Impressions' );
			$Recherches->search( array( 'modelRechercheName' => 'WebrsaCohorteApre66Imprimer' ) );

			$this->Aideapre66->validate = array();
			$this->Apre66->validate = array();
		}

		/**
		 * Impression de la cohorte
		 */
		public function cohorte_imprimer_impressions() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesApres66Impressions' );
			$Cohortes->impressions(
				array(
					'modelRechercheName' => 'WebrsaCohorteApre66Imprimer',
					'configurableQueryFieldsKey' => 'Apres66.cohorte_imprimer'
				)
			);
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_imprimer() {
			$Recherches = $this->Components->load( 'WebrsaCohortesApres66Impressions' );
			$Recherches->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteApre66Imprimer' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_notifiees() {
			$Recherches = $this->Components->load( 'WebrsaCohortesApres66Impressions' );
			$Recherches->search( array( 'modelRechercheName' => 'WebrsaCohorteApre66Imprimer' ) );

			$this->Aideapre66->validate = array();
			$this->Apre66->validate = array();

			$this->view = 'cohorte_imprimer';
		}

		/**
		 * Impression de la cohorte
		 */
		public function cohorte_notifiees_impressions() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesApres66Impressions' );
			$Cohortes->impressions(
				array(
					'modelRechercheName' => 'WebrsaCohorteApre66Imprimer',
					'configurableQueryFieldsKey' => 'Apres66.cohorte_notifiees'
				)
			);
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_notifiees() {
			$Recherches = $this->Components->load( 'WebrsaCohortesApres66Impressions' );
			$Recherches->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteApre66Imprimer' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_transfert() {
			$this->Apre66->forceVirtualFields = true;
			$Recherches = $this->Components->load( 'WebrsaCohortesApres66' );
			$Recherches->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteApre66Transfert' ) );

			$this->Aideapre66->validate = array();
			$this->Apre66->validate = array();
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_transfert() {
			$this->Apre66->forceVirtualFields = true;
			$Recherches = $this->Components->load( 'WebrsaCohortesApres66' );
			$Recherches->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteApre66Transfert' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_traitement() {
			$Recherches = $this->Components->load( 'WebrsaCohortesApres66' );
			$Recherches->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteApre66Traitement' ) );

			$this->Aideapre66->validate = array();
			$this->Apre66->validate = array();
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv_traitement() {
			$Recherches = $this->Components->load( 'WebrsaCohortesApres66' );
			$Recherches->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteApre66Traitement' ) );
		}

		/**
		 * Permet d'obtenir à partir d'un Apre66.id le nombre de fichiers liés
		 */
		public function ajax_get_nb_fichiers_lies() {
			$apre66_id = $this->request->data['Apre66_id'];
			$result = $this->Apre66->find(
				'first',
				array(
					'fields' => '(SELECT COUNT(*) '
						. 'FROM fichiersmodules AS f '
						. 'WHERE f.fk_value = "Apre66"."id" '
						. 'AND f.modele = \'Apre66\''
						. ') AS "Apre66__nb_fichiers_lies"',
					'conditions' => array(
						'Apre66.id' => $apre66_id
					)
				)
			);

			$this->set( 'json', Hash::get($result, 'Apre66.nb_fichiers_lies') );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}

		/**
		 * Fait la distinction entre les options pour les APREs et celles des ADREs
		 * 
		 * @return array
		 */
		protected function _typeaideOptions() {
			$typeaide = $this->Typeaideapre66->find(
				'all',
				array (
					'fields' => array(
						'Typeaideapre66.id',
						'Typeaideapre66.themeapre66_id',
						'Typeaideapre66.name',
						'Typeaideapre66.typeplafond',
					),
					'contain' => false,
					'order' => 'Typeaideapre66.name ASC',
				)
			);
			
			$options = array(
				'ADRE' => array(),
				'APRE' => array(),
			);
			// INFO : Typeaideapre66.typeplafond renvoie soit : 'APRE', 'ADRE' ou 'ALL'
			foreach ($typeaide as $key => $value) {
				$dependentSelectOption = $value['Typeaideapre66']['themeapre66_id'].'_'.$value['Typeaideapre66']['id'];
				if ($value['Typeaideapre66']['typeplafond'] !== 'APRE') {
					$options['ADRE'][] = $dependentSelectOption;
				}
				if ($value['Typeaideapre66']['typeplafond'] !== 'ADRE') {
					$options['APRE'][] = $dependentSelectOption;
				}
			}

			return $options;
		}
	}
?>
