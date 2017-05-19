<?php
	/**
	 * Code source de la classe AidesdirectesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe AidesdirectesController ...
	 *
	 * @package app.Controller
	 */
	class AidesdirectesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Aidesdirectes';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
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
			'Actioninsertion',
			'Action',
			'Aidedirecte',
			'Contratinsertion',
			'Option',
			'Prestform',
			'Refpresta',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Aidesdirectes:edit',
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
		);
		
		/**
		 *
		 */
		public function beforeFilter() {
			parent::beforeFilter();
			$this->set( 'actions', $this->Action->grouplist( 'aide' ) );
			$this->set( 'typo_aide', ClassRegistry::init('Aidedirecte')->enum('typo_aide') );
		}

		/**
		 *
		 * @param integer $contratinsertion_id
		 */
		public function add( $contratinsertion_id = null ){
			// Vérification du format de la variable
			$this->assert( valid_int( $contratinsertion_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Contratinsertion->personneId( $contratinsertion_id ) ) ) );

			$contratinsertion = $this->Contratinsertion->find(
				'first',
				array(
					'conditions' => array(
						'Contratinsertion.id' => $contratinsertion_id
					),
					'recursive' => -1
				)
			);

			// Si action n'existe pas -> 404
			if( empty( $contratinsertion ) ) {
				$this->cakeError( 'error404' );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->request->data['Actioninsertion']['contratinsertion_id'] = $contratinsertion_id;
				$this->Actioninsertion->set( $this->request->data );
				$this->Aidedirecte->set( $this->request->data );

				$validates = $this->Actioninsertion->validates();

				if( $validates ) {
					$this->Actioninsertion->begin();
					$saved = $this->Actioninsertion->save( $this->request->data );

					$this->request->data['Aidedirecte']['actioninsertion_id'] = $this->Actioninsertion->id;
					$saved = $this->Aidedirecte->save( $this->request->data ) && $saved;

					if( $saved ) {
						$this->Actioninsertion->commit();
						$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
						$this->redirect( array( 'controller' => 'actionsinsertion', 'action' => 'index', $contratinsertion['Contratinsertion']['id'] ) );
					}
					else {
						$this->Actioninsertion->rollback();
					}
				}
			}

			$this->set( 'personne_id', $contratinsertion['Contratinsertion']['personne_id'] );
			$this->render( 'add_edit' );
		}

		/**
		 *
		 * @param integer $aidedirecte_id
		 */
		public function edit( $aidedirecte_id = null ){
			// Vérification du format de la variable
			$this->assert( valid_int( $aidedirecte_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Aidedirecte->personneId( $aidedirecte_id ) ) ) );

			$aidedirecte = $this->Aidedirecte->find(
				'first',
				array(
					'conditions' => array(
						'Aidedirecte.id' => $aidedirecte_id
					),
					'contain' => array(
						'Actioninsertion' => array(
							'Contratinsertion'
						)
					)
				)
			);

			// Si action n'existe pas -> 404
			if( empty( $aidedirecte ) ) {
				$this->cakeError( 'error404' );
			}

			if( !empty( $this->request->data ) ) {
				// FIXME pourquoi pas avec saveAll ?
				$this->Aidedirecte->set( $this->request->data['Aidedirecte'] );

				$validates = $this->Aidedirecte->validates();

				if( $validates ) {
					$this->Aidedirecte->begin();
					$saved = $this->Aidedirecte->save( $this->request->data['Aidedirecte'] );

					if( $saved ) {
						$this->Aidedirecte->commit();
						$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );

					//FIXME:
					$this->redirect( array( 'controller' => 'actionsinsertion', 'action' => 'index', $aidedirecte['Actioninsertion']['Contratinsertion']['id']) );
					}
					else {
						$this->Aidedirecte->rollback();
					}
				}
			}
			else{
				$this->request->data = array(
					'Aidedirecte' => $aidedirecte['Aidedirecte'],
				);
			}

			// FIXME: [0] grujage
			$this->set( 'personne_id', $aidedirecte['Actioninsertion']['Contratinsertion']['personne_id'] );
			$this->render( 'add_edit' );
		}
	}

?>