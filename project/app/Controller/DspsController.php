<?php
	/**
	 * Code source de la classe DspsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAccessDsps', 'Utility' );

	/**
	 * La classe DspsController ...
	 *
	 * @package app.Controller
	 */
	class DspsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Dsps';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Fileuploader',
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'search'
				),
			),
			'WebrsaAccesses' => array(
				'mainModelName' => 'DspRev',
				'webrsaModelName' => 'WebrsaDsp'
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
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault',
			),
			'Dsphm',
			'Fileuploader',
			'Romev3',
			'Search',
			'Xform',
			'Xhtml',
			'Search.SearchForm'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dsp',
			'Catalogueromev3',
			'DspRev',
			'Familleromev3',
			'Option',
			'WebrsaDsp',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'findPersonne' => 'Dsps:view'
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
			'add' => 'create',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'download' => 'read',
			'edit' => 'update',
			'exportcsv' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'findPersonne' => 'read',
			'histo' => 'read',
			'revertTo' => 'update',
			'search' => 'read',
			'view' => 'read',
			'view_diff' => 'read',
			'view_revs' => 'read',
		);

		public $paginate = array(
			'limit' => 10,
			'order' => array( 'DspRev.created' => 'desc', 'DspRev.id' => 'desc' )
		);

		public $wildcardKeys = array(
			'Personne.nom',
			'Personne.prenom',
			'Personne.nir',
			'Dossier.matricule',
			'Dsp.libsecactdomi',
			'Dsp.libactdomi',
			'Dsp.libsecactrech',
			'Dsp.libemploirech',
			'Dsp.libsecactderact',
			'Dsp.libderact'
		);

		/**
		 *
		 */
		public function beforeFilter() {
			$return = parent::beforeFilter();

			$this->set( 'cg', Configure::read( 'nom_form_ci_cg' ) ); // FIXME

			return $return;
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

		/**
		 *   Fonction permettant d'accéder à la page pour lier les fichiers à l'Orientation
		 */
		public function filelink( $id ) {
			$this->WebrsaAccesses->check($id);
            $this->assert( valid_int( $id ), 'invalidParameter' );

			$fichiers = array( );

			$dsprev = $this->DspRev->find(
				'first',
				array(
					'conditions' => array(
						'DspRev.id' => $id
					),
					'contain' => array(
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						)
					)
				)
			);

			$optionsrevs = (array)Hash::get( $this->DspRev->enums(), 'DspRev' );

			$personne_id = $dsprev['DspRev']['personne_id'];
			$dsp_id = $dsprev['DspRev']['dsp_id'];

			$dossier_id = $this->Dsp->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'histo', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Dsp->begin();

				$saved = $this->DspRev->updateAllUnBound(
						array( 'DspRev.haspiecejointe' => '\''.$this->request->data['DspRev']['haspiecejointe'].'\'' ), array(
					'"DspRev"."personne_id"' => $personne_id,
					'"DspRev"."dsp_id"' => $dsp_id,
					'"DspRev"."id"' => $id
						)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "DspRev.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Dsp->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( $this->referer() );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Dsp->commit();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'optionsrevs', 'dsprev' ) );
			$this->set( 'urlmenu', '/dsps/histo/'.$personne_id );
		}

		/**
		 *
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */
		public function edit($personne_id, $id = null) {
			$this->WebrsaAccesses->check($id, $personne_id);
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */
		public function view( $id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $id ) ) );

			$this->_setEntriesAncienDossier( $id, 'Dsp' );

			$query = $this->WebrsaDsp->completeVirtualFieldsForAccess(
				array(
					'conditions' => array(
						'Dsp.personne_id' => $id
					),
					'contain' => array(
						'Personne',
						'Libderact66Metier',
						'Libsecactderact66Secteur',
						'Libactdomi66Metier',
						'Libsecactdomi66Secteur',
						'Libemploirech66Metier',
						'Libsecactrech66Secteur',
						'Detaildifsoc',
						'Detailaccosocfam',
						'Detailaccosocindi',
						'Detaildifdisp',
						'Detailnatmob',
						'Detaildiflog',
						'Detailmoytrans',
						'Detaildifsocpro',
						'Detailprojpro',
						'Detailfreinform',
						'Detailconfort'
					)
				)
			);

			/**
			 * ATOLCD : methode personneId non trouvée depuis PHP 5.6
			 * On enlève l'appel à cette méthode.
			 * Supprimer la ligne commentée à partie de la version 3.4.0
			 */
			//$paramsAccess = $this->WebrsaDsp->getParamsForAccess($this->Dsp->personneId($id), WebrsaAccessDsps::getParamsList());
			$paramsAccess = $this->WebrsaDsp->getParamsForAccess($id, WebrsaAccessDsps::getParamsList());
			$this->set('ajoutPossible', Hash::get($paramsAccess, 'ajoutPossible'));

			$dsp = WebrsaAccessDsps::access($this->Dsp->find('first', $query), $paramsAccess);

			$qd_personne = array(
				'conditions' => array(
					'Personne.id' => $id
				),
				'fields' => array(
                    $this->Dsp->Personne->sqVirtualField( 'nom_complet' )
                ),
				'order' => null,
				'recursive' => -1
			);
			$personne = $this->Dsp->Personne->find( 'first', $qd_personne );

			$this->set( 'personne', $personne );
			$this->set( 'personne_id', $id );
			if( isset( $dsp['Dsp']['id'] ) ) {
				$dsp_id = $dsp['Dsp']['id'];
			}
			else {
				$dsp_id = 0;
			}
			$rev = false;

			$dspRev = $this->DspRev->find(
				'first',
				array(
					'conditions' => array(
						'dsp_id' => $dsp_id
					),
					'order' => array(
						'DspRev.id ASC',
						'DspRev.created ASC'
					),
					'recursive' => -1
				)
			);

			if( !empty( $dspRev ) ) {
				$rev = true;
			}
			$this->set( 'dsp', $dsp );
			$this->set( 'rev', $rev );
			$this->set( 'options', $this->Dsp->WebrsaDsp->options( array( 'find' => false ) ) );
		}

		/**
		 * Permet de visualiser les différentes versions des DSP d'un allocataire,
		 * ainsi que le nombre de différences entre avec la version précédente.
		 */
		public function histo( $id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $id ) ) );

			$dsp = $this->Dsp->Personne->find(
				'first',
				array(
					'conditions' => array(
						'Personne.id' => $id
					),
					'contain' => array(
						'Dsp' => array(
							'Detaildifsoc',
							'Detailaccosocfam',
							'Detailaccosocindi',
							'Detaildifdisp',
							'Detailnatmob',
							'Detaildiflog',
							'Detailmoytrans',
							'Detaildifsocpro',
							'Detailprojpro',
							'Detailfreinform',
							'Detailconfort'
						)
					)
				)
			);
			$this->assert( !empty( $dsp ), 'invalidParameter' );

			$query = $this->WebrsaDsp->getViewQuery();
			$query['conditions'] = array( 'DspRev.personne_id' => $id );
			$query['order'] = array( 'DspRev.created DESC', 'DspRev.id DESC' );

			/**
			 * Contrôle d'accès
			 */
			$actionsParams = WebrsaAccessDsps::getParamsList();
			$paramsAccess = $this->WebrsaDsp->getParamsForAccess($id, $actionsParams);
			$ajoutPossible = Hash::get($paramsAccess, 'ajoutPossible') !== false;
			$histos = WebrsaAccessDsps::accesses($this->WebrsaDsp->getDataForAccess(array('DspRev.personne_id' => $id)), $paramsAccess);

			$this->set( array( 'dsp' => $dsp, 'histos' => $histos, 'personne_id' => $id, 'ajoutPossible' => $ajoutPossible ) );
		}

		/**
		 * Permet d'ajouter une nouvelle version des DspRev à partir d'une copie
		 * plus ancienne.
		 *
		 * @param integer $id L'id de l'entrée des DspRev qu'il faut copier.
		 * @throws NotFoundException
		 */
		public function revertTo( $id = null ) {
			$this->WebrsaAccesses->check($id);
			$belongsToRomev3 = $this->DspRev->belongsTo;
			foreach( $belongsToRomev3 as $alias => $params ) {
				if( strpos( $alias, 'romev3Rev' ) === false ) {
					unset( $belongsToRomev3[$alias] );
				}
			}

			$query = array(
				'conditions' => array(
					'DspRev.id' => $id
				),
				'contain' => array_merge(
					array_keys( $this->DspRev->hasMany ),
					array_keys( $this->DspRev->hasOne ),
					array_keys( $belongsToRomev3 )
				)
			);

			$record = $this->DspRev->find( 'first', $query );

			if( empty( $record ) ) {
				throw new NotFoundException();
			}

			$personne_id = Hash::get( $record, 'DspRev.personne_id' );
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$dossier_id = Hash::get( $dossierMenu, 'Dossier.id' );

			// INFO: on obtient un jeton qui sera traité dans la méthode edit()
			$this->Jetons2->get( $dossier_id );

			// Nettoyage des champs
			$fieldNames = array( 'id', 'created', 'modified' );
			foreach( $fieldNames as $fieldName ) {
				unset( $record['DspRev'][$fieldName] );
			}
			foreach( array_keys( $this->DspRev->hasMany ) as $alias ) {
				foreach( array_keys( $record[$alias] ) as $key ) {
					foreach( array_merge( $fieldNames, array( 'dsp_rev_id' ) ) as $fieldName ) {
						unset( $record[$alias][$key][$fieldName] );
					}
				}
			}

			// Remplacement des alias XxxRev en Xxx
			foreach( $record as $alias => $values ) {
				$newAlias = preg_replace( '/Rev$/', '', $alias );
				if( $alias !== $newAlias ) {
					$record[$newAlias] = $record[$alias];
					unset( $record[$alias] );
				}
			}

			$record['Dsp']['id'] = $record['Dsp']['dsp_id'];
			unset( $record['Dsp']['dsp_id'] );

			// INFO: on ne copie pas Fichiermodule
			unset( $record['Fichiermodule'] );

			// Enregistrements ROME V3 dans la table entreesromesv3, à copier
			foreach( $belongsToRomev3 as $alias => $params ) {
				$alias = preg_replace( '/Rev$/', '', $alias );
				$record['Dsp'][$params['foreignKey']] = null;
				foreach( $fieldNames as $fieldName ) {
					unset( $record[$alias][$fieldName] );
				}
			}

			$this->request->data = $record;
			$this->edit( $personne_id, $id );
		}

		/**
		 * Visualisation d'une version particulière des DspRev.
		 */
		public function view_revs( $id = null ) {
			$this->WebrsaAccesses->check($id);
			$query = $this->WebrsaDsp->completeVirtualFieldsForAccess($this->WebrsaDsp->getViewQuery());
			$query['conditions'] = array( 'DspRev.id' => $id );

			/**
			 * ATOLCD : methode personneId non trouvée depuis PHP 5.6
			 * On enlève donc l'appel à la méthode : $this->Dsp->personneId($id)
			 *
			 * Le premier argument de getParamsForAccess est $personne_id qui est ici $id.
			 */
			$paramAccess = $this->WebrsaDsp->getParamsForAccess($id, WebrsaAccessDsps::getParamsList());
			$dsprevs = WebrsaAccessDsps::access($this->DspRev->find('first', $query), $paramAccess);

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $dsprevs['DspRev']['personne_id'] ) ) );

			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'histo', $dsprevs['DspRev']['personne_id'] ) );
			}

			$this->_setEntriesAncienDossier( $dsprevs['DspRev']['personne_id'], 'DspRev' );

			$dsp = array( );
			// Suppression du suffixe Rev pour utiliser la même vue que les Dsp
			foreach( $dsprevs as $key => $value ) {
				$key = preg_replace( '/Rev$/', '', $key );
				$dsp[$key] = $value;
			}

			$this->assert( !empty( $dsp ), 'invalidParameter' );

			$this->set( 'dsp', $dsp );

			$this->set( 'personne_id', $dsprevs['DspRev']['personne_id'] );
			$personne = $dsprevs; // Pour récupérer les informations de la personne
			$this->set( 'personne', $personne );
			$this->set( 'urlmenu', '/dsps/histo/'.$dsprevs['DspRev']['personne_id'] );
			$this->set( 'options', $this->Dsp->WebrsaDsp->options( array( 'find' => false ) ) );

			$this->render( 'view' );
		}

		/**
		 *
		 */
		public function view_diff( $id = null ) {
			$this->WebrsaAccesses->check($id);
			$base = $this->WebrsaDsp->getViewQuery();

			$query = $base;
			$query['conditions'] = array( 'DspRev.id' => $id );

			$dsprevact = $this->DspRev->find( 'first', $query );
			$this->assert( !empty( $dsprevact ), 'invalidParameter' );
			$personne_id = $dsprevact['Personne']['id'];

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $dsprevact['Personne']['id'] ) ) );

			// -----------------------------------------------------------------

			$query = $base;
			$query['conditions'] = array(
				'DspRev.personne_id' => $dsprevact['DspRev']['personne_id'],
				'DspRev.created <=' => $dsprevact['DspRev']['created'],
				'DspRev.id <' => $dsprevact['DspRev']['id']
			);
			$query['order'] = array( 'DspRev.created DESC', 'DspRev.id DESC' );

			$dsprevold = $this->DspRev->find( 'first', $query );
			$this->assert( !empty( $dsprevold ), 'invalidParameter' );

			$diff = $this->WebrsaDsp->getDiffs($dsprevold, $dsprevact);

			$this->set( 'personne', $this->findPersonne( Set::classicExtract( $dsprevact, 'DspRev.personne_id' ) ) );
			$this->set( 'dsprevact', $dsprevact );
			$this->set( 'dsprevold', $dsprevold );
			$this->set( 'diff', $diff );

			if( Configure::read( 'Romev3.enabled' ) ) {
				$prefixes = $this->Dsp->WebrsaDsp->prefixesRomev3;
				$suffixes = $this->Dsp->WebrsaDsp->suffixesRomev3;
				$this->set( compact( 'prefixes', 'suffixes' ) );
			}

			$this->set( 'personne_id', Set::classicExtract( $dsprevact, 'DspRev.personne_id' ) );
			$this->set( 'urlmenu', '/dsps/histo/'.Set::classicExtract( $dsprevact, 'DspRev.personne_id' ) );
			$this->set( 'options', $this->Dsp->WebrsaDsp->options( array( 'find' => false ) ) );
		}

		/**
		 *
		 */
		public function findPersonne( $personne_id ) {
			return $this->Dsp->Personne->find(
				'first',
				array(
					'fields' => array(
						'Personne.id',
						'Personne.qual',
						'Personne.nom',
						'Personne.prenom',
                        $this->Dsp->Personne->sqVirtualField( 'nom_complet' )
					),
					'conditions' => array(
						'Personne.id' => $personne_id
					),
					'recursive' => -1
				)
			);
		}

		/**
		 *
		 */
		protected function _add_edit( $personne_id = null, $version_id = null ) {
			$dossier_id = $this->Dsp->Personne->dossierId( $personne_id );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );

				if( empty( $version_id ) ) {
					$this->redirect( array( 'action' => 'view', $personne_id ) );
				}
				else {
					$this->redirect( array( 'action' => 'histo', $personne_id ) );
				}
			}

			// On cherche soit la dsp directement, soit la personne liée
			$dsp = null;
			if( ( ( $this->action == 'edit' || $this->action == 'revertTo' ) ) && !empty( $personne_id ) ) {
				if( empty( $version_id ) ) {

					$qd_dsp = array(
						'conditions' => array(
							'Dsp.personne_id' => $personne_id
						)
					);
					$dsp = $this->Dsp->find( 'first', $qd_dsp );
					if( empty( $dsp ) ) {
						$qd_dsp = array(
							'conditions' => array(
								'Personne.id' => $personne_id
							)
						);
						$dsp = $this->Dsp->Personne->find( 'first', $qd_dsp );
					}
				}
				else {
					$dsprevs = $this->DspRev->find(
						'first',
						array(
							'conditions' => array(
								'DspRev.id' => $version_id
							),
							'contain' => array(
								'Personne',
								'Libderact66Metier',
								'Libsecactderact66Secteur',
								'Libactdomi66Metier',
								'Libsecactdomi66Secteur',
								'Libemploirech66Metier',
								'Libsecactrech66Secteur',
								'DetaildifsocRev',
								'DetailaccosocfamRev',
								'DetailaccosocindiRev',
								'DetaildifdispRev',
								'DetailnatmobRev',
								'DetaildiflogRev',
								'DetailmoytransRev',
								'DetaildifsocproRev',
								'DetailprojproRev',
								'DetailfreinformRev',
								'DetailconfortRev',
								'Fichiermodule',
								'Deractromev3Rev',
								'Deractdomiromev3Rev',
								'Actrechromev3Rev'
							)
						)
					);
					$dsp_id = $dsprevs['DspRev']['dsp_id'];
					foreach( $dsprevs as $key => $value ) {
						$key = preg_replace( '/Rev$/', '', $key );
						$dsp[$key] = $value;
					}
					$dsp['Dsp']['id'] = $dsp_id;
				}
			}
			else if( ( $this->action == 'add' ) && !empty( $personne_id ) ) {

				$qd_dsp = array(
					'conditions' => array(
						'Personne.id' => $personne_id
					),
					'fields' => null,
					'order' => null,
					'contain' => array(
						'Dsp'
					)
				);
				$dsp = $this->Dsp->Personne->find( 'first', $qd_dsp );

			}

			// Vérification indirecte de l'id
			$this->assert( !empty( $dsp ), 'invalidParameter' );

			// Tentative d'enregistrement
			if( !empty( $this->request->data ) ) {
				$this->Dsp->begin();

				$success = true;

				// Nettoyage des Dsp
				$keys = array_keys( $this->Dsp->schema() );
				$defaults = array_combine( $keys, array_fill( 0, count( $keys ), null ) );
				unset( $defaults['id'] );

				$this->request->data['Dsp'] = Set::merge( $defaults, $this->request->data['Dsp'] );
				foreach( $this->request->data['Dsp'] as $key => $value ) {
					if( strlen( trim( $value ) ) == 0 ) {
						$this->request->data['Dsp'][$key] = null;
					}
				}

				// Modèles liés, début hasMany spéciaux
				$deleteConditions = array( );
				$valuesNone = $this->Dsp->WebrsaDsp->getCheckboxesValuesNone();

				foreach( $this->Dsp->WebrsaDsp->getCheckboxesVirtualFields() as $fieldName ) {
					list( $model, $checkbox ) = model_field( $fieldName );
					$values = Set::classicExtract( $this->request->data, "{$model}" );

					if( isset( $valuesNone[$model] ) && $valuesNone[$model] !== null ) {
						$tmpValues = Set::extract( $values, "/{$checkbox}" );
						$cKey = array_search( $valuesNone[$model], $tmpValues );
						$tmpValues = $values;
						if( $cKey !== false ) {
							unset( $tmpValues[$cKey] );// FIXME
							$ids = Set::extract( $tmpValues, '/id' );
							foreach( $ids as $id ) {
								$deleteConditions[$model][] = "{$model}.id = {$id}";
							}
						}
						// FIXME: s'assurer que les autres soient à 0 ?
					}

					foreach( $values as $key => $value ) {
						$val = Set::classicExtract( $value, $checkbox );
						if( empty( $val ) ) {
							if( isset( $value['id'] ) ) {
								$deleteConditions[$model][] = "{$model}.id = {$value['id']}";
							}
							unset( $this->request->data[$model][$key] );
						}
					}
				}

				foreach( $deleteConditions as $model => $values ) {
					if( !empty( $values ) ) {
						$this->Dsp->{$model}->deleteAll( array( 'or' => $values ) );
					}
				}
				// fin hasMany spéciaux

				$dsp_id = Set::classicExtract( $this->request->data, 'Dsp.id' );
				$this->request->data = Hash::filter( (array)$this->request->data );

				$data2 = null;

				unset( $this->request->data['Dsp']['haspiecejointe'] );
				if( $success = $this->Dsp->saveAll( $this->request->data, array( 'atomic' => false, 'validate' => 'only' ) ) && $success ) {
					if( $this->action == 'add' ) {
						$success = $this->Dsp->saveAll( $this->request->data, array( 'atomic' => false, 'validate' => 'first' ) ) && $success;
					}
					foreach( $this->request->data as $Model => $values ) {
						$data2[$Model."Rev"] = $this->request->data[$Model];

						if( in_array( $Model, array( 'Deractromev3', 'Deractdomiromev3', 'Actrechromev3' ) ) ) {
							$data2 = Hash::remove( $data2, $Model."Rev.id" );
						}
						else if( !in_array( $Model, array( 'Dsp', 'Personne' ) ) ) {
							foreach( $data2[$Model."Rev"] as $key => $value ) {
								if( isset( $data2[$Model."Rev"][$key]['dsp_id'] ) )
									$data2[$Model."Rev"][$key]['dsp_rev_id'] = $data2[$Model."Rev"][$key]['dsp_id'];
								$data2 = Hash::remove( $data2, $Model."Rev.".$key.".dsp_id" );
								$data2 = Hash::remove( $data2, $Model."Rev.".$key.".id" );
							}
						}
					}
					$data2['DspRev']['dsp_id'] = $this->Dsp->id;
					$data2 = Hash::remove( $data2, 'DspRev.id' );

					$this->DspRev->saveAll( $data2, array( 'atomic' => false, 'validate' => 'first' ) );

					$this->Flash->success( __( 'Save->success' ) );
					// Fin de la transaction
					$this->Dsp->commit();
					$this->Jetons2->release( $dossier_id );
					$this->redirect( array( 'action' => 'histo', Set::classicExtract( $this->request->data, 'Dsp.personne_id' ) ) );
				}
				else {
					$this->Flash->error( __( 'Save->error' ) );
					$this->Dsp->rollback();
				}
			}
			// Affectation au formulaire
			else if( $this->action == 'edit' ) {
				$libderact66 = $dsp['Dsp']['libsecactderact66_secteur_id'].'_'.$dsp['Dsp']['libderact66_metier_id'];
				$libactdomi66 = $dsp['Dsp']['libsecactdomi66_secteur_id'].'_'.$dsp['Dsp']['libactdomi66_metier_id'];
				$libemploirech66 = $dsp['Dsp']['libsecactrech66_secteur_id'].'_'.$dsp['Dsp']['libemploirech66_metier_id'];
				$dsp['Dsp']['libderact66_metier_id'] = preg_match('/_$/', $libderact66) ? '' : $libderact66;
				$dsp['Dsp']['libactdomi66_metier_id'] = preg_match('/_$/', $libactdomi66) ? '' : $libactdomi66;
				$dsp['Dsp']['libemploirech66_metier_id'] = preg_match('/_$/', $libemploirech66) ? '' : $libemploirech66;

				// Début ROME V3
				foreach( array( 'Deractromev3', 'Deractdomiromev3', 'Actrechromev3' ) as $alias ) {
					$dsp = $this->Dsp->{$alias}->prepareFormDataAddEdit( $dsp );
				}
				// Fin ROME V3
				$this->request->data = $dsp;
			}

			// Affectation à la vue
			$this->set( 'dsp', $dsp );
			$this->set( 'personne_id', $dsp['Dsp']['personne_id'] );
			$this->set( 'urlmenu', ( $this->action === 'edit' ? "/dsps/edit/{$dsp['Dsp']['personne_id']}" : "/dsps/histo/{$dsp['Dsp']['personne_id']}" ) );

			// Options
			$options = $this->Dsp->WebrsaDsp->options();
			$this->set( compact( 'options' ) );

			// Valeurs spéciales "Aucun(e)"
			$valuesNone = $this->Dsp->WebrsaDsp->getCheckboxesValuesNone();
			$checkboxes = $this->Dsp->WebrsaDsp->getCheckboxes();
			$this->set( compact( 'checkboxes', 'valuesNone' ) );

			$this->render( '_add_edit' );
		}

		public function search() {
			$this->loadModel( 'Personne' );

			$Recherches = $this->Components->load( 'WebrsaRecherchesDsps' );
			$Recherches->search( array( 'modelName' => 'Personne' ) );
		}

		public function exportcsv() {
			$this->loadModel( 'Personne' );

			$Recherches = $this->Components->load( 'WebrsaRecherchesDsps' );
			$Recherches->exportcsv( array( 'modelName' => 'Personne' ) );
		}

		/**
		 * Ajoute les caractères '*' devant et derrière les valeurs non
		 * vides pour les clés qui ont été définies.
		 *
		 * @param array $data Un array de profondeur quelconque venant
		 *  d'un formulaire de recherche.
		 * @param mixed $wildcardKeys Soit une liste de clés, soit la
		 *  valeur true pour appliquer sur toutes les clés.
		 * @return array
		 */
		protected function _wildcardKeys( $data, $wildcardKeys ) {
			$search = array( );
			foreach( Hash::flatten( $data ) as $key => $value ) {
				$keyNeedsWildcard = (
						$wildcardKeys === true
						|| ( is_array( $wildcardKeys ) && in_array( $key, $wildcardKeys ) )
						);
				if( $keyNeedsWildcard && (!is_null( $value ) && trim( $value ) != '' ) ) {
					$search[$key] = "*{$value}*";
				}
				else {
					$search[$key] = $value;
				}
			}
			return Hash::expand( $search );
		}
	}
?>