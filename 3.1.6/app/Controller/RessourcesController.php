<?php
	/**
	 * Code source de la classe RessourcesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	 App::uses('WebrsaAccessRessource', 'Utility');

	/**
	 * La classe RessourcesController permet de gérer les ressources d'un allocataire.
	 *
	 * @package app.Controller
	 */
	class RessourcesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Ressources';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Ressource',
			'Detailressourcemensuelle',
			'Option',
			'Personne',
			'Ressourcemensuelle',
			'WebrsaRessource',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Ressources:edit',
			'view' => 'Ressources:index',
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
			'index' => 'read',
			'view' => 'read',
		);
		
		/**
		 *
		 */
		public function beforeFilter() {
			$return = parent::beforeFilter();
			$this->set( 'natress', ClassRegistry::init('Detailressourcemensuelle')->enum('natress') );
			$this->set( 'abaneu', ClassRegistry::init('Detailressourcemensuelle')->enum('abaneu') );
			return $return;
		}

		/**
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id = null ) {
			// Vérification du format de la variable
			$this->assert( valid_int( $personne_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$ressources = $this->WebrsaAccesses->getIndexRecords(
				$personne_id, array(
					'conditions' => array(
						'Ressource.personne_id' => $personne_id
					),
					'contain' => array(
						'Ressourcemensuelle' => array(
							'Detailressourcemensuelle'
						)
					)
				)
			);

			foreach( $ressources as $i => $ressource ) {
				$ressources[$i]['Ressource']['avg'] = $this->Ressource->moyenne( $ressource );
			}
			$this->set( 'ressources', $ressources );
			$this->set( 'personne_id', $personne_id );
		}

		/**
		 *
		 * @param integer $ressource_id
		 */
		public function view( $ressource_id = null ) {
			$this->WebrsaAccesses->check($ressource_id);
			// Vérification du format de la variable
			$this->assert( valid_int( $ressource_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Ressource->personneId( $ressource_id ) ) ) );

			$ressource = $this->Ressource->find(
					'first', array(
				'conditions' => array(
					'Ressource.id' => $ressource_id
				),
				'contain' => array(
					'Ressourcemensuelle' => array(
						'Detailressourcemensuelle'
					)
				)
					)
			);
			$this->assert( !empty( $ressource ), 'invalidParameter' );

			$ressource['Ressource']['avg'] = $this->Ressource->moyenne( $ressource );

			$this->set( 'ressource', $ressource );
			$this->set( 'personne_id', $ressource['Ressource']['personne_id'] );
			$this->set( 'urlmenu', '/ressources/index/'.$ressource['Ressource']['personne_id'] );
		}

		/**
		 *
		 * @param integer $personne_id
		 */
		public function add( $personne_id = null ) {
			$this->WebrsaAccesses->check(null, $personne_id);
			// Vérification du format de la variable
			$this->assert( valid_int( $personne_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$qd_personne = array(
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$personne = $this->Personne->find( 'first', $qd_personne );
			$this->assert( !empty( $personne ), 'invalidParameter' );

			$dossier_id = $this->Personne->dossierId( $personne_id );

			$this->Jetons2->get( $dossier_id );

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Ressource->begin();

				$this->request->data['Ressource']['topressnul'] = !$this->request->data['Ressource']['topressnotnul'];
				$this->Ressource->set( $this->request->data['Ressource'] );

				$validates = $this->Ressource->validates();
				if( isset( $this->request->data['Ressourcemensuelle'] ) && isset( $this->request->data['Detailressourcemensuelle'] ) ) {
					$validates = $this->Ressourcemensuelle->saveAll( $this->request->data['Ressourcemensuelle'], array( 'validate' => 'only' ) ) && $validates;
					$validates = $this->Detailressourcemensuelle->saveAll( $this->request->data['Detailressourcemensuelle'], array( 'validate' => 'only' ) ) && $validates;
				}

				if( $validates ) {
					$saved = $this->Ressource->save( $this->request->data );
					if( isset( $this->request->data['Ressourcemensuelle'] ) ) {
						foreach( $this->request->data['Ressourcemensuelle'] as $index => $dataRm ) {
							$dataRm['ressource_id'] = $this->Ressource->id;
							$this->Ressourcemensuelle->create();
							$saved = $this->Ressourcemensuelle->save( $dataRm ) && $saved;
							if( isset( $this->request->data['Detailressourcemensuelle'] ) ) {
								$dataDrm = $this->request->data['Detailressourcemensuelle'][$index];
								$dataDrm['ressourcemensuelle_id'] = $this->Ressourcemensuelle->id;
								$this->Detailressourcemensuelle->create();
								$saved = $this->Detailressourcemensuelle->save( $dataDrm ) && $saved;
							}
						}
					}
					if( $saved ) {
						$this->Ressource->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
						$this->redirect( array( 'controller' => 'ressources', 'action' => 'index', $personne_id ) );
					}
					else {
						$this->Ressource->rollback();
						$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
					}
				}
				else {
					$this->Ressource->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}

			$qd_ressource = array(
				'conditions' => array(
					'Ressource.personne_id' => $personne_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$ressource = $this->Ressource->find( 'first', $qd_ressource );

			$this->set( 'personne_id', $personne_id );
			$this->render( 'add_edit' );
		}

		/**
		 *
		 * @param integer $ressource_id
		 */
		public function edit( $ressource_id = null ) {
			$this->WebrsaAccesses->check($ressource_id);
			// Vérification du format de la variable
			$this->assert( valid_int( $ressource_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Ressource->personneId( $ressource_id ) ) ) );

			$qd_ressource = array(
				'conditions' => array(
					'Ressource.id' => $ressource_id
				),
				'fields' => null,
				'order' => null,
				'contain' => array(
					'Ressourcemensuelle' => array(
						'Detailressourcemensuelle'
					)
				)
			);
			$ressource = $this->Ressource->find( 'first', $qd_ressource );

			$this->assert( !empty( $ressource ), 'invalidParameter' );

			$dossier_id = $this->Ressource->dossierId( $ressource_id );

			$this->Jetons2->get( $dossier_id );

			if( !empty( $this->request->data ) ) {
				$this->Ressource->begin();

				$this->request->data['Ressource']['topressnul'] = !$this->request->data['Ressource']['topressnotnul'];

				$this->Ressource->set( $this->request->data );

				$validates = $this->Ressource->validates();
				if( array_key_exists( 'Ressourcemensuelle', $this->request->data ) ) {
					$validates = $this->Ressourcemensuelle->saveAll( $this->request->data['Ressourcemensuelle'], array( 'validate' => 'only' ) ) && $validates;
					if( array_key_exists( 'Detailressourcemensuelle', $this->request->data ) ) {
						$validates = $this->Detailressourcemensuelle->saveAll( $this->request->data['Detailressourcemensuelle'], array( 'validate' => 'only' ) ) && $validates;
					}
				}

				if( $validates ) {
					$saved = $this->Ressource->save( $this->request->data );
					if( !$this->request->data['Ressource']['topressnul'] ) {
						if( array_key_exists( 'Ressourcemensuelle', $this->request->data ) ) {
							foreach( $this->request->data['Ressourcemensuelle'] as $index => $dataRm ) {
								$this->Ressourcemensuelle->create();
								$dataRm['ressource_id'] = $this->Ressource->id;
								$saved = $this->Ressourcemensuelle->save( $dataRm ) && $saved;

								if( array_key_exists( 'Detailressourcemensuelle', $this->request->data ) ) {
									$dataDrm = $this->request->data['Detailressourcemensuelle'][$index];
									$dataDrm['ressourcemensuelle_id'] = $this->Ressourcemensuelle->id;
									$this->Detailressourcemensuelle->create();
									$saved = $this->Detailressourcemensuelle->save( $dataDrm ) && $saved;
								}
							}
						}
					}
					else {
						$rm = $this->Ressourcemensuelle->find(
								'list', array(
							'fields' => array( 'Ressourcemensuelle.id' ),
							'conditions' => array( 'Ressourcemensuelle.ressource_id' => $this->Ressource->id )
								)
						);
						if( !empty( $rm ) ) {
							$saved = $this->Detailressourcemensuelle->deleteAll(
											array(
												'Detailressourcemensuelle.ressourcemensuelle_id' => $rm
											)
									) && $saved;

							$saved = $this->Ressourcemensuelle->deleteAll(
											array(
												'Ressourcemensuelle.id' => $rm
											)
									) && $saved;
						}
					}

					$saved = $this->Ressource->refresh( $ressource['Ressource']['personne_id'] ) && $saved;

					if( $saved ) {
						$this->Ressource->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
						$this->redirect( array( 'controller' => 'ressources', 'action' => 'index', $ressource['Ressource']['personne_id'] ) );
					}
					else {
						$this->Ressource->rollback();
						$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
					}
				}
				else {
					$this->Ressource->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else {
				//INFO !!!! ça marche, mais c'est un hack
				$ressource['Detailressourcemensuelle'] = array( );
				foreach( $ressource['Ressourcemensuelle'] as $kRm => $rm ) {
					if( isset( $rm['Detailressourcemensuelle'][0] ) ) {
						$ressource['Detailressourcemensuelle'][$kRm] = $rm['Detailressourcemensuelle'][0];
					}
					unset( $ressource['Ressourcemensuelle'][$kRm]['Detailressourcemensuelle'] );
				}

				$this->request->data = $ressource;
			}

			$this->Ressource->commit();
			$this->set( 'personne_id', $ressource['Ressource']['personne_id'] );
			$this->set( 'urlmenu', '/ressources/index/'.$ressource['Ressource']['personne_id'] );
			$this->render( 'add_edit' );
		}

	}
?>