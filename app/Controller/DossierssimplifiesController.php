<?php
	/**
	 * Code source de la classe DossierssimplifiesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe DossierssimplifiesController ...
	 *
	 * @package app.Controller
	 */
	class DossierssimplifiesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Dossierssimplifies';

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

		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dossier',
			'Foyer',
			'Option',
			'Orientstruct',
			'Personne',
			'Structurereferente',
			'Typeorient',
			'Typocontrat',
			'Zonegeographique',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Dossierssimplifies:edit',
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
			'view' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$this->set( 'pays', ClassRegistry::init('Adresse')->enum('pays') );
			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'fonorg', array( 'CAF' => 'CAF', 'MSA' => 'MSA' ) );
			$this->set( 'rolepers', array_filter_keys( ClassRegistry::init('Prestation')->enum('rolepers'), array( 'DEM', 'CJT' ) ) );
			$this->set( 'toppersdrodevorsa', $this->Option->toppersdrodevorsa() );
			// Statut de l'orientation. Au CG 66, on ne veut que "Orienté" ou vide.
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$statut_orient = array( 'Orienté' => 'Orienté' );
			}
			else {
				$statut_orient = $this->Orientstruct->enum( 'statut_orient' );
			}
			$this->set( 'statut_orient', $statut_orient );
			$this->set( 'options', $this->Typeorient->listOptions() );
			$this->set( 'structsReferentes', $this->Structurereferente->list1Options() );
			$this->set( 'refsorientants', $this->Structurereferente->Referent->WebrsaReferent->listOptions() );
		}


		/**
		 *
		 * @param integer $id
		 */
		public function view( $id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $id ) ) );

			$details = array( );

			$typeorient = $this->Typeorient->find( 'list', array( 'fields' => array( 'lib_type_orient' ) ) );
			$this->set( 'typeorient', $typeorient );

			$qd_tDossier = array(
				'conditions' => array(
					'Dossier.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$tDossier = $this->Dossier->find( 'first', $qd_tDossier );


			$details = Set::merge( $details, $tDossier );

			$qd_tFoyer = array(
				'conditions' => array(
					'Foyer.dossier_id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$tFoyer = $this->Dossier->Foyer->find( 'first', $qd_tFoyer );

			$details = Set::merge( $details, $tFoyer );

			$bindPrestation = $this->Personne->hasOne['Prestation'];
			$this->Personne->unbindModelAll();
			$this->Personne->bindModel( array( 'hasOne' => array( 'Dossiercaf', 'Prestation' => $bindPrestation ) ) );
			$personnesFoyer = $this->Personne->find(
				'all',
				array(
					'conditions' => array(
						'Personne.foyer_id' => $tFoyer['Foyer']['id'],
						'Prestation.rolepers' => array( 'DEM', 'CJT' )
					),
					'contain' => array(
						'Prestation'
					)
// 					'recursive' => -1
				)
			);

			$roles = Set::extract( '{n}.Prestation.rolepers', $personnesFoyer );
			foreach( $roles as $index => $role ) {
				///Orientations
				$orient = $this->Orientstruct->find(
						'first', array(
					'conditions' => array( 'Orientstruct.personne_id' => $personnesFoyer[$index]['Personne']['id'] ),
					'recursive' => -1,
					'order' => 'Orientstruct.date_propo DESC',
						)
				);
				$personnesFoyer[$index]['Orientstruct'] = (array)Hash::get( $orient, 'Orientstruct' );

				///Structures référentes
				$struct = $this->Structurereferente->find(
						'first', array(
					'conditions' => array( 'Structurereferente.id' => Hash::get( $personnesFoyer, "{$index}.Orientstruct.structurereferente_id" ) ),
					'recursive' => -1
						)
				);
				$personnesFoyer[$index]['Structurereferente'] = (array)Hash::get( $struct, 'Structurereferente' );

				$details[$role] = $personnesFoyer[$index];
			}

			$this->set( 'personnes', $personnesFoyer );

			$this->_setOptions();
			$this->set( 'details', $details );
		}

		/**
		 *
		 */
		public function add() {
			$this->set( 'typesOrient', $this->Typeorient->listOptions() );
			$this->set( 'structures', $this->Structurereferente->list1Options() );

			$typesOrient = $this->Typeorient->find(
					'list', array(
				'fields' => array(
					'Typeorient.id',
					'Typeorient.lib_type_orient'
				),
				'conditions' => array(
					'Typeorient.parentid' => null
				)
					)
			);
			$this->set( 'typesOrient', $typesOrient );

			$typesStruct = $this->Typeorient->find(
					'list', array(
				'fields' => array(
					'Typeorient.id',
					'Typeorient.lib_type_orient'
				),
				'conditions' => array(
					'Typeorient.parentid NOT' => null
				)
					)
			);
			$this->set( 'typesStruct', $typesStruct );


			if( !empty( $this->request->data ) ) {
				if( !empty( $this->request->data['Dossier']['numdemrsatemp'] ) ) {
					$this->request->data['Dossier']['numdemrsa'] = $this->Dossier->generationNumdemrsaTemporaire();
				}

				$this->Dossier->set( $this->request->data );
				$this->Foyer->set( $this->request->data );
				$this->Orientstruct->set( $this->request->data );
				$this->Structurereferente->set( $this->request->data );

				$validates = $this->Dossier->validates();
				$validates = $this->Foyer->validates() && $validates;

				$tPers1 = $this->request->data['Personne'][1];
				unset( $tPers1['rolepers'] );
				unset( $tPers1['dtnai'] ); // FIXME ... créer array_filter_deep
				$t = array_filter( $tPers1 );
				if( empty( $t ) ) {
					unset( $this->request->data['Personne'][1] );
				}
				$validates = $this->Personne->saveAll( $this->request->data['Personne'], array( 'validate' => 'only' ) ) && $validates;

				$validates = $this->Orientstruct->validates() && $validates;
				$validates = $this->Structurereferente->validates() && $validates;

				if( $validates ) {
					$this->Dossier->begin();
					$saved = $this->Dossier->save( $this->request->data , array( 'atomic' => false ) );
					// Foyer
					$this->request->data['Foyer']['dossier_id'] = $this->Dossier->id;
					$saved = $this->Foyer->save( $this->request->data , array( 'atomic' => false ) ) && $saved;
					// Situation dossier RSA
					$situationdossierrsa = array( 'Situationdossierrsa' => array( 'dossier_id' => $this->Dossier->id, 'etatdosrsa' => 'Z' ) );
					$this->Dossier->Situationdossierrsa->validate = array( );
					$saved = $this->Dossier->Situationdossierrsa->save( $situationdossierrsa , array( 'atomic' => false ) ) && $saved;

					$orientstruct_validate = $this->Orientstruct->validate;

					$orientsstructsValidationErrors = array();

					foreach( $this->request->data['Personne'] as $key => $pData ) {
						if( !empty( $pData ) ) {
							$this->Orientstruct->validate = $orientstruct_validate;
							// Personne
							$this->Personne->create();
							$pData['foyer_id'] = $this->Foyer->id;
							$this->Personne->set( $pData );
							$saved = $this->Personne->save( null, array( 'atomic' => false ) ) && $saved;
							$personneId = $this->Personne->id;

							// Prestation, Calculdroitrsa
							foreach( array( 'Prestation', 'Calculdroitrsa' ) as $tmpModel ) {
								$this->Personne->{$tmpModel}->create();
								$this->request->data[$tmpModel][$key]['personne_id'] = $personneId;
								$this->Personne->{$tmpModel}->set( $this->request->data[$tmpModel][$key] );
								$saved = $this->Personne->{$tmpModel}->save( $this->request->data['Prestation'][$key] , array( 'atomic' => false ) ) && $saved;
							}

							// Orientation
							$statut_orient = Hash::get( $this->request->data, "Orientstruct.{$key}.statut_orient" );
							// Si le statut d'orientation n'est pas renseigné, on ne cherche pas à ajouter une orientation
							if( !in_array( $statut_orient, array( null, '' ), true ) ) {
								$tOrientstruct = Set::extract( $this->request->data, 'Orientstruct.'.$key );
								if( !empty( $tOrientstruct ) ) {
									$tOrientstruct = Hash::filter( (array)$tOrientstruct );
								}

								if( !empty( $tOrientstruct ) ) {
									$this->request->data['Orientstruct'][$key]['personne_id'] = $this->Personne->id;
									$this->request->data['Orientstruct'][$key]['valid_cg'] = true;
									$this->request->data['Orientstruct'][$key]['date_propo'] = date( 'Y-m-d' );
									$this->request->data['Orientstruct'][$key]['date_valid'] = date( 'Y-m-d' );
									$this->request->data['Orientstruct'][$key]['user_id'] = $this->Session->read( 'Auth.User.id' );
									$this->Orientstruct->create( $this->request->data['Orientstruct'][$key] );
									$saved = $this->Orientstruct->save( null, array( 'atomic' => false ) ) && $saved;
								}
								else {
									$this->Orientstruct->validate = array( );
									$this->request->data['Orientstruct'][$key]['personne_id'] = $this->Personne->id;
									$this->request->data['Orientstruct'][$key]['user_id'] = $this->Session->read( 'Auth.User.id' );
									$this->Orientstruct->create( $this->request->data['Orientstruct'][$key] );
									$saved = $this->Orientstruct->save( null, array( 'atomic' => false ) ) && $saved;
								}

								// Si on a une erreur lors de l'enregistrement d'une orientation
								if( empty( $this->Orientstruct->id ) ) {
									$orientsstructsValidationErrors[$key] = $this->Orientstruct->validationErrors;
								}
							}
						}
					}

					if( $saved ) {
						$this->Dossier->commit();
						$this->Flash->success( __( 'Save->success' ) );
						return $this->redirect( array( 'controller' => 'dossierssimplifies', 'action' => 'view', $this->Dossier->id ) );
					}
					else {
						$this->Dossier->rollback();
						$this->Flash->error( __( 'Save->error' ) );
						$this->Orientstruct->validationErrors = $orientsstructsValidationErrors;
					}
				}
			}
			$this->_setOptions();
		}


		/**
		 *
		 * @param type $personne_id
		 * @param type $orient_id
		 */
		public function edit( $personne_id = null, $orient_id = null ) {
			$this->assert( valid_int( $personne_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );
			$dossier_id = $this->Personne->dossierId( $personne_id );

			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'view', $dossier_id ) );
			}

			$qd_personne = array(
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'fields' => null,
				'order' => null,
				'contain' => array(
					'Foyer',
					'Orientstruct',
					'Prestation',
					'Calculdroitrsa'
				)
			);
			$personne = $this->Personne->find( 'first', $qd_personne );
// debug($personne);
			$orientstruct = $this->Orientstruct->find(
				'first',
				array(
					'conditions' => array( 'Orientstruct.personne_id' => $personne_id ),
					'contain' => false,
					'recursive' => -1,
					'order' => 'Orientstruct.date_propo DESC'
				)
			);
			$personne = Set::merge( $personne, array( 'Orientstruct' => (array)Hash::get( $orientstruct, 'Orientstruct' ) ) );

			$dossier_id = $personne['Foyer']['dossier_id'];
			$dossimple = $this->Dossier->find(
				'first',
				array(
					'conditions' => array(
						'Dossier.id' => $dossier_id
					),
					'contain' => false,
					'recursive' => -1
				)
			);

			$this->set( 'personne_id', $personne_id );
			$this->set( 'dossiersimple_id', $dossier_id );
			$this->set( 'foyer_id', $personne['Foyer']['id'] );
			$this->set( 'typesOrient', $this->Typeorient->listOptions() );
			$this->set( 'structures', $this->Structurereferente->list1Options() );
			$this->set( 'structuresorientantes', $this->Structurereferente->listOptions() );
			$this->set( 'numdossierrsa', $dossimple['Dossier']['numdemrsa'] );
			$this->set( 'datdemdossrsa', $dossimple['Dossier']['dtdemrsa'] );
			$this->set( 'matricule', $dossimple['Dossier']['matricule'] );
			$this->set( 'orient_id', Hash::get( $orientstruct, 'Orientstruct.0.typeorient_id' ) );
			$this->set( 'structure_id', Hash::get( $orientstruct, 'Orientstruct.0.structurereferente_id' ) );
			$this->set( 'structureorientante_id', Hash::get( $orientstruct, 'Orientstruct.0.structureorientante_id' ) );

			$this->_setOptions();
			if( !empty( $this->request->data ) ) {

				$statut_orient = Hash::get( $this->request->data, "Orientstruct.0.statut_orient" );
				// Si le statut d'orientation n'est pas renseigné, on ne cherche pas à ajouter une orientation
				if( !in_array( $statut_orient, array( null, '' ), true ) ) {
					if( isset( $personne['Orientstruct'][0]['id'] ) ) {
						$this->request->data['Orientstruct'][0]['id'] = $personne['Orientstruct'][0]['id'];
					}

					$this->request->data['Orientstruct'][0]['user_id'] = $this->Session->read( 'Auth.User.id' );

					if( $this->Personne->saveAll( $this->request->data, array( 'validate' => 'only' ) ) && isset( $this->request->data['Orientstruct'][0]['typeorient_id'] ) && isset( $this->request->data['Orientstruct'][0]['structurereferente_id'] ) ) {
						$this->request->data['Orientstruct'][0]['statut_orient'] = 'Orienté';
						$this->request->data['Orientstruct'][0]['date_propo'] = strftime( '%Y-%m-%d', time() ); // FIXME
						$this->request->data['Orientstruct'][0]['date_valid'] = strftime( '%Y-%m-%d', time() ); // FIXME
					}
				}
				else {
					unset( $this->request->data['Orientstruct'] );
				}

				$this->Dossier->begin();
				$this->Dossier->id = $dossier_id;
				$savePersonne = $this->Personne->saveAll( $this->request->data, array( 'atomic' => false ) );
				$saveDossier = $this->Dossier->saveField( 'dtdemrsa', $this->request->data['Dossier']['dtdemrsa'], true );

				if( $savePersonne && $saveDossier ) {
					$this->Dossier->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					return $this->redirect( array( 'controller' => 'dossierssimplifies', 'action' => 'view', $dossier_id ) );
				}
				else {
					$this->Dossier->rollback();
				}
			}
			else {
				$this->request->data = $personne;
				$this->request->data['Dossier']['dtdemrsa'] = $dossimple['Dossier']['dtdemrsa'];
			}
			$this->_setOptions();
			$this->set( 'personne', $personne );
		}

	}
?>