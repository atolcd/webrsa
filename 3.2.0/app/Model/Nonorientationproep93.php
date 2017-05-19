<?php
	/**
	 * Code source de la classe Nonorientationproep93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Nonorientationproep', 'Model/Abstractclass' );

	/**
	 * La classe Nonorientationproep93 ...
	 *
	 * @package app.Model
	 */
	class Nonorientationproep93 extends Nonorientationproep
	{
		/**
		* Chemin relatif pour les modèles de documents .odt utilisés lors des
		* impressions. Utiliser %s pour remplacer par l'alias.
		*/
		public $modelesOdt = array(
			// Convocation EP
			'%s/convocationep_beneficiaire.odt',
			// Décision EP (décision CG)
			'%s/decision_reorientation.odt',
			'%s/decision_maintienref.odt',
			'%s/decision_annule.odt',
			'%s/decision_reporte.odt'
		);

		/**
		 * Classes utilisées par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'WebrsaOrientstruct' );

		public $belongsTo = array(
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
			'Nvorientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'nvorientstruct_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		/**
		* Modèle de document pour la convocation du bénéficiaire.
		*/
		protected $_modeleOdtConvocationepBeneficiaire = 'Nonorientationproep93/convocationep_beneficiaire.odt';

		/**
		 *
		 */

		public function prepareFormData( $commissionep_id, $datas, $niveauDecision ) {
			// Doit-on prendre une décision à ce niveau ?
			$themes = $this->Dossierep->Passagecommissionep->Commissionep->WebrsaCommissionep->themesTraites( $commissionep_id );
			$niveauFinal = Hash::get( $themes, Inflector::underscore($this->alias) );
			if( ( $niveauFinal == 'ep' ) && ( $niveauDecision == 'cg' ) ) {
				return array();
			}

			$formData = array();
			foreach( $datas as $key => $dossierep ) {
				$formData['Decision'.Inflector::underscore( $this->alias )][$key]['passagecommissionep_id'] = @$datas[$key]['Passagecommissionep'][0]['id'];

				// On modifie les enregistrements de cette étape
				if( @$dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['etape'] == $niveauDecision ) {
					$formData['Decision'.Inflector::underscore( $this->alias )][$key] = @$dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0];
					$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id'] = implode(
						'_',
						array(
							$formData['Decision'.Inflector::underscore( $this->alias )][$key]['typeorient_id'],
							$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id']
						)
					);
				}
				// On ajoute les enregistrements de cette étape
				else {
					if( $niveauDecision == 'cg' ) {
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['decisionpcg'] = 'valide';
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['decision'] = @$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['decision'];
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['raisonnonpassage'] = @$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['raisonnonpassage'];
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id'] = @$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['typeorient_id'].'_'.@$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['structurereferente_id'];
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['typeorient_id'] = @$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['typeorient_id'];
					}
				}
			}
			return $formData;
		}

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
								'Decisionnonorientationproep93' => array(
									'conditions' => array(
										'Decisionnonorientationproep93.etape' => $etape
									)
								)
							)
						)
					)
				)
			);

			$success = true;

			if( $niveauDecisionFinale == "decision{$etape}" ) {

				foreach( $dossierseps as $dossierep ) {
					if( !isset( $dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep93'][0]['decision'] ) ) {
						$success = false;
					}
					elseif ( $dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep93'][0]['decision'] == 'reorientation' ) {
						list($date_propo, $heure_propo) = explode( ' ', $dossierep['Nonorientationproep93']['created'] );
						list($date_valid, $heure_valid) = explode( ' ', $commissionep['Commissionep']['dateseance'] );

						$rgorient = $this->Orientstruct->WebrsaOrientstruct->rgorientMax( $dossierep['Dossierep']['personne_id'] ) + 1;
						$origine = ( $rgorient > 1 ? 'reorientation' : 'manuelle' );

						$orientstruct = array(
							'Orientstruct' => array(
								'personne_id' => $dossierep['Dossierep']['personne_id'],
								'typeorient_id' => @$dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep93'][0]['typeorient_id'],
								'structurereferente_id' => @$dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep93'][0]['structurereferente_id'],
								'date_propo' => $date_propo,
								'date_valid' => $date_valid,
								'statut_orient' => 'Orienté',
								'rgorient' => $rgorient,
								'origine' => $origine,
								'etatorient' => 'decision',
								'user_id' => $dossierep['Nonorientationproep93']['user_id']
							)
						);

						$this->Orientstruct->create( $orientstruct );
						$success = $this->Orientstruct->save( null, array( 'atomic' => false ) ) && $success;

						// Mise à jour de l'enregistrement de la thématique avec l'id de la nouvelle orientation
						$success = $success && $this->updateAllUnBound(
							array( "\"{$this->alias}\".\"nvorientstruct_id\"" => $this->Orientstruct->id ),
							array( "\"{$this->alias}\".\"id\"" => $dossierep[$this->alias]['id'] )
						);

						// Fin de désignation du référent de la personne
						$this->Orientstruct->Personne->PersonneReferent->updateAllUnBound(
							array( 'PersonneReferent.dfdesignation' => "'".$date_valid."'" ),
							array(
								'"PersonneReferent"."personne_id"' => $dossierep['Dossierep']['personne_id'],
								'"PersonneReferent"."dfdesignation" IS NULL'
							)
						);

						// Si l'allocataire est réorienté et qu'il avait un D1, il sort de l'accompagnement
						$dossierep['Decisionnonorientationproep93'] = $dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep93'][0];
						$success = $this->WebrsaOrientstruct->reorientationEpQuestionnaired2pdv93Auto( $dossierep, 'Decisionnonorientationproep93', $this->Orientstruct->id ) && $success;
					}
				}
			}

			return $success;
		}
	}
?>