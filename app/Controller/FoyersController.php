<?php
	/**
	* Code source de la classe FoyersController.php.
	*
	* PHP 5.3
	*
	* @package app.controllers
	* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	*/
	App::uses( 'AppController', 'Controller' );

	/**
	* La classe FoyersController.php ...
	*
	* @package app.controllers
	*/
	class FoyersController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Foyers';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Fileuploader',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Default2',
			'Fileuploader',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Foyer',
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
			'corbeille' => 'read',
			'download' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
		);

		protected function _setOptions() {
			$this->set( 'options', $this->Foyer->enums() );
			$this->set( 'gestionnaire', $this->User->find(
					'list', array(
						'fields' => array(
							'User.nom_complet'
						),
						'conditions' => array(
							'User.isgestionnaire' => 'O'
						)
					)
				)
			);
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
		 *   Téléchargement des fichiers préalablement associés à la corbeille du foyer
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 *   Fonction permettant d'accéder à la page pour lier les fichiers à la corbeille du foyer
		 */
		public function filelink( $id ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $id ) ) );
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$fichiers = array( );
			$foyer = $this->Foyer->find(
				'first', array(
					'conditions' => array(
						'Foyer.id' => $id
					),
					'contain' => array(
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						)
					)
				)
			);

			$dossier_id = $this->Foyer->dossierId( $id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'corbeille', $id ) );
			}
// debug($this->request);
			if( !empty( $this->request->data ) ) {
				$this->Foyer->begin();

				// Sauvegarde des fichiers liés à une PDO
				$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
				$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "Foyer.haspiecejointe" ), $id );

				if( $saved ) {
					$this->Foyer->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( $this->referer() );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Foyer->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			$this->set( 'urlmenu', '/foyers/index/'.$id );
			$this->_setOptions();
			$this->set( 'dossier_id', $dossier_id );
			$this->set( compact( 'id', 'fichiers', 'foyer' ) );
		}


		/**
		*
		*/

		public function corbeille( $id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $id ) ) );

			$hasFichierLie = $this->Foyer->find(
				'first',
				array(
					'fields' => array(
						$this->Foyer->Fichiermodule->sqNbFichiersLies( $this->Foyer, 'nb_fichiers_lies' )
					),
					'conditions' => array(
						'Foyer.id' => $id
					),
					'contain' => false
				)
			);
			$this->set( compact( 'hasFichierLie' ) );


			// Validation du formulaire de la corbeille
			if( !empty( $this->request->data ) ) {
				if( isset( $this->request->data['Foyer'] ) ) {
					$datas = array();
					foreach( $this->request->data['Foyer'] as $i => $tmp ) {
						if( $tmp['action'] === 'Valider' ) {
							$datas[$i] = array( 'Foyer' => $tmp );
						}
					}

					if( !empty( $datas ) ) {
						$this->Foyer->begin();
						$saved = true;
						foreach( $datas as $key => $data ) {
							$fk_value = suffix( $data['Foyer']['traitementpcg66_id'] );
							if( preg_match( '/^[0-9]+$/', $fk_value ) ) {
								$saved = $this->Foyer->Fichiermodule->updateAllUnBound(
									array(
										'Fichiermodule.fk_value' => $fk_value,
										'Fichiermodule.modele' => "'Traitementpcg66'"
									),
									array(
										'Fichiermodule.id' => $data['Foyer']['fichiermodule_id'],
										'Fichiermodule.modele' => 'Foyer'
									)
								) && $saved;

								$saved = $this->Foyer->Dossierpcg66->Personnepcg66->Traitementpcg66->updateAllUnBound(
									array(
										'Traitementpcg66.haspiecejointe' => "'1'"
									),
									array(
										'Traitementpcg66.id' => $fk_value
									)
								) && $saved;
							}
							else {
								$saved = false;
								$this->Foyer->validationErrors[$key]['traitementpcg66_id'] = 'Champ obligatoire';
							}
							if( empty( $data['Foyer']['dossierpcg66_id'] ) ) {
								$saved = false;
								$this->Foyer->validationErrors[$key]['dossierpcg66_id'] = 'Champ obligatoire';
							}
						}

						if( $saved ) {
							$this->Foyer->commit();
							$this->Flash->success( __( 'Save->success' ) );
							unset( $this->request->data['Foyer'] );
						}
						else {
							$this->Foyer->rollback();
							$this->Flash->error( __( 'Save->error' ) );
						}
					}
					else {
						$this->Flash->notice( 'Aucun élément à enregistrer' );
					}
				}
			}

			$listeCourriers = $this->Foyer->Fichiermodule->find(
				'all',
				array(
					'fields' => array(
						'Fichiermodule.id',
						'Fichiermodule.name',
						'Fichiermodule.created'
					),
					'conditions' => array(
						'Fichiermodule.fk_value' => $id,
						'Fichiermodule.modele' => 'Foyer'
					),
					'contain' => false
				)
			);
			$this->set( compact( 'listeCourriers' ) );

			$sqPersonnesIds = $this->Foyer->Personne->sq(
				array(
					'alias' => 'personnes',
					'fields' => array(
						'personnes.id',
					),
					'conditions' => array(
						'personnes.foyer_id' => $id
					),
					'contain' => false
				)
			);

			$personnespcgs66 = ClassRegistry::init( 'Personnepcg66' )->find(
				'all',
				array(
					'fields' => array(
						'Personnepcg66.id',
						'Personnepcg66.dossierpcg66_id',
					),
					'conditions' => array(
						"Personnepcg66.personne_id IN ( {$sqPersonnesIds} )"
					),
					'contain' => false
				)
			);
			$listDossierspcgs66 = (array)Set::extract( $personnespcgs66, '{n}.Personnepcg66.dossierpcg66_id' );
			$listPersonnespcgs66 = (array)Set::extract( $personnespcgs66, '{n}.Personnepcg66.id' );

			$options = array(
				'Dossierpcg66' => array( 'id' => array() ),
				'Traitementpcg66' => array( 'id' => array() ),
				'actions' => array( 'Valider' => 'Valider', 'En attente' => 'En attente' )
			);

			$dossierspcgs66 = array();
			$traitementspcgs66 = array();
			if( !empty( $listDossierspcgs66 ) ) {
				$dossierspcgs66 = $this->Foyer->Dossierpcg66->find(
					'all',
					array(
						'fields' => array(
							'Dossierpcg66.id',
							'Dossierpcg66.datereceptionpdo',
							'Typepdo.libelle',
							$this->Foyer->Dossierpcg66->User->sqVirtualField( 'nom_complet' )
						),
						'conditions' => array(
							'Dossierpcg66.id' => $listDossierspcgs66
						),
						'joins' => array(
							$this->Foyer->Dossierpcg66->join( 'Typepdo', array( 'type' => 'INNER' ) ),
							$this->Foyer->Dossierpcg66->join( 'User', array( 'type' => 'INNER' ) )
						),
						'contain' => false
					)
				);
				if( !empty( $dossierspcgs66 ) ) {
					foreach( $dossierspcgs66 as $dossierpcg66 ) {
						$options['Dossierpcg66']['id'][$dossierpcg66['Dossierpcg66']['id']] = $dossierpcg66['Typepdo']['libelle'].' ('.date_short( $dossierpcg66['Dossierpcg66']['datereceptionpdo'] ).')'.' géré par '.$dossierpcg66['User']['nom_complet'];
					}
				}

				$conditions = array(
					'Traitementpcg66.personnepcg66_id' => $listPersonnespcgs66,
					'Traitementpcg66.clos' => 'N'
				);
				$corbeillepcgDescriptionId = Configure::read( 'Corbeillepcg.descriptionpdoId' );
				if( !empty( $corbeillepcgDescriptionId ) ) {
					$conditions['Traitementpcg66.descriptionpdo_id'] = $corbeillepcgDescriptionId;
				}

				$traitementspcgs66 = $this->Foyer->Dossierpcg66->Personnepcg66->Traitementpcg66->find(
					'all',
					array(
						'fields' => array(
							'Traitementpcg66.id',
							'Traitementpcg66.datedepart',
							'Personnepcg66.dossierpcg66_id',
							'Personnepcg66.id',
							'Personnepcg66.personne_id',
							$this->Foyer->Personne->sqVirtualField( 'nom_complet' ),
							'Descriptionpdo.name',
							'Situationpdo.libelle'
						),
						'conditions' => $conditions,
						'joins' => array(
							$this->Foyer->Dossierpcg66->Personnepcg66->Traitementpcg66->join( 'Personnepcg66', array( 'type' => 'INNER' ) ),
							$this->Foyer->Dossierpcg66->Personnepcg66->join( 'Personne', array( 'type' => 'INNER' ) ),
							$this->Foyer->Dossierpcg66->Personnepcg66->Traitementpcg66->join( 'Descriptionpdo', array( 'type' => 'INNER' ) ),
							$this->Foyer->Dossierpcg66->Personnepcg66->Traitementpcg66->join( 'Situationpdo', array( 'type' => 'LEFT OUTER' ) )
						),
						'contain' => false
					)
				);

				if( !empty( $traitementspcgs66 ) ) {
					foreach( $traitementspcgs66 as $traitementpcg66 ) {
						$options['Traitementpcg66']['id']["{$traitementpcg66['Personnepcg66']['dossierpcg66_id']}_{$traitementpcg66['Traitementpcg66']['id']}"] = $traitementpcg66['Situationpdo']['libelle'].', '.$traitementpcg66['Personne']['nom_complet'].', ('.date_short( $traitementpcg66['Traitementpcg66']['datedepart'] ).')';
					}
				}
			}

			$this->_setOptions();

			$this->set( 'options', $options );

			$this->set( 'foyer_id', $id );
		}
	}
?>