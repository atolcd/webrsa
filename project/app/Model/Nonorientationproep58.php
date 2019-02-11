<?php
	/**
	 * Code source de la classe Nonorientationproep58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Nonorientationproep', 'Model/Abstractclass' );

	/**
	 * La classe Nonorientationproep58 ...
	 *
	 * @package app.Model
	 */
	class Nonorientationproep58 extends Nonorientationproep
	{
		public $belongsTo = array(
			'Decisionpropononorientationprocov58' => array(
				'className' => 'Decisionpropononorientationprocov58',
				'foreignKey' => 'decisionpropononorientationprocov58_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Dossierep' => array(
				'className' => 'Dossierep',
				'foreignKey' => 'dossierep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Nvorientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'nvorientstruct_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Orientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'orientstruct_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		/**
		* Chemin relatif pour les modèles de documents .odt utilisés lors des
		* impressions. Utiliser %s pour remplacer par l'alias.
		*/
		public $modelesOdt = array(
			// Convocation EP
			'Commissionep/convocationep_beneficiaire.odt',
			// Décision EP (décision CG)
			'%s/decision_reorientation.odt',
			'%s/decision_maintienref.odt',
			'%s/decision_annule.odt',
			'%s/decision_reporte.odt',
		);

		/**
		* Modèle de document pour la convocation du bénéficiaire.
		*/
		protected $_modeleOdtConvocationepBeneficiaire = 'Commissionep/convocationep_beneficiaire.odt';

		/**
		*
		*/

		public function finaliser( $commissionep_id, $etape, $user_id ) {
			$commissionep = $this->Dossierep->Passagecommissionep->Commissionep->find(
				'first',
				array(
					'conditions' => array( 'Commissionep.id' => $commissionep_id ),
					'contain' => array(
						'Ep' => array(
							'Regroupementep'
						)
					)
				)
			);

			$niveauDecisionFinale = $commissionep['Ep']['Regroupementep'][Inflector::underscore( $this->alias )];

			$dossierseps = $this->find(
				'all',
				array(
					'conditions' => array(
						'Dossierep.id IN ( '.
							$this->Dossierep->Passagecommissionep->sq(
								array(
									'fields' => array(
										'passagescommissionseps.dossierep_id'
									),
									'alias' => 'passagescommissionseps',
									'conditions' => array(
										'passagescommissionseps.commissionep_id' => $commissionep_id
									)
								)
							)
						.' )',
						'Dossierep.themeep' => Inflector::tableize( $this->alias )
					),
					'contain' => array(
						'Dossierep' => array(
							'Passagecommissionep' => array(
								'conditions' => array(
									'Passagecommissionep.commissionep_id' => $commissionep_id
								),
								'Decisionnonorientationproep58' => array(
									'conditions' => array(
										'Decisionnonorientationproep58.etape' => $etape
									)
								)
							)
						)
					)
				)
			);

			$success = true;

			if( $niveauDecisionFinale == "decision{$etape}" ) {
				$this->Orientstruct->Behaviors->detach( 'StorablePdf' );
				foreach( $dossierseps as $dossierep ) {
					if( !isset( $dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep58'][0]['decision'] ) || empty( $dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep58'][0]['decision'] ) ) {
						$success = false;
					}
					elseif ( in_array( $dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep58'][0]['decision'], array( 'reorientation', 'maintienref' ) ) ) {
						list($date_propo, $heure_propo) = explode( ' ', $dossierep['Nonorientationproep58']['created'] );
						list($date_valid, $heure_valid) = explode( ' ', $commissionep['Commissionep']['dateseance'] );

						$rgorient = $this->Orientstruct->WebrsaOrientstruct->rgorientMax( $dossierep['Dossierep']['personne_id'] ) + 1;
						$origine = ( $rgorient > 1 ? 'reorientation' : 'manuelle' );

						$orientstruct = array(
							'Orientstruct' => array(
								'personne_id' => $dossierep['Dossierep']['personne_id'],
								'typeorient_id' => @$dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep58'][0]['typeorient_id'],
								'structurereferente_id' => @$dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep58'][0]['structurereferente_id'],
								'referent_id' => @$dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep58'][0]['referent_id'],
								'date_propo' => $date_propo,
								'date_valid' => $date_valid,
								'statut_orient' => 'Orienté',
								'rgorient' => $rgorient,
								'origine' => $origine,
								'etatorient' => 'decision',
								'user_id' => $dossierep['Nonorientationproep58']['user_id']
							)
						);

						$this->Orientstruct->create( $orientstruct );
						$success = $this->Orientstruct->save( null, array( 'atomic' => false ) ) && $success;

						// Mise à jour de l'enregistrement de la thématique avec l'id de la nouvelle orientation
						$success = $success && $this->updateAllUnBound(
							array( "\"{$this->alias}\".\"nvorientstruct_id\"" => $this->Orientstruct->id ),
							array( "\"{$this->alias}\".\"id\"" => $dossierep[$this->alias]['id'] )
						);

						$success = $this->Orientstruct->Personne->PersonneReferent->changeReferentParcours(
							$dossierep['Dossierep']['personne_id'],
							@$dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep58'][0]['referent_id'],
							array(
								'PersonneReferent' => array(
									'personne_id' => $dossierep['Dossierep']['personne_id'],
									'referent_id' => @$dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep58'][0]['referent_id'],
									'dddesignation' => $date_valid,
									'structurereferente_id' => @$dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep58'][0]['structurereferente_id'],
									'user_id' => $dossierep['Nonorientationproep58']['user_id']
								)
							)
						) && $success;
					}
				}
				$this->Orientstruct->Behaviors->attach( 'StorablePdf' );
			}

			return $success;
		}

		/**
		* Fonction retournant un querydata qui va permettre de retrouver des dossiers d'EP
		*/
		public function qdListeDossier( $commissionep_id = null ) {
			$querydata = parent::qdListeDossier( $commissionep_id );

				$joins = array(
					$this->Dossierep->Nonorientationproep58->join( 'Decisionpropononorientationprocov58' ),
					$this->Dossierep->Nonorientationproep58->Decisionpropononorientationprocov58->join( 'Passagecov58' ),
					$this->Dossierep->Nonorientationproep58->Decisionpropononorientationprocov58->Passagecov58->join( 'Cov58' )
				);

				$querydata['joins'] = array_merge( $querydata['joins'], $joins );
				$querydata['fields'][] = 'Cov58.datecommission';
				$querydata['fields'][] = 'Passagecommissionep.heureseance';


			return $querydata;
		}

		/**
		 * Retourne une partie de querydata concernant la thématique pour le PV d'EP.
		 *
		 * @return array
		 */
		public function qdProcesVerbal() {
			$querydata = parent::qdProcesVerbal();

			$modeleDecisionPart = strtolower( 'Decisionnonorientationproep'.Configure::read( 'Cg.departement' ) );
			$aliases = array( 'Referent' => "Referentdecnonopro58" );

			$fields = array_merge(
				$this->Dossierep->Passagecommissionep->Decisionnonorientationproep58->Referent->fields()
			);
			$fields = array_words_replace( $fields, $aliases );
			$querydata['fields'] = array_merge( $querydata['fields'], $fields );


			$joins = array(
				$this->Dossierep->Passagecommissionep->Decisionnonorientationproep58->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
			);
			$joins = array_words_replace( $joins, $aliases );
			$querydata['joins'] = array_merge( $querydata['joins'], $joins );

			return $querydata;
		}
	}
?>