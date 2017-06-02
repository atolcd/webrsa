<?php
	/**
	 * Code source de la classe PrestsformController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PrestsformController ...
	 *
	 * @package app.Controller
	 */
	class PrestsformController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Prestsform';

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
			'Personne',
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
			'add' => 'Prestsform:edit',
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
			$this->set( 'actions', $this->Action->grouplist( 'prestation' ) );
		}

		/**
		 *
		 * @param integer $contratinsertion_id
		 */
		public function add( $contratinsertion_id = null ){
			if( !valid_int( $contratinsertion_id ) ) {
				$this->cakeError( 'error404' );
			}
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
				$this->Prestform->set( $this->request->data['Prestform'] );
				$this->Refpresta->set( $this->request->data );

				$validates = $this->Actioninsertion->validates();

				if( $validates ) {
					$this->Actioninsertion->begin();
					$saved = $this->Actioninsertion->save( $this->request->data );
					$saved = $this->Refpresta->save( $this->request->data ) && $saved;

					$this->request->data['Prestform']['refpresta_id'] = $this->Refpresta->id;
					$this->request->data['Prestform']['actioninsertion_id'] = $this->Actioninsertion->id;
					$saved = $this->Prestform->save( $this->request->data ) && $saved;

					if( $saved ) {
						$this->Actioninsertion->commit();
						$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );

						// FIXME:
						$this->redirect( array( 'controller' => 'actionsinsertion', 'action' => 'index', $contratinsertion['Contratinsertion']['id'] ) );
					}
					else {
						$this->Actioninsertion->rollback();
					}
				}
			}

			$this->request->data['Actioninsertion']['contratinsertion_id'] = $contratinsertion_id;
			$this->set( 'personne_id',  $contratinsertion['Contratinsertion']['personne_id'] );
			$this->render( 'add_edit' );
		}

		/**
		 *
		 * @param integer $prestform_id
		 */
		public function edit( $prestform_id = null ){
			if( !valid_int( $prestform_id ) ) {
				$this->cakeError( 'error404' );
			}
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Prestform->personneId( $prestform_id ) ) ) );

			$prestform = $this->Prestform->find(
				'first',
				array(
					'conditions' => array(
						'Prestform.id' => $prestform_id
					),
					'contain' => array(
						'Actioninsertion' => array(
							'Contratinsertion'
						),
						'Refpresta'
					)
				)
			);
			// Si action n'existe pas -> 404
			if( empty( $prestform ) ) {
				$this->cakeError( 'error404' );
			}

			if( !empty( $this->request->data ) ) {
				// FIXME pourquoi pas avec saveAll ?
				$this->Prestform->set( $this->request->data['Prestform'] );
				$this->Refpresta->set( $this->request->data['Refpresta'] );

				$validates = $this->Prestform->validates();
				$validates = $this->Refpresta->validates() && $validates;

				if( $validates ) {
					$this->Prestform->begin();
					$saved = $this->Prestform->save( $this->request->data['Prestform'] );
					$saved = $this->Refpresta->save( $this->request->data['Refpresta'] ) && $saved;

					if( $saved ) {
						$this->Prestform->commit();
						$this->Session->setFlash( 'Enregistrement effectué', 'flash/success');

						//FIXME:
						$this->redirect( array( 'controller' => 'actionsinsertion', 'action' => 'index', $prestform['Actioninsertion']['Contratinsertion']['id']) );
					}
					else {
						$this->Prestform->rollback();
					}
				}
			}
			else{
				$this->request->data = array(
					'Prestform' => $prestform['Prestform'],
					'Refpresta' => $prestform['Refpresta'],
				);
			}
			// FIXME: [0] grujage
			$this->set( 'personne_id', $prestform['Actioninsertion']['Contratinsertion']['personne_id'] );
			$this->render( 'add_edit' );
		}
	}

?>