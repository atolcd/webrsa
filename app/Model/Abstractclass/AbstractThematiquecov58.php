<?php
	/**
	 * Code source de la classe AbstractThematiquecov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Abstractclass
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe abstraite contenant les signatures de méthodes qui doivent être
	 * implémentées dans les classes des thématiques de COV, et des méthodes pouvant
	 * être utilisées dans ces mêmes classes.
	 *
	 * 	- Nonorientationprocov58 -> OK
	 * 	- Propoorientsocialecov58 -> OK
	 * 	- Propocontratinsertioncov58 -> OK
	 * 	- Propononorientationprocov58 -> OK
	 * 	- Propoorientationcov58 -> OK
	 *
	 * @package app.Model.Abstractclass
	 */
	abstract class AbstractThematiquecov58 extends AppModel
	{

		/**
		 * Fonction retournant un querydata qui va permettre de retrouver des
		 * dossiers de COV à sélectionner pour passer dans une commission donnée,
		 * ainsi que la liste des dossiers déjà sélectionnés pour passer dans
		 * cette comission.
		 *
	     * @param integer $cov58_id
		 * @return array
		 */
		public function qdListeDossier( $cov58_id = null ) {
			$return = array(
				'fields' => array(
					'Dossiercov58.id',
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.dtnai',
					'Personne.prenom',
					'Dossier.numdemrsa',
					'Adresse.nomcom',
					'Passagecov58.id',
					'Passagecov58.cov58_id',
					'Passagecov58.etatdossiercov'
				)
			);

			if( !empty( $cov58_id ) ) {
				$join = $this->join( 'Dossiercov58', array( 'type' => 'INNER' ) );
			}
			else {
				$join = $this->Dossiercov58->join( $this->alias, array( 'type' => 'INNER' ) );
			}

			$return['joins'] = array(
				$join,
				$this->Dossiercov58->join( 'Themecov58', array( 'type' => 'INNER' ) ),
				$this->Dossiercov58->join( 'Personne', array( 'type' => 'INNER' ) ),
				$this->Dossiercov58->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
				$this->Dossiercov58->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
				$this->Dossiercov58->Personne->Foyer->join(
					'Adressefoyer',
					array(
						'type' => 'INNER',
						'conditions' => array( 'Adressefoyer.rgadr' => '01' )
					)
				),
				$this->Dossiercov58->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
				$this->Dossiercov58->join(
					'Passagecov58', array(
						'type' => 'LEFT OUTER',
						'conditions' => empty( $cov58_id ) ? array() : array(
							'OR' => array(
								'Passagecov58.cov58_id IS NULL',
								'Passagecov58.cov58_id' => $cov58_id
							)
						)
					)
				)
			);

			return $return;
		}

		/**
		 * Retourne une partie de querydata propre à la thématique et nécessaire
		 * à l'impression de l'ordre du jour.
		 *
		 * @return array
		 */
		public function qdOrdreDuJour() {
			return array(
				'fields' => $this->fields(),
				'joins' => array(
					$this->Dossiercov58->join( $this->alias, array( 'type' => 'LEFT OUTER' ) )
				)
			);
		}

		/**
		 * Retourne le querydata utilisé dans la partie décisions d'une COV.
		 *
		 * A surcharger dans les classes filles afin d'ajouter le modèle de la
		 * thématique et le modèle de décision dans le contain.
		 *
		 * @param integer $cov58_id
		 * @return array
		 */
		public function qdDossiersParListe( $cov58_id ) {
			$themecov58 = Inflector::tableize( $this->alias );

			$result = array(
				'conditions' => array(
					'Dossiercov58.themecov58' => $themecov58,
					'Dossiercov58.id IN ( '.
						$this->Dossiercov58->Passagecov58->sq(
							array(
								'fields' => array(
									'passagescovs58.dossiercov58_id'
								),
								'alias' => 'passagescovs58',
								'conditions' => array(
									'passagescovs58.cov58_id' => $cov58_id
								)
							)
						)
					.' )'
				),
				'contain' => array(
					'Personne' => array(
						'Foyer' => array(
							'Adressefoyer' => array(
								'conditions' => array(
									'Adressefoyer.rgadr' => '01'
								),
								'Adresse'
							)
						)
					),
					'Passagecov58' => array(
						'conditions' => array(
							'Passagecov58.cov58_id' => $cov58_id
						)
					)
				)
			);

			return $result;
		}

		/**
		 * Tentative de sauvegarde des décisions de la thématique.
		 *
		 * @param array $data
		 * @return boolean
		 */
		abstract public function saveDecisions( $data );

		/**
		 * Retourne un morceau de querydata propre à la thématique utilisé pour
		 * l'impression du procès-verbal de la COV.
		 *
		 * @return array
		 */
		public function qdProcesVerbal() {
			$modeleDecision = 'Decision'.Inflector::underscore( $this->alias );

			$result = array(
				'fields' => array_merge(
					$this->fields(),
					$this->Dossiercov58->Passagecov58->{$modeleDecision}->fields()
				),
				'contain' => false,
				'joins' => array(
					$this->Dossiercov58->join( $this->alias, array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossiercov58->Passagecov58->join(
						$modeleDecision,
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								"{$modeleDecision}.etapecov" => 'finalise'
							)
						)
					)
				),
				'conditions' => array(),
			);

			return $result;
		}

		// deprecated ?
//		public abstract function getPdfDecision( $passagecov58_id );

		/**
		 * Retourne un querydata permettant de trouver les dossiers de COV en
		 * attente de traitement pour un allocataire donné.
		 *
		 * @deprecated voir les méthodes getReorientationsEnCours() de Dossiercov58
		 * et de Dossierep + l'appel à cette méthode dans ContratsinsertionController.
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function qdEnCours( $personne_id ) {
			$sqDernierPassagecov58 = $this->Dossiercov58->Passagecov58->sqDernier();

			$query = array(
				'fields' => array(
					"{$this->alias}.id",
					"{$this->alias}.dossiercov58_id",
					"{$this->alias}.created",
					"Dossiercov58.personne_id",
					"Dossiercov58.themecov58",
					"Passagecov58.etatdossiercov",
					"Personne.id",
					"Personne.nom",
					"Personne.prenom"
				),
				'conditions' => array(
					'Dossiercov58.personne_id' => $personne_id,
					'Dossiercov58.themecov58' => Inflector::tableize( $this->alias ),
					array(
						'OR' => array(
							'Passagecov58.etatdossiercov NOT' => array( 'traite', 'annule' ),
							'Passagecov58.etatdossiercov IS NULL'
						),
					),
					array(
						'OR' => array(
							"Passagecov58.id IN ( {$sqDernierPassagecov58} )",
							'Passagecov58.etatdossiercov IS NULL'
						),
					),
				),
				'joins' => array(
					$this->join( 'Dossiercov58', array( 'type' => 'INNER' ) ),
					$this->Dossiercov58->join( 'Passagecov58', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossiercov58->join( 'Personne', array( 'type' => 'INNER' ) )
				),
				'contain' => false,
			);

			return $query;
		}
	}
?>