<?php
	/**
	 * Code source de la classe Thematiqueep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Abstractclass
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	require_once  APPLIBS.'cmis.php' ;

	/**
	 * Classe abstraite contenant les signatures de méthodes qui doivent être
	 * implémentées dans les classes des thématiques d'EP, et des méthodes pouvant
	 * être utilisées dans ces mêmes classes.
	 *
	 * @package app.Model.Abstractclass
	 */
	abstract class Thematiqueep extends AppModel
	{
		/**
		*
		*/
		abstract public function qdDossiersParListe( $commissionep_id, $niveauDecision );

		/**
		*
		*/
		abstract public function prepareFormData( $commissionep_id, $datas, $niveauDecision );

		/**
		*
		*/
		abstract public function saveDecisions( $data, $niveauDecision );

		/**
		*
		*/
		abstract public function qdProcesVerbal();

		/**
		*
		*/
		abstract public function getConvocationBeneficiaireEpPdf( $passagecommissionep_id );

		/**
		*
		*/
		abstract public function getDecisionPdf( $passagecommissionep_id, $user_id = null  );

		/**
		*
		*/
		abstract public function qdListeDossier( $commissionep_id = null );

		/**
		*
		*/
		abstract public function finaliser( $commissionep_id, $etape, $user_id );

		/**
		* Exécute les différentes méthods du modèle permettant la mise en cache.
		* Utilisé au préchargement de l'application (/prechargements/index).
		*
		* @return boolean true en cas de succès, false en cas d'erreur,
		* 	null pour les fonctions vides.
		*/
		public function prechargement() {
			$success = ( parent::prechargement() !== false );

			$getConvocationBeneficiaireEpPdf = $this->getConvocationBeneficiaireEpPdf( 0 );
			$success = empty( $getConvocationBeneficiaireEpPdf ) && $success;

			$getDecisionPdf = $this->getDecisionPdf( 0 );
			$success = empty( $getDecisionPdf ) && $success;

			return $success;
		}

		/**
		* Fonction inutile dans cette saisine donc elle retourne simplement true
		*/
		public function verrouiller( $commissionep_id, $etape ) {
			return true;
		}

		/**
		*
		*/
		public function saveDecisionUnique( $data, $niveauDecision ) {
			return true;
		}

		/**
		* Récupère (et met en cache) un querydata permettant de récupérer les
		* données afin de générer le PDF de convocation pour une thématique d'EP.
		*
		* @return array
		* FIXME: à utiliser et à compléter dans les modèles des thématiques
		* OK:
		*	- Signalementep93
		*	- Contratcomplexeep93
		*/
		public function _qdConvocationBeneficiaireEpPdf() {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$datas = Cache::read( $cacheKey );

			if( $datas === false ) {
				// Querydata
				$datas['querydata'] = array(
					'fields' => array_merge(
						$this->Dossierep->Passagecommissionep->fields(),
						$this->Dossierep->Passagecommissionep->Commissionep->fields(),
						$this->Dossierep->Passagecommissionep->Commissionep->Ep->fields(),
						$this->Dossierep->Passagecommissionep->User->fields(),
						$this->Dossierep->Passagecommissionep->User->Serviceinstructeur->fields(),
						$this->Dossierep->Passagecommissionep->Dossierep->fields(),
						$this->Dossierep->Passagecommissionep->Dossierep->Personne->fields(),
						$this->Dossierep->Passagecommissionep->Dossierep->Personne->Foyer->fields(),
						$this->Dossierep->Passagecommissionep->Dossierep->Personne->Foyer->Dossier->fields(),
						$this->Dossierep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->Adresse->fields(),
						$this->fields()
					),
					'joins' => array(
						$this->Dossierep->Passagecommissionep->join( 'Dossierep' ),
						$this->Dossierep->Passagecommissionep->join( 'Commissionep' ),
						$this->Dossierep->Passagecommissionep->Commissionep->join( 'Ep' ),
						$this->Dossierep->Passagecommissionep->join( 'User' ),
						$this->Dossierep->Passagecommissionep->User->join( 'Serviceinstructeur' ),
						$this->Dossierep->Passagecommissionep->Dossierep->join( $this->alias ),
						$this->Dossierep->Passagecommissionep->Dossierep->join( 'Personne' ),
						$this->Dossierep->Passagecommissionep->Dossierep->Personne->join( 'Foyer' ),
						$this->Dossierep->Passagecommissionep->Dossierep->Personne->Foyer->join( 'Dossier' ),
						$this->Dossierep->Passagecommissionep->Dossierep->Personne->Foyer->join( 'Adressefoyer' ),
						$this->Dossierep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->join( 'Adresse' )
					),
					'conditions' => array(
						'Adressefoyer.id IN ('
							.$this->Dossierep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' )
						.')'
					),
				);

				// Options
				$datas['options'] = Hash::merge(
					$this->enums(),
					array(
						'Personne' => array(
							'qual' => ClassRegistry::init( 'Option' )->qual()
						)
					),
					$this->Dossierep->enums(),
					$this->Dossierep->Passagecommissionep->enums()
				);

				Cache::write( $cacheKey, $datas );
			}

			return $datas;
		}

		/**
		* Récupère (et met en cache) un querydata permettant de récupérer les
		* données afin de générer le PDF de décision pour une thématique d'EP.
		*
		* @return array
		* FIXME: à utiliser et à compléter dans les modèles des thématiques
		* OK:
		*	- Signalementep (Signalementep93)
		*	- Contratcomplexeep93
		*/
		protected function _qdDecisionPdf() {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$querydata = Cache::read( $cacheKey );

			if( $querydata === false ) {
				// Querydata commun à toutes les thématiques
				$querydata = array(
					'fields' => array_merge(
						$this->Dossierep->Passagecommissionep->fields(),
						$this->Dossierep->Passagecommissionep->Commissionep->fields(),
						$this->Dossierep->Passagecommissionep->Commissionep->Ep->fields(),
						$this->Dossierep->fields(),
						$this->Dossierep->Personne->fields(),
						$this->Dossierep->Personne->Foyer->fields(),
						$this->Dossierep->Personne->Foyer->Dossier->fields(),
						$this->Dossierep->Personne->Foyer->Adressefoyer->fields(),
						$this->Dossierep->Personne->Foyer->Adressefoyer->Adresse->fields(),
						$this->Dossierep->Personne->PersonneReferent->Referent->fields(),
						$this->Dossierep->Personne->PersonneReferent->Referent->Structurereferente->fields()
					),
					'joins' => array(
						$this->Dossierep->Passagecommissionep->join( 'Dossierep' ),
						$this->Dossierep->Passagecommissionep->join( 'Commissionep' ),
						$this->Dossierep->Passagecommissionep->Commissionep->join( 'Ep' ),
						$this->Dossierep->join( 'Personne' ),
						$this->Dossierep->Personne->join( 'Foyer' ),
						$this->Dossierep->Personne->Foyer->join( 'Dossier' ),
						$this->Dossierep->Personne->Foyer->join( 'Adressefoyer' ),
						$this->Dossierep->Personne->Foyer->Adressefoyer->join( 'Adresse' ),
						$this->Dossierep->Personne->join('PersonneReferent'),
						$this->Dossierep->Personne->PersonneReferent->join( 'Referent' ),
						$this->Dossierep->Personne->PersonneReferent->Referent->join( 'Structurereferente' ),
					),
					'conditions' => array(
						'Adressefoyer.id IN ('
							.$this->Dossierep->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' )
						.')'
					),
					'contain' => false
				);

				// Querydata propre à la thématique, mais commun à l'ensemble des thématiques
				$modeleDecisionName = 'Decision'.strtolower( $this->alias );
				$tableDecisionName = Inflector::tableize( $modeleDecisionName );

				$qdThematique = array(
					'fields' => array_merge(
						$this->fields(),
						$this->Dossierep->Passagecommissionep->{$modeleDecisionName}->fields(),
						$this->Dossierep->Passagecommissionep->{$modeleDecisionName}->User->fields(), // FIXME: vérifier si toutes les tématiques ont ça
						$this->Dossierep->Passagecommissionep->{$modeleDecisionName}->User->Serviceinstructeur->fields()
					),
					'joins' => array(
						$this->Dossierep->join( $this->alias ),
						$this->Dossierep->Passagecommissionep->join( $modeleDecisionName ),
						$this->Dossierep->Passagecommissionep->{$modeleDecisionName}->join( 'User' ),
						$this->Dossierep->Passagecommissionep->{$modeleDecisionName}->User->join( 'Serviceinstructeur' )
					),
					'conditions' => array(
						"{$modeleDecisionName}.id IN ("
							.$this->Dossierep->Passagecommissionep->{$modeleDecisionName}->sq(
								array(
									'fields' => array( "{$tableDecisionName}.id" ),
									'alias' => $tableDecisionName,
									'conditions' => array(
										"{$tableDecisionName}.passagecommissionep_id = Passagecommissionep.id"
									),
									'order' => array( "{$tableDecisionName}.etape DESC" ),
									'limit' => 1
								)
							)
						.")"
					),
				);

				foreach( array( 'fields', 'joins', 'conditions' ) as $key ) {
					if( isset( $qdThematique[$key] ) ) {
						$querydata[$key] = array_merge( $querydata[$key], $qdThematique[$key] );
					}
				}

				Cache::write( $cacheKey, $querydata );
			}

			return $querydata;
		}

		/**
		* Renvoie un PDF de décision de comité d'EP pour un passage en commission donné.
		* Si le PDF a déja été crée et a été stocké (dans la table pdfs ou sur un
		* serveur CMS), on revoie celui-ci, sinon on génère le PDF et on le stocke.
		* Retourne le PDF s'il a été trouvé ou a pu être généré, false sinon.
		*
		* @param integer $passagecommissionep_id L'id du pasage du dossier en
		* 	commission d'EP pour lequel il faut générer le PDF.
		* @param array $gedooo_data Un array contenant les données permettant
		* 	de générer le PDF le cas échéant.
		* @param string $modeleOdt Le chemin absolu vers le modèle de document
		* 	permettant de générer le PDF le cas échéant.
		* @param array $options Les traductions de certains champs utilisées lors
		* 	 de la génération du PDF.
		* @return mixed
		*/
		protected function _getOrCreateDecisionPdf( $passagecommissionep_id, $gedooo_data, $modeleOdt, $options = array() ) {
			// Possède-t'on un PDF déjà stocké ?
			$pdfModel = ClassRegistry::init( 'Pdf' );
			$oldRecord = $pdfModel->find(
				'first',
				array(
					'conditions' => array(
						'modele' => 'Passagecommissionep',
						'fk_value' => $passagecommissionep_id
					)
				)
			);

			if( !empty( $oldRecord ) && empty( $oldRecord['Pdf']['document'] ) ) {
				$cmisPdf = Cmis::read( "/Passagecommissionep/{$passagecommissionep_id}.pdf", true );
				$oldRecord['Pdf']['document'] = $cmisPdf['content'];
			}

			if( !empty( $oldRecord['Pdf']['document'] ) ) {
				return $oldRecord['Pdf']['document'];
			}

			// Sinon, on génère le PDF
			$pdf =  $this->ged(
				$gedooo_data,
				$modeleOdt,
				false,
				$options
			);

			$oldRecord['Pdf']['modele'] = 'Passagecommissionep';
			$oldRecord['Pdf']['modeledoc'] = $modeleOdt;
			$oldRecord['Pdf']['fk_value'] = $passagecommissionep_id;
			$oldRecord['Pdf']['document'] = $pdf;

			$pdfModel->create( $oldRecord );
			$success = $pdfModel->save( null, array( 'atomic' => false ) );

			if( !$success ) {
				return false;
			}
			return $pdf;
		}

		/**
		 *
		 */
		public function nbErreursFinaliserCg( $commissionep_id, $niveauDecision ) {
			$conditions = array(
				'Dossierep.themeep' => Inflector::tableize( $this->name ),
				'Dossierep.id IN ( '.$this->Dossierep->Passagecommissionep->sq(
					array(
						'alias' => 'passagescommissionseps',
						'fields' => array(
							'passagescommissionseps.dossierep_id'
						),
						'conditions' => array(
							'passagescommissionseps.commissionep_id' => $commissionep_id,
							'passagescommissionseps.etatdossierep <>' => "decision{$niveauDecision}",
						)
					)
				).' )',
			);
			return $this->Dossierep->find( 'count', array( 'conditions' => $conditions ) );
		}

		/**
		 *
		 */
		public function nbDossiersATraiterCg( $commissionep_id ) {
			$conditions = array(
				'Dossierep.themeep' => Inflector::tableize( $this->name ),
				'Dossierep.id IN ( '.$this->Dossierep->Passagecommissionep->sq(
					array(
						'alias' => 'passagescommissionseps',
						'fields' => array(
							'passagescommissionseps.dossierep_id'
						),
						'conditions' => array(
							'passagescommissionseps.commissionep_id' => $commissionep_id,
							'passagescommissionseps.etatdossierep <>' => "decisionep", // FIXME: annulé/reporté
						)
					)
				).' )',
			);
			return $this->Dossierep->find( 'count', array( 'conditions' => $conditions ) );
		}

		/**
		 * @param array $dossierep
		 * @return integer
		 */
		protected function _prepareFormDataDecisionId( $dossierep ) {
			return @$dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['id'];
		}

		/**
		 * Retourne le querydata qui sera utilisé par la thématique pour la
		 * sélection des dossiers à associer à une commission d'EP donnée.
		 *
		 * @param integer $commissionep_id
		 * @return array
		 */
		public function qdListeDossierChoose( $commissionep_id = null ) {
			$departement = Configure::read( 'Cg.departement' );
			$query = $this->qdListeDossier( $commissionep_id );
			$query += $this->queryDefaults;

			$query['conditions']['Dossierep.actif'] = '1';

			if( $departement == 66 ) {
				$query['conditions'][] = 'Dossierep.id NOT IN ('.
					$this->Dossierep->Defautinsertionep66->sq(
						array(
							'fields' => array( 'defautsinsertionseps66.dossierep_id' ),
							'alias' => 'defautsinsertionseps66',
							'conditions' => array(
								'defautsinsertionseps66.dateimpressionconvoc IS NULL'
							)
						)
					)
				.' )';

				// Correction du bug des sélections des defautsinsertionseps66
				$query['conditions'][] =
				'Dossierep.id NOT IN (
					SELECT "defautsinsertionseps66"."dossierep_id" AS "defautsinsertionseps66__dossierep_id"
					FROM "defautsinsertionseps66", "bilansparcours66"
					WHERE "defautsinsertionseps66"."bilanparcours66_id" = "bilansparcours66"."id"
						AND "bilansparcours66"."positionbilan" IN (\'ajourne\', \'annule\', \'traite\')
				)';

				$delaiAvantSelection = Configure::read( 'Dossierep.delaiavantselection' );
				if( !empty( $delaiAvantSelection ) ) {
					$query['conditions'][] = array(
						'Dossierep.id IN (
							SELECT
								dossierseps.id
							FROM
								dossierseps
								WHERE
									date_trunc( \'day\', dossierseps.created ) <= ( DATE( NOW() ) - INTERVAL \''.$delaiAvantSelection.'\' )
						)'
					);
				}
			}

			return $query;
		}
	}
?>