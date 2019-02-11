<?php
	/**
	 * Code source de la classe Proposorientationscovs58Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Proposorientationscovs58Controller ... (CG 58).
	 *
	 * @package app.Controller
	 */
	class Proposorientationscovs58Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Proposorientationscovs58';

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
			'Default',
			'Default2',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Propoorientationcov58',
			'WebrsaOrientstruct',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Proposorientationscovs58:edit',
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
			'delete' => 'delete',
			'edit' => 'update',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$this->set( 'referents', $this->Propoorientationcov58->Referent->WebrsaReferent->listOptions() );
			$this->set( 'typesorients', $this->Propoorientationcov58->Typeorient->listOptions() );
			$this->set( 'structuresreferentes', $this->Propoorientationcov58->Structurereferente->list1Options( array( 'orientation' => 'O' ) ) );

			//Ajout des structures et référents orientants
			$this->set( 'refsorientants', $this->Propoorientationcov58->Referent->WebrsaReferent->listOptions() );
			$this->set( 'structsorientantes', $this->Propoorientationcov58->Structurereferente->listOptions( array( 'orientation' => 'O' ) ) );
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
		 * @param integer $personne_id
		 */
		protected function _add_edit( $personne_id = null ) {
			$this->assert( valid_int( $personne_id ), 'invalidParameter' );

			if ( $this->action == 'edit' ) {
				$propoorientationcov58 = $this->Propoorientationcov58->find(
					'first',
					array(
						'fields' => array(
							'Propoorientationcov58.id',
							'Propoorientationcov58.dossiercov58_id',
							'Propoorientationcov58.typeorient_id',
							'Propoorientationcov58.structurereferente_id',
							'Propoorientationcov58.referent_id',
							'Propoorientationcov58.structureorientante_id',
							'Propoorientationcov58.referentorientant_id',
							'Propoorientationcov58.datedemande'
						),
						'joins' => array(
							array(
								'table' => 'dossierscovs58',
								'alias' => 'Dossiercov58',
								'type' => 'INNER',
								'conditions' => array(
									'Dossiercov58.id = Propoorientationcov58.dossiercov58_id',
									'Dossiercov58.personne_id' => $personne_id
								)
							)
						),
						'contain' => false,
						'order' => array( 'Propoorientationcov58.rgorient DESC' )
					)
				);
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$dossier_id = $this->Propoorientationcov58->Dossiercov58->Personne->dossierId( $personne_id );
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'controller' => 'orientsstructs', 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$saved = true;
				$this->Propoorientationcov58->begin();

				$this->request->data['Propoorientationcov58']['rgorient'] = $this->Propoorientationcov58->Dossiercov58->Personne->Orientstruct->WebrsaOrientstruct->rgorientMax( $personne_id );

				// Si c'est une régression, on envoie en COV
				if ( $this->WebrsaOrientstruct->isRegression( $personne_id, $this->request->data['Propoorientationcov58']['typeorient_id'] ) ) {
					$query = array(
						'fields' => array( 'Themecov58.id' ),
						'contain' => false,
						'conditions' => array( 'Themecov58.name' => 'regressionsorientationscovs58' )
					);
					$themecov58 = $this->Propoorientationcov58->Dossiercov58->Themecov58->find( 'first', $query );

					$query = array(
						'fields' => array( 'Orientstruct.id' ),
						'contain' => false,
						'conditions' => array(
							'Orientstruct.personne_id' => $personne_id,
							'Orientstruct.statut_orient' => 'Orienté'
						),
						'order' => array( 'Orientstruct.date_valid DESC' )
					);
					$orientstruct = $this->Propoorientationcov58->Structurereferente->Orientstruct->find( 'first', $query );

					$dossiercov58 = array(
						'Dossiercov58' => array(
							'personne_id' => $personne_id,
							'themecov58_id' => Hash::get( $themecov58, 'Themecov58.id' ),
							'themecov58' => 'regressionsorientationscovs58',
						)
					);

					$this->Propoorientationcov58->Dossiercov58->create( $dossiercov58 );
					$saved = $this->Propoorientationcov58->Dossiercov58->save( null, array( 'atomic' => false ) ) && $saved;

					$regressionorientationcov58 = array(
						'Regressionorientationcov58' => array(
							'dossiercov58_id' => $this->Propoorientationcov58->Dossiercov58->id,
							'orientstruct_id' => Hash::get( $orientstruct, 'Orientstruct.id' ),
							'typeorient_id' => Hash::get( $this->request->data, 'Propoorientationcov58.typeorient_id' ),
							'structurereferente_id' => suffix( Hash::get( $this->request->data, 'Propoorientationcov58.structurereferente_id' ) ),
							'referent_id' => suffix( Hash::get( $this->request->data, 'Propoorientationcov58.referent_id' ) ),
							'datedemande' => Hash::get( $this->request->data, 'Propoorientationcov58.datedemande' ),
							'user_id' => Hash::get( $this->request->data, 'Propoorientationcov58.user_id' )
						)
					);

					$this->Propoorientationcov58->Dossiercov58->Regressionorientationcov58->create( $regressionorientationcov58 );
					$saved = $this->Propoorientationcov58->Dossiercov58->Regressionorientationcov58->save( null, array( 'atomic' => false ) ) && $saved;
					if( $saved === false ) {
						$this->Propoorientationcov58->validationErrors = $this->Propoorientationcov58->Dossiercov58->Regressionorientationcov58->validationErrors;
					}
				}
				else {
					if ( $this->action == 'add' ) {
						$themecov58 = $this->Propoorientationcov58->Dossiercov58->Themecov58->find(
							'first',
							array(
								'conditions' => array(
									'Themecov58.name' => Inflector::tableize($this->Propoorientationcov58->alias)
								),
								'contain' => false
							)
						);
						$dossiercov58['Dossiercov58']['themecov58_id'] = $themecov58['Themecov58']['id'];
						$dossiercov58['Dossiercov58']['personne_id'] = $personne_id;
						$dossiercov58['Dossiercov58']['themecov58'] = 'proposorientationscovs58';

						$saved = $this->Propoorientationcov58->Dossiercov58->save( $dossiercov58, array( 'atomic' => false ) ) && $saved;

						$this->Propoorientationcov58->create();

						$this->request->data['Propoorientationcov58']['dossiercov58_id'] = $this->Propoorientationcov58->Dossiercov58->id;
					}

					$saved = $this->Propoorientationcov58->save( $this->request->data['Propoorientationcov58'] , array( 'atomic' => false ) ) && $saved;
				}

				if( $saved ) {
					$this->Propoorientationcov58->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'orientsstructs', 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Flash->error( __( 'Save->error' ) );
					$this->Propoorientationcov58->rollback();
				}
			}
			elseif ( $this->action == 'edit' ) {
				$this->request->data = $propoorientationcov58;
			}

			$this->_setOptions();
			$this->set( 'personne_id', $personne_id );
			$this->set( 'urlmenu', '/orientsstructs/index/'.$personne_id );
			$this->render( '_add_edit' );
		}

		/**
		 * Suppression de la proposition d'orientation en COV lorsque le dossier COV n'est pas encore attaché
		 * à une COV.
		 *
		 * @param integer $propoorientationcov58_id L'id de la proposition d'orientation
		 */
		public function delete( $propoorientationcov58_id ) {
			$propoorientationcov58 = $this->Propoorientationcov58->find(
				'first',
				array(
					'fields' => array(
						'Propoorientationcov58.id',
						'Propoorientationcov58.dossiercov58_id'
					),
					'contain' => false,
					'conditions' => array(
						'Propoorientationcov58.id' => $propoorientationcov58_id
					)
				)
			);

			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Propoorientationcov58->Dossiercov58->personneId( $propoorientationcov58['Propoorientationcov58']['dossiercov58_id'] ) ) );

			$this->Propoorientationcov58->begin();

			$success = $this->Propoorientationcov58->delete( $propoorientationcov58['Propoorientationcov58']['id'] );
			$success = $this->Propoorientationcov58->Dossiercov58->delete( $propoorientationcov58['Propoorientationcov58']['dossiercov58_id'] ) && $success;

			if( $success ) {
				$this->Propoorientationcov58->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Propoorientationcov58->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}
			$this->redirect( $this->referer() );
		}
	}
?>