<?php
	/**
	 * Code source de la classe EntretiensController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAccessDsps', 'Utility');

	/**
	 * La classe EntretiensController ....
	 *
	 * @package app.Controller
	 */
	class EntretiensController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Entretiens';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Default',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array( 'search' )
			),
			'WebrsaAccesses',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Csv',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Locale',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Entretien',
			'Option',
			'WebrsaEntretien',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Entretiens:edit',
			'exportcsv' => 'Criteresentretiens:exportcsv',
			'search' => 'Criteresentretiens:index',
			'view' => 'Entretiens:index',
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajaxaction',
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
			'add' => 'create',
			'ajaxaction' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'exportcsv' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'impression' => 'update',
			'index' => 'read',
			'search' => 'read',
			'view' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$options = array( );
			$optionsdsps = array( );

			$options = $this->Entretien->enums();
			$optionsdsps = $this->Entretien->Personne->Dsp->enums();
			$options = Set::merge( $options, $optionsdsps );

			$options[$this->modelClass]['typerdv_id'] = $this->Entretien->Typerdv->find( 'list' );
			$options[$this->modelClass]['objetentretien_id'] = $this->Entretien->Objetentretien->find( 'list' );
            $options[$this->modelClass]['structurereferente_id'] = $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) );

			$typerdv = $this->Entretien->Rendezvous->Typerdv->find( 'list' );
			$this->set( compact( 'options', 'typerdv' ) );
		}

		/**
		 * Ajax pour les partenaires fournissant l'action liée à l'entretien
		 *
		 * @param integer $actioncandidat_id
		 */
		public function ajaxaction( $actioncandidat_id = null ) {
			Configure::write( 'debug', 0 );

			$dataActioncandidat_id = Set::extract( $this->request->data, 'Entretien.actioncandidat_id' );
			$actioncandidat_id = ( empty( $actioncandidat_id ) && !empty( $dataActioncandidat_id ) ? $dataActioncandidat_id : $actioncandidat_id );

			if( !empty( $actioncandidat_id ) ) {
				$actioncandidat = $this->Entretien->Actioncandidat->find(
					'first',
                    array(
                        'conditions' => array(
                            'Actioncandidat.id' => $actioncandidat_id
                        ),
                        'contain' => array(
                            'Contactpartenaire' => array(
                                'Partenaire'
                            ),
                            'Referent',
                            'Fichiermodule'
                        )
                    )
				);

				$this->set( compact( 'actioncandidat' ) );
			}
			$this->render( 'ajaxaction', 'ajax' );
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
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Entretien->personneId( $id ) ) ) );

			$fichiers = array( );
			$entretien = $this->Entretien->find(
					'first', array(
				'conditions' => array(
					'Entretien.id' => $id
				),
				'contain' => array(
					'Fichiermodule' => array(
						'fields' => array( 'name', 'id', 'created', 'modified' )
					)
				)
					)
			);

			$personne_id = $entretien['Entretien']['personne_id'];
			$dossier_id = $this->Entretien->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Entretien->begin();

				$saved = $this->Entretien->updateAllUnBound(
						array( 'Entretien.haspiecejointe' => '\''.$this->request->data['Entretien']['haspiecejointe'].'\'' ), array(
					'"Entretien"."personne_id"' => $personne_id,
					'"Entretien"."id"' => $id
						)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "Entretien.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Entretien->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
// 					$this->redirect( array(  'controller' => 'entretiens','action' => 'index', $personne_id ) );
					$this->redirect( $this->referer() );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Entretien->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}

			$this->_setOptions();
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'entretien' ) );
			$this->set( 'urlmenu', '/entretiens/index/'.$personne_id );
		}

		public function search() {
			$this->helpers[] = 'Search.SearchForm';
			$Recherches = $this->Components->load( 'WebrsaRecherchesEntretiens' );
			$Recherches->search();
		}

		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesEntretiens' );
			$Recherches->exportcsv();
		}

		/**
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			// On s'assure que la personne existe
			$this->Entretien->Personne->unbindModelAll();
			$nbrPersonnes = $this->Entretien->Personne->find(
					'count', array(
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'contain' => false
					)
			);
			$this->assert( ( $nbrPersonnes == 1 ), 'invalidParameter' );

			$this->_setEntriesAncienDossier( $personne_id, 'Entretien' );
			
			$entretiens = $this->WebrsaAccesses->getIndexRecords(
				$personne_id, array(
					'fields' => array(
						'Entretien.id',
						'Entretien.personne_id',
						'Entretien.dateentretien',
						'Entretien.arevoirle',
						'Entretien.typeentretien',
						'Structurereferente.lib_struc',
						$this->Entretien->Referent->sqVirtualField( 'nom_complet' ),
						'Objetentretien.name',
					),
					'contain' => array(
						'Structurereferente',
						'Referent',
						'Objetentretien',
						'Fichiermodule'
					),
					'conditions' => array(
						'Entretien.personne_id' => $personne_id
					),
					'order' => array(
						'Entretien.dateentretien DESC', 'Entretien.id DESC'
					)
				)
			);

			$this->_setOptions();

			$this->set( compact( 'entretiens', 'nbFichiersLies' ) );
			$this->set( 'personne_id', $personne_id );
		}

		/**
		 *
		 */
		public function add($personne_id) {
			$this->WebrsaAccesses->check(null, $personne_id);
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

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
			}
			else if( $this->action == 'edit' ) {
				$entretien_id = $id;
				$qd_entretien = array(
					'conditions' => array(
						'Entretien.id' => $entretien_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$entretien = $this->Entretien->find( 'first', $qd_entretien );

				$this->assert( !empty( $entretien ), 'invalidParameter' );

				$personne_id = $entretien['Entretien']['personne_id'];
			}
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$dossier_id = $this->Entretien->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				if( $this->action == 'edit' ) {
					$id = $this->Entretien->field( 'personne_id', array( 'id' => $id ) );
				}
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $id ) );
			}

			///Récupération de la liste des structures référentes
			$structs = $this->Entretien->Structurereferente->listOptions();
			$this->set( 'structs', $structs );

			///Récupération de la liste des référents
			$referents = $this->Entretien->Referent->WebrsaReferent->listOptions();
			$this->set( 'referents', $referents );

            //On affiche les actions inactives en édition mais pas en ajout,
            // afin de pouvoir gérer les actions n'étant plus prises en compte mais toujours en cours
            $isactive = 'O';
            if( $this->action == 'edit' ){
                $isactive = array( 'O', 'N' );
            }
            $actionsSansFiche = $this->{$this->modelClass}->Actioncandidat->listePourFicheCandidature( null, $isactive, array( '0', '1' ) );
            $this->set( 'actionsSansFiche', $actionsSansFiche );

			if( !empty( $this->request->data ) ) {
				$this->Entretien->begin();

				if( isset( $this->request->data['Entretien']['arevoirle'] ) ) {
					$this->request->data['Entretien']['arevoirle']['day'] = '01';
				}

				if( $this->Entretien->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
					if( $this->Entretien->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) ) ) {
						$this->Entretien->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
						$this->redirect( array( 'controller' => 'entretiens', 'action' => 'index', $personne_id ) );
					}
					else {
						$this->Entretien->rollback();
						$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
					}
				}
			}
			else if( $this->action == 'edit' ) {
				$entretien['Entretien']['referent_id'] = $entretien['Entretien']['structurereferente_id'].'_'.$entretien['Entretien']['referent_id'];

				$rdv_id = Set::classicExtract( $entretien, 'Entretien.rendezvous_id' );
				$qd_rdv = array(
					'conditions' => array(
						'Rendezvous.id' => $rdv_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$rdv = $this->Entretien->Personne->Rendezvous->find( 'first', $qd_rdv );

				if( !empty( $rdv ) ) {
					$entretien = Set::merge( $entretien, $rdv );
					$entretien['Rendezvous']['referent_id'] = $entretien['Rendezvous']['structurereferente_id'].'_'.$entretien['Rendezvous']['referent_id'];
				}
				$this->request->data = $entretien;
			}

			$this->_setOptions();
			$this->set( 'personne_id', $personne_id );
			$this->set( 'urlmenu', '/entretiens/index/'.$personne_id );

			$this->render( 'add_edit' );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function view( $id = null ) {
			$this->WebrsaAccesses->check($id);
			$qd_entretien = array(
				'conditions' => array(
					'Entretien.id' => $id
				),
				'fields' => array(
					'Entretien.personne_id',
					'Entretien.dateentretien',
					'Structurereferente.lib_struc',
					$this->Entretien->Referent->sqVirtualField( 'nom_complet' ),
					'Entretien.typeentretien',
					'Entretien.typerdv_id',
					'Entretien.commentaireentretien'
				),
				'order' => null,
				'joins' => array(
					$this->Entretien->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Entretien->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$this->Entretien->join( 'Referent', array( 'type' => 'LEFT OUTER' ) )
				),
				'recursive' => -1
			);
			$entretien = $this->Entretien->find( 'first', $qd_entretien );

			$personne_id = Set::classicExtract( $entretien, 'Entretien.personne_id' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			// Retour à l'entretien en cas de retour
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'entretiens', 'action' => 'index', $personne_id ) );
			}

			$this->_setOptions();
			$this->set( compact( 'entretien' ) );
			$this->set( 'personne_id', $personne_id );
			$this->set( 'urlmenu', '/entretiens/index/'.$personne_id );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->WebrsaAccesses->check($id);
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Entretien->personneId( $id ) ) );

			$this->Default->delete( $id );
		}

		/**
		 * On lui donne l'id du CUI et le modèle de document et il renvoi le pdf
		 *
		 * @param integer $entretien_id
		 * @param string $modeleOdt
		 * @return PDF
		 */
		protected function _getEntretienPdf( $entretien_id, $modeleOdt = null ){
			$Model = $this->{$this->modelClass};

			$path =
				$modeleOdt === null || !isset($Model->modelesOdt[$modeleOdt])
				? sprintf( $Model->modelesOdt['default'], $Model->alias )
				: sprintf( $Model->modelesOdt[$modeleOdt], $Model->alias )
			;

			$Model->forceVirtualFields = true;

			$queryImpressionEntretien = $Model->queryImpression( $entretien_id );

			$queryImpressionEntretien['fields'] = array_merge( $queryImpressionEntretien['fields'], $Model->fields() );
			$queryImpressionEntretien['conditions']["{$Model->alias}.{$Model->primaryKey}"] = $entretien_id;
			$queryImpressionEntretien['contain'] = false;

			$dataEntretien = $Model->find( 'first', $queryImpressionEntretien );

			$options = array_merge(
				$Model->options()
			);

			$options[$this->modelClass]['structurereferente_id'] = $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) );

			$result = $Model->ged(
				$dataEntretien,
				$path,
				false,
				$options
			);

			return $result;
		}

		/**
		 * Méthode générique d'impression d'un Entretien.
		 *
		 * @param integer $entretien_id
		 * @param string $modeleOdt
		 */
		protected function _impression( $entretien_id, $modeleOdt = null ){
			$personne_id = $this->Entretien->personneId( $entretien_id );
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $personne_id ) );

			$pdf = $this->_getEntretienPdf( $entretien_id, $modeleOdt );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'entretien_%d-%s.pdf', $entretien_id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer le PDF.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Impression d'un CUI
		 *
		 * @param integer $entretien_id
		 */
		public function impression( $entretien_id ) {
			$this->WebrsaAccesses->check($entretien_id);
			$this->_impression($entretien_id);
		}
	}
?>