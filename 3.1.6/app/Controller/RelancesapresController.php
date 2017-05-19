<?php
	/**
	 * Code source de la classe RelancesapresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe RelancesapresController ...
	 *
	 * @package app.Controller
	 */
	class RelancesapresController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Relancesapres';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Gedooo.Gedooo',
			'Jetons2',
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
			'Acccreaentr',
			'Acqmatprof',
			'Actprof',
			'Amenaglogt',
			'Locvehicinsert',
			'Option',
			'Permisb',
			'Personne',
			'Prestation',
			'Relanceapre',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Relancesapres:edit',
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
			'impression' => 'read',
			'view' => 'read',
		);

		/**
		 *
		 */
		public function beforeFilter() {
			parent::beforeFilter();
			$this->set( 'options', (array)Hash::get( $this->Relanceapre->enums(), 'Relanceapre' ) );
			$piecesapre = $this->Apre->Pieceapre->find( 'list' );
			$this->set( 'piecesapre', $piecesapre );
			$this->set( 'natureAidesApres', $this->Option->natureAidesApres() );
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

			// Récupération des id afférents
			if( $this->action == 'add' ) {
				$apre_id = $id;

				$apre = $this->Apre->find( 'first', array( 'conditions' => array( 'Apre.id' => $id ) ) );
				$this->set( 'apre', $apre );

				$personne_id = Set::classicExtract( $apre, 'Apre.personne_id' );
				$dossier_id = $this->Personne->dossierId( Set::classicExtract( $apre, 'Apre.personne_id' ) );
			}
			else if( $this->action == 'edit' ) {
				$relanceapre_id = $id;
				$qd_relanceapre = array(
					'conditions' => array(
						'Relanceapre.id' => $relanceapre_id
					),
					'fields' => array_merge(
						$this->Relanceapre->fields(),
						array(
							'Apre.personne_id'
						)
					),
					'order' => null,
					'contain' => array(
						'Apre'
					)
				);
				$relanceapre = $this->Relanceapre->find( 'first', $qd_relanceapre );


				$this->assert( !empty( $relanceapre ), 'invalidParameter' );

				$personne_id = Set::classicExtract( $relanceapre, 'Apre.personne_id' );
				$apre = $this->Apre->find( 'first', array( 'conditions' => array( 'Apre.personne_id' => $personne_id ) ) );
				$this->set( 'apre', $apre );
				$dossier_id = $this->Personne->dossierId( $personne_id );
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$this->assert( !empty( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossier_id', $dossier_id );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'controller' => 'apres', 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Relanceapre->begin();

				if( $this->Relanceapre->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
					$saved = $this->Relanceapre->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) );
					if( $saved ) {
						$this->Relanceapre->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
						$this->redirect( array( 'controller' => 'apres', 'action' => 'index', Set::classicExtract( $apre, 'Apre.personne_id' ) ) );
					}
					else {
						$this->Relanceapre->rollback();
						$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
					}
				}
				else {
					$this->Relanceapre->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else {
				if( $this->action == 'edit' ) {
					$this->request->data = $relanceapre;
				}
			}

			$this->render( 'add_edit' );
		}

		/**
		 *
		 * @param integer $relanceapre_id
		 */
		public function view( $relanceapre_id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Relanceapre->personneId( $relanceapre_id ) ) ) );

			$qd_relanceapre = array(
				'conditions' => array(
					'Relanceapre.id' => $relanceapre_id
				)
			);
			$relanceapre = $this->Relanceapre->find( 'first', $qd_relanceapre );

			$this->assert( !empty( $relanceapre ), 'invalidParameter' );

			$qd_apre = array(
				'conditions' => array(
					'Apre.personne_id' => Set::classicExtract( $relanceapre, 'Relanceapre.apre_id' )
				)
			);
			$apre = $this->Apre->find( 'first', $qd_apre );

			$this->set( 'apre', $apre );

			$this->set( 'relanceapre', $relanceapre );
			$this->set( 'personne_id', Set::classicExtract( $relanceapre, 'Relanceapre.apre_id' ) );
		}

		/**
		 * Génère l'impression d'une relance d'APRE pour le CG 93.
		 * On prend la décision de ne pas le stocker.
		 *
		 * @param integer $id L'id de la relance d'APRE que l'on veut imprimer.
		 * @return void
		 */
		public function impression( $id = null ) {
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Relanceapre->personneId( $id ) ) );

			$pdf = $this->Relanceapre->getDefaultPdf( $id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'relanceapre_%d-%s.pdf', $id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer l\'impression de la relance de l\'APRE.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}

	}
?>