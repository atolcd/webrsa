<?php
	/**
	 * Code source de la classe Proposdecisionscuis66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Proposdecisionscuis66Controller ... (CG 66).
	 *
	 * @package app.Controller
	 */
	class Proposdecisionscuis66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Proposdecisionscuis66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Default',
			'Default2',
			'Fileuploader',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Propodecisioncui66',
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
			'ajaxtaux',
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
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'filelink' => 'read',
			'fileview' => 'read',
			'maillink' => 'read',
			'notifelucui' => 'read',
			'printaviscui' => 'update',
			'propositioncui' => 'read',
		);
		
		/**
		 *
		 */
		protected function _setOptions() {
			$options = $this->Propodecisioncui66->enums();

			$this->set( 'qual', $this->Option->qual() );
			$options = Set::merge(
				$this->Propodecisioncui66->Cui->enums(),
				$options
			);
			$this->set( 'options', $options );
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
		 *   Téléchargement des fichiers préalablement associés à un traitement donné
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 * Fonction permettant d'accéder à la page pour lier les fichiers auw avis techniques
		 */
		public function filelink( $id ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Propodecisioncui66->personneId( $id ) ) ) );

			$fichiers = array( );
			$propodecisioncui66 = $this->Propodecisioncui66->find(
				'first',
				array(
					'conditions' => array(
						'Propodecisioncui66.id' => $id
					),
					'contain' => array(
						'Cui',
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						)
					)
				)
			);

			$personne_id = $propodecisioncui66['Cui']['personne_id'];
			$cui_id = $propodecisioncui66['Cui']['id'];
			$dossier_id = $this->Propodecisioncui66->Cui->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'propositioncui', $cui_id ) );
			}

			if( !empty( $this->request->data ) ) {
                $this->Propodecisioncui66->begin();
				$saved = $this->Propodecisioncui66->updateAllUnBound(
					array( 'Propodecisioncui66.haspiecejointe' => '\''.$this->request->data['Propodecisioncui66']['haspiecejointe'].'\'' ),
					array(
						'"Propodecisioncui66"."cui_id"' => $cui_id,
						'"Propodecisioncui66"."id"' => $id
					)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "Propodecisioncui66.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Propodecisioncui66->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( $this->referer() );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Propodecisioncui66->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}

			$this->_setOptions();
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'propodecisioncui66' ) );
			$this->set( 'urlmenu', '/proposdecisionscuis66/propositioncui/'.$cui_id );
		}


		/**
		 *
		 * @param integer $cui_id
		 */
		public function propositioncui( $cui_id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Propodecisioncui66->Cui->personneId( $cui_id ) ) ) );

			$nbrCuis = $this->Propodecisioncui66->Cui->find( 'count', array( 'conditions' => array( 'Cui.id' => $cui_id ), 'recursive' => -1 ) );
			$this->assert( ( $nbrCuis == 1 ), 'invalidParameter' );

			$cui = $this->Propodecisioncui66->Cui->find(
				'first',
				array(
					'conditions' => array(
						'Cui.id' => $cui_id
					),
					'contain' => false,
					'recursive' => -1
				)
			);
			$personne_id = Set::classicExtract( $cui, 'Cui.personne_id' );

			$proposdecisionscuis66 = $this->Propodecisioncui66->find(
				'all',
				array(
					'fields' => array_merge(
						$this->Propodecisioncui66->fields(),
						array(
							$this->Propodecisioncui66->Fichiermodule->sqNbFichiersLies( $this->Propodecisioncui66, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Propodecisioncui66.cui_id' => $cui_id
					),
					'recursive' => -1,
					'contain' => false
				)
			);

			$this->_setOptions();
			$this->set( 'personne_id', $personne_id );
			$this->set( 'cui_id', $cui_id );
			$this->set( compact( 'proposdecisionscuis66' ) );
			$this->set( 'urlmenu', '/cuis/index/'.$personne_id );

			// Retour à la liste des CUI en cas de retour
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'cuis', 'action' => 'index', $personne_id ) );
			}
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
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 * @param integer $id
		 */
		protected function _add_edit( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			if( $this->action == 'add' ) {
				$cui_id = $id;
			}
			else if( $this->action == 'edit' ) {
				$propodecisioncui66_id = $id;
				$propodecisioncui66 = $this->Propodecisioncui66->find(
					'first',
					array(
						'conditions' => array(
							'Propodecisioncui66.id' => $propodecisioncui66_id
						),
						'contain' => array(
							'Cui'
						),
						'recursive' => -1
					)
				);
				$this->set( 'propodecisioncui66', $propodecisioncui66 );

				$cui_id = Set::classicExtract( $propodecisioncui66, 'Propodecisioncui66.cui_id' );
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Propodecisioncui66->Cui->personneId( $cui_id ) ) ) );


			// CUI en lien avec la proposition
			$cui = $this->Propodecisioncui66->Cui->find(
				'first',
				array(
					'conditions' => array(
						'Cui.id' => $cui_id
					),
					'contain' => false,
					'recursive' => -1
				)
			);

			$personne_id = Set::classicExtract( $cui, 'Cui.personne_id' );

			$this->set( 'personne_id', $personne_id );
			$this->set( 'cui', $cui );
			$this->set( 'cui_id', $cui_id );

			$dossier_id = $this->Propodecisioncui66->Cui->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'controller' => 'proposdecisionscuis66', 'action' => 'propositioncui', $cui_id ) );
			}

			// On récupère l'utilisateur connecté et qui exécute l'action
			$userConnected = $this->Session->read( 'Auth.User.id' );
			$this->set( compact( 'userConnected' ) );

			if ( !empty( $this->request->data ) ) {
				$this->Propodecisioncui66->begin();

				if( $this->Propodecisioncui66->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
					$saved = $this->Propodecisioncui66->save( $this->request->data );

					if( $saved ) {
						$saved = $this->Propodecisioncui66->Cui->updatePositionFromPropodecisioncui66( $this->Propodecisioncui66->id ) && $saved;
					}

					if( $saved ) {
						$this->Propodecisioncui66->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
						$this->redirect( array( 'controller' => 'proposdecisionscuis66', 'action' => 'propositioncui', $cui_id ) );
					}
					else {
						$this->Propodecisioncui66->rollback();
						$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
					}

				}
			}
			else{
				if( $this-> action == 'edit' ){
					$this->request->data = $propodecisioncui66;
				}
			}

			$this->_setOptions();
			$this->set( 'urlmenu', '/cuis/index/'.$personne_id );
			$this->render( 'add_edit' );
		}


		/**
		 * Imprime la notification pour récupérer l'avis de l'élu, suite à l'avis de la MNE
		 *
		 * @param integer $id L'id de la proposition du CUI que l'on veut imprimer
		 * @return void
		 */
		public function notifelucui( $id ) {
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Propodecisioncui66->personneId( $id ) ) );

			$pdf = $this->Propodecisioncui66->getNotifelucuiPdf( $id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ){
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'NotifElu_%d_%d.pdf', $id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer le courrier à destination de l\'élu.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Propodecisioncui66->personneId( $id ) ) );

			$this->Default->delete( $id );
		}


		/**
		 * Permet d'envoyer un mail au référent en lien avec la fiche de candidature
		 *
		 * @param integer $id
		 */
		public function maillink( $id = null ) {
			$personne_id = $this->Propodecisioncui66->personneId( $id );
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $personne_id ) );

			$sqDernierReferent = $this->Propodecisioncui66->Cui->Personne->PersonneReferent->sqDerniere( 'Personne.id' );

			$propodecisioncui66 = $this->Propodecisioncui66->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Propodecisioncui66->fields(),
						$this->Propodecisioncui66->Cui->fields(),
						$this->Propodecisioncui66->Cui->Personne->fields(),
						$this->Propodecisioncui66->Cui->Personne->PersonneReferent->Referent->fields()
					),
					'conditions' => array(
						'Propodecisioncui66.id' => $id
					),
					'joins' => array(
						$this->Propodecisioncui66->join( 'Cui', array( 'type' => 'INNER' ) ),
						$this->Propodecisioncui66->Cui->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Propodecisioncui66->Cui->Personne->join(
							'PersonneReferent',
							array(
								'type' => 'INNER',
								'conditions' => array(
									"PersonneReferent.id IN ( {$sqDernierReferent} )"
								)
							)
						),
						$this->Propodecisioncui66->Cui->Personne->PersonneReferent->join( 'Referent', array( 'type' => 'INNER') )
					)
				)
			);

			$this->assert( !empty( $propodecisioncui66 ), 'error404' );

			if( !isset( $propodecisioncui66['Referent']['email'] ) || empty( $propodecisioncui66['Referent']['email'] ) ) {
				$this->Session->setFlash( "Mail non envoyé: adresse mail du référent ({$propodecisioncui66['Referent']['nom']} {$propodecisioncui66['Referent']['prenom']}) non renseignée.", 'flash/error' );
				$this->redirect( $this->referer() );
			}

			// Envoi du mail
			$success = true;
			try {
				$configName = WebrsaEmailConfig::getName( 'avis_technique_cui' );
				$Email = new CakeEmail( $configName );

				// Choix du destinataire suivant l'environnement
				if( !WebrsaEmailConfig::isTestEnvironment() ) {
					$Email->to( $propodecisioncui66['Referent']['email'] );
				}
				else {
					$Email->to( WebrsaEmailConfig::getValue( 'avis_technique_cui', 'to', $Email->from() ) );
				}

				$Email->subject( WebrsaEmailConfig::getValue( 'avis_technique_cui', 'subject', 'Avis technique sur le CUI' ) );
				$mailBody = "Bonjour,\n\nle CUI de {$propodecisioncui66['Personne']['qual']} {$propodecisioncui66['Personne']['nom']} {$propodecisioncui66['Personne']['prenom']} a été saisi dans WEBRSA.";

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
		 * Imprime l'avis technique émis sur le CUI
		 *
		 * @param integer $id L'id de la proposition du CUI que l'on veut imprimer
		 * @return void
		 */
		public function printaviscui( $id ) {
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Propodecisioncui66->personneId( $id ) ) );

			$pdf = $this->Propodecisioncui66->getAvistechniquecuiPdf( $id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ){
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'AvisTechnique_%d_%d.pdf', $id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer le courrier d\'avis technique.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}
	}
?>
