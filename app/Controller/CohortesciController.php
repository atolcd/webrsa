<?php
	/**
	 * Code source de la classe CohortesciController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CohortesciController permet de traiter les contrats d'engagements réciproques en cohorte
	 * (CG 66 et 93).
	 *
	 * @package app.Controller
	 * @deprecated since version 3.0
	 */
	class CohortesciController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cohortesci';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes' => array(
				'nouveaux',
				'nouveauxsimple',
				'nouveauxparticulier',
			),
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.SearchPrg' => array(
				'actions' => array(
					'valides',
					'nouveaux' => array('filter' => 'Search'),
					'nouveauxsimple' => array('filter' => 'Search'),
					'nouveauxparticulier' => array('filter' => 'Search'),
				)
			),
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
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cohorteci',
			'Dossier',
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
			'ajaxreferent',
			'constReq',
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'ajaxreferent' => 'read',
			'exportcsv' => 'read',
			'nouveaux' => 'read',
			'nouveauxparticulier' => 'read',
			'nouveauxsimple' => 'read',
			'valides' => 'update',
		);

		/**
		 * Méthode commune d'envoi des options dans les vues.
		 *
		 * @return void
		 */
		public function beforeFilter() {
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '512M');
			parent::beforeFilter();

			$this->set( 'oridemrsa', ClassRegistry::init('Detaildroitrsa')->enum('oridemrsa') );
			$this->set( 'typeserins', $this->Option->typeserins() );
			$this->set( 'printed', $this->Option->printed() );
			$this->set( 'decision_ci', ClassRegistry::init('Contratinsertion')->enum('decision_ci') );
			$struct = $this->Dossier->Foyer->Personne->Contratinsertion->Structurereferente->find( 'list', array( 'fields' => array( 'id', 'lib_struc' ) ) );
			$this->set( 'struct', $struct );
			$this->set( 'duree_engag', $this->Option->duree_engag() );

			$this->set( 'numcontrat', (array)Hash::get( $this->Dossier->Foyer->Personne->Contratinsertion->enums(), 'Contratinsertion' ) );

			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );

			$forme_ci = array();
			if( Configure::read( 'nom_form_ci_cg' ) == 'cg93' ) {
				$forme_ci = array( 'S' => 'Simple', 'C' => 'Complexe' );
			}
			else if( Configure::read( 'nom_form_ci_cg' ) == 'cg66' ) {
				$forme_ci = array( 'S' => 'Simple', 'C' => 'Particulier' );
			}
			$this->set( 'forme_ci', $forme_ci );

			if( $this->action == 'valides' ) {
				$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );
			}
			else {
				$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa', array('filter' =>  $this->Dossier->Situationdossierrsa->etatOuvert())) );
			}
		}

		/**
		 * Liste des CER particuliers (CG 66) non validés.
		 *
		 * @return void
		 */
		public function nouveauxparticulier() {
			$this->_index( 'Decisionci::nouveauxparticulier' );
		}

		/**
		 * Liste des CER simples (CG 66) non validés.
		 *
		 * @return void
		 */
		public function nouveauxsimple() {
			$this->_index( 'Decisionci::nouveauxsimple' );
		}

		/**
		 * Liste des CER (CG 93) non validés.
		 *
		 * @deprecated since 3.0.00
		 *
		 * @return void
		 */
		public function nouveaux() {
			$this->_index( 'Decisionci::nouveaux' );
		}

		/**
		 * Liste des CER (CG 93) validés.
		 *
		 * @deprecated since 3.0.00
		 *
		 * @return void
		 */
		public function valides() {
			$this->_index( 'Decisionci::valides' );
		}

		/**
		 * Ajax pour lien référent - structure référente.
		 *
		 * @param integer $structurereferente_id
		 * @return array
		 */
		protected function _selectReferents( $structurereferente_id ) {
			$conditions = array();
			if( !empty( $structurereferente_id ) ) {
				$conditions['Referent.structurereferente_id'] = $structurereferente_id;
			}
			$referents = $this->Dossier->Foyer->Personne->Contratinsertion->Referent->find(
				'all',
				array(
					'conditions' => $conditions,
					'recursive' => -1
				)
			);
			return $referents;
		}

		/**
		 * @return void
		 */
		public function ajaxreferent() {
			Configure::write( 'debug', 0 );
			$referents = $this->_selectReferents( Set::classicExtract( $this->request->data, 'Contratinsertion.structurereferente_id' ) );
			$options = array( '<option value=""></option>' );
			foreach( $referents as $referent ) {
				$options[] = '<option value="'.$referent['Referent']['id'].'">'.$referent['Referent']['nom'].' '.$referent['Referent']['prenom'].'</option>';
			} ///FIXME: à mettre dans la vue
			echo implode( '', $options );
			$this->render( null, 'ajax' );
		}

		/**
		 * Traitement de la cohorte.
		 *
		 * @param string $statutValidation Le statut de validation (nouveauxparticulier, nouveauxsimple, nouveaux, valides)
		 */
		protected function _index( $statutValidation = null ) {
			$this->assert( !empty( $statutValidation ), 'invalidParameter' );

			/*$personne_suivi = $this->Dossier->Foyer->Personne->Contratinsertion->find(
				'list',
				array(
					'fields' => array(
						'Contratinsertion.pers_charg_suivi',
						'Contratinsertion.pers_charg_suivi'
					),
					'order' => 'Contratinsertion.pers_charg_suivi ASC',
					'group' => 'Contratinsertion.pers_charg_suivi',
				)
			);

			$this->set( 'personne_suivi', $personne_suivi );*/

			// Un formulaire a été envoyé.
			if( !empty( $this->request->data ) ) {
				// On a renvoyé  le formulaire de la cohorte
				if( !empty( $this->request->data['Contratinsertion'] ) ) {
					$this->Cohortes->get( array_unique( Set::extract( $this->request->data, 'Contratinsertion.{n}.dossier_id' ) ) );

					if( Configure::read( 'Cg.departement' ) == 66 ) {
						$contratsatraiter = Set::extract('/Contratinsertion[atraiter=1]', $this->request->data );
					}
					else{
						$contratsatraiter = Set::extract('/Contratinsertion[decision_ci!=E]', $this->request->data );
					}

					if( !empty( $contratsatraiter ) ){
						if( Configure::read( 'Cg.departement' ) != 66 ) {
							$valid = $this->Dossier->Foyer->Personne->Contratinsertion->saveAll( $contratsatraiter, array( 'validate' => 'only', 'atomic' => false ) );
							if( $valid ) {
								$this->Dossier->begin();
								$saved = $this->Dossier->Foyer->Personne->Contratinsertion->saveAll( $contratsatraiter, array( 'validate' => 'first', 'atomic' => false ) );
								if( $saved ) {
									$this->Dossier->commit();
									$this->Session->setFlash( 'Enregistrement effectué.', 'flash/success' );
									$this->Cohortes->release( array_unique( Set::extract( $this->request->data, 'Contratinsertion.{n}.dossier_id' ) ) );
									unset( $this->request->data['Contratinsertion'] );
								}
								else {
									$this->Dossier->rollback();
									$this->Session->setFlash( 'Erreur lors de l\'enregistrement.', 'flash/error' );
								}
							}
						}
						else if( Configure::read( 'Cg.departement' ) == 66 ) {
							$valid = $this->Dossier->Foyer->Personne->Contratinsertion->saveAll( $contratsatraiter, array( 'validate' => 'only', 'atomic' => false ) );
							if( $valid ) {
								$this->Dossier->begin();

								$saved = $this->Dossier->Foyer->Personne->Contratinsertion->Propodecisioncer66->sauvegardeCohorteCer( $this->request->data['Contratinsertion'] );
								$saved = $this->Dossier->Foyer->Personne->Contratinsertion->saveAll( $contratsatraiter, array( 'validate' => 'first', 'atomic' => false ) ) && $saved;

								if( $saved ) {
									$this->Dossier->commit();
									$this->Session->setFlash( 'Enregistrement effectué.', 'flash/success' );
									$this->Cohortes->release( array_unique( Set::extract( $this->request->data, 'Contratinsertion.{n}.dossier_id' ) ) );
									unset( $this->request->data['Contratinsertion'] );
								}
								else {
									$this->Dossier->rollback();
									$this->Session->setFlash( 'Erreur lors de l\'enregistrement.', 'flash/error' );
								}
							}
						}
					}
				}

				// Filtrage
				if( in_array( $statutValidation, array( 'Decisionci::nouveaux', 'Decisionci::nouveauxsimple', 'Decisionci::nouveauxparticulier', 'Decisionci::valides' ) ) && !empty( $this->request->data ) ) {
					$querydata = $this->Cohorteci->search(
						$statutValidation,
						$this->request->data['Search'],
						( $statutValidation != 'Decisionci::valides' ? $this->Dossier->Situationdossierrsa->etatOuvert() : array() )
					);

					$querydata = $this->Cohortes->qdConditions( $querydata );
					$querydata = $this->Gestionzonesgeos->qdConditions( $querydata );
					$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();

					$this->paginate = $querydata;
					$progressivePaginate = !Hash::get( $this->request->data, 'Search.Contratinsertion.nombre_total' );
					$cohorteci = $this->paginate( $this->Dossier->Foyer->Personne->Contratinsertion, array(), array(), $progressivePaginate );

					foreach( $cohorteci as $key => $value ) {
						$cohorteci[$key]['Contratinsertion']['proposition_decision_ci'] = $value['Contratinsertion']['decision_ci'];

						if( empty( $value['Contratinsertion']['datevalidation_ci'] ) ) {
							$cohorteci[$key]['Contratinsertion']['proposition_datevalidation_ci'] = $value['Contratinsertion']['dd_ci'];
						}
						else {
							$cohorteci[$key]['Contratinsertion']['proposition_datevalidation_ci'] = $value['Contratinsertion']['datevalidation_ci'];
						}

						if( Configure::read( 'Cg.departement' ) == 66 ) {
							if( empty( $value['Contratinsertion']['datedecision'] ) ) {
								$cohorteci[$key]['Contratinsertion']['proposition_datedecision'] = date( 'Y-m-d' );
							}
							else {
								$cohorteci[$key]['Contratinsertion']['proposition_datedecision'] = $value['Contratinsertion']['datedecision'];
							}
						}
					}

					if( $statutValidation != 'Decisionci::valides' ) {
						$this->Cohortes->get( array_unique( (array)Set::extract( $cohorteci, '{n}.Dossier.id' ) ) );
					}

					$this->set( 'cohorteci', $cohorteci );
				}
			}

			// Population du select référents liés aux structures
			$structurereferente_id = Set::classicExtract( $this->request->data, 'Contratinsertion.structurereferente_id' );
			$referents = $this->InsertionsBeneficiaires->referents(
				array(
					'type' => 'list',
					'prefix' => false,
					'conditions' => $this->InsertionsBeneficiaires->conditions['referents']
						+ (
							empty( $structurereferente_id )
							? array()
							: array( 'Referent.structurereferente_id' => $structurereferente_id )
						)
				)
			);
			$this->set( 'referents', $referents );

			$this->set( 'cantons', $this->Gestionzonesgeos->listeCantons() );
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			switch( $statutValidation ) {
				case 'Decisionci::nouveaux':
					$this->render( 'formulaire' );
					break;
				case 'Decisionci::nouveauxsimple':
					$this->render( 'formulairesimple' );
					break;
				case 'Decisionci::nouveauxparticulier':
					$this->render( 'formulaireparticulier' );
					break;
				case 'Decisionci::valides':
					$this->render( 'visualisation' );
					break;
			}
		}

		/**
		 * Export du tableau en CSV.
		 *
		 * @return void
		 */
		public function exportcsv() {
			$querydata = $this->Cohorteci->search(
				'Decisionci::valides',
				Hash::get( Hash::expand( $this->request->params['named'], '__' ), 'Search' ),
				array()
			);

			$querydata = $this->Cohortes->qdConditions( $querydata ); // FIXME
			$querydata = $this->Gestionzonesgeos->qdConditions( $querydata );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();

			unset( $querydata['limit'] );

			$contrats = $this->Dossier->Foyer->Personne->Contratinsertion->find( 'all', $querydata );

			/// Population du select référents liés aux structures
			$structurereferente_id = Set::classicExtract( $this->request->data, 'Contratinsertion.structurereferente_id' );
			$referents = $this->InsertionsBeneficiaires->referents(
				array(
					'type' => 'list',
					'prefix' => false,
					'conditions' => $this->InsertionsBeneficiaires->conditions['referents']
						+ (
							empty( $structurereferente_id )
							? array()
							: array( 'Referent.structurereferente_id' => $structurereferente_id )
						)
				)
			);
			$this->set( 'referents', $referents );

			$this->set( 'action', $this->Dossier->Foyer->Personne->Contratinsertion->Actioninsertion->find( 'list' ) );

			$this->layout = '';
			$this->set( compact( 'contrats' ) );
		}
	}
?>