<?php
	/**
	 * Fichier source du plugin StatistiquesDrees.
	 *
	 * PHP 7.2
	 *
	 * @package StatistiquesDrees.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 * @author Atol CD
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Statistiquedrees ...
	 *
	 * @package app.Model
	 */
	class Statistiquedrees extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Statistiquedrees';

		/**
		 * Ce modèle n'est lié à aucune table.
		 *
		 * @var boolean
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Conditionnable'
		);

		/**
		 * Autres modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Foyer' );

		/**
		 * Les différentes tranches qui sont utilisées dans les tableaux
		 * "Indicateurs d'orientations" et "Indicateurs de réorientations".
		 *
		 * @var array
		 */
		public $tranches = array(
			'age' => array(
				'0 - 24',
				'25 - 29',
				'30 - 39',
				'40 - 49',
				'50 - 59',
				'>= 60',
				'Age inconnu'
			),
			'sexe' => array(
				'Femme',
				'Homme',
				'Sexe inconnu'
			),
			'sitfam' => array(
				'Personne seule sans enfant',
				'Personne seule avec enfant(s)',
				'Personne en couple sans enfant',
				'Personne en couple avec enfant(s)',
				'Situation familiale non connue'
			),
			'anciennete' => array(
				'moins de 6 mois',
				'6 mois et moins 1 an',
				'1 an et moins de 2 ans',
				'2 ans et moins de 5 ans',
				'5 ans et plus',
				'Ancienneté non connue',
			),
			'nivetu' => array(
				'Vbis et VI',
				'V',
				'IV',
				'III, II, I',
				'Niveau de formation non connu'
			),
		);


		/**
		 *
		 * @param array $search
		 * @return array
		 */
		private function _initialiseResults( &$resultats, $tableau = 'Tableau1' ) {
			foreach ($this->tranches as $key => $value) {
				foreach ($value as $value2) {
					$this->{'_initialiseRowInformations'.$tableau}($resultats, $key, $value2);
				}
			}
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		private function _getFieldsIndicateurAge( array $search ) {
			$annee = Hash::get( $search, 'Search.annee' );

			return
				'(
					CASE
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP\''.$annee.'-12-31\', "Personne"."dtnai" ) ) BETWEEN 0 AND 24 THEN \'0 - 24\'
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP \''.$annee.'-12-31\', "Personne"."dtnai" ) ) BETWEEN 25 AND 29 THEN \'25 - 29\'
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP \''.$annee.'-12-31\', "Personne"."dtnai" ) ) BETWEEN 30 AND 39 THEN \'30 - 39\'
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP \''.$annee.'-12-31\', "Personne"."dtnai" ) ) BETWEEN 40 AND 49 THEN \'40 - 49\'
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP \''.$annee.'-12-31\', "Personne"."dtnai" ) ) BETWEEN 50 AND 59 THEN \'50 - 59\'
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP \''.$annee.'-12-31\', "Personne"."dtnai" ) ) >= 60 THEN \'>= 60\'
						ELSE \'age_nc\'
					END
				) AS "age"';
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		private function _getFieldsIndicateurAnciennete( array $search ) {
			$annee = Hash::get( $search, 'Search.annee' );

			return
				'(
					CASE
						WHEN "Dossier"."dtdemrsa" BETWEEN ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'6\' MONTH - INTERVAL \'1\' DAY ) AND ( TIMESTAMP \''.$annee.'-12-31\' ) THEN \'moins de 6 mois\'
						WHEN "Dossier"."dtdemrsa" BETWEEN ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'1\' YEAR - INTERVAL \'1\' DAY ) AND ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'6\' MONTH ) THEN \'6 mois et moins 1 an\'
						WHEN "Dossier"."dtdemrsa" BETWEEN ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'2\' YEAR - INTERVAL \'1\' DAY ) AND ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'1\' YEAR ) THEN \'1 an et moins de 2 ans\'
						WHEN "Dossier"."dtdemrsa" BETWEEN ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'5\' YEAR - INTERVAL \'1\' DAY ) AND ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'2\' YEAR ) THEN \'2 ans et moins de 5 ans\'
						WHEN "Dossier"."dtdemrsa" < ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'5\' YEAR ) THEN \'5 ans et plus\'
						ELSE \'Ancienneté non connue\'
					END
				) AS "anciennete"';
		}

		/**
		 * Retourne les catégories de niveaux d'études renseigné dans les DspRev
		 * (suivant la date de dernière modification et l'année demandée) ou
		 * les Dsp.
		 *
		 * Utilisées dans les tableaux "1 - Orientation des personnes dans le champ
		 * des Droits et Devoirs au 31 décembre de l'année, au sens du type de
		 * parcours" et "4 - Nombre et profil des personnes réorientées au cours
		 * de l'année, au sens de la loi " de la partie "Questionnaire orientation".
		 *
		 * @see Statistiquedrees::_completeQueryDspDspRev()
		 *
		 * @param array $search
		 * @return array
		 */
		private function _getFieldsIndicateurNivetu( array $search ) {
			return
				'(
					CASE
						WHEN "DspRev"."nivetu" IN ( \'1205\', \'1206\', \'1207\' ) THEN \'Vbis et VI\'
						WHEN "DspRev"."nivetu" IN ( \'1204\' ) THEN \'V\'
						WHEN "DspRev"."nivetu" IN ( \'1203\' ) THEN \'IV\'
						WHEN "DspRev"."nivetu" IN ( \'1201\', \'1202\') THEN \'III, II, I\'
						WHEN "DspRev"."id" IS NULL AND "Dsp"."nivetu" IN ( \'1205\', \'1206\', \'1207\' ) THEN \'Vbis et VI\'
						WHEN "DspRev"."id" IS NULL AND "Dsp"."nivetu" IN ( \'1204\' ) THEN \'V\'
						WHEN "DspRev"."id" IS NULL AND "Dsp"."nivetu" IN ( \'1203\' ) THEN \'IV\'
						WHEN "DspRev"."id" IS NULL AND "Dsp"."nivetu" IN ( \'1201\', \'1202\') THEN \'III, II, I\'
						ELSE \'Niveau de formation non connu\'
					END
				) AS "nivetu"';
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		private function _getFieldsIndicateurSexe( array $search ) {
			$parts = array(
				'femme' => '"Personne"."sexe" = \'2\'',
				'homme' => '"Personne"."sexe" = \'1\'',
			);

			return
				'(
					CASE
						WHEN (
							'.$parts['homme'].'
						) THEN \'Homme\'
						WHEN (
							'.$parts['femme'].'
						) THEN \'Femme\'

						ELSE \'Sexe inconnu\'
					END
				) AS "sexe"';
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		private function _getFieldsIndicateurCer( $annee ) {
			return
				'(
					CASE
						WHEN ("Contratinsertion"."decision_ci" = \'V\' AND "Contratinsertion"."dd_ci" <= \''.$annee.'-12-31\' AND "Contratinsertion"."df_ci" >= \''.$annee.'-12-31\') THEN \'cer\'
						ELSE \'Sans CER\'
					END
				) AS "contrat_cer"';
		}

		/**
		 * Retourne la requête de base utilisée dans les différents questionnaires.
		 *
		 * Les modèles utilisés sont: Dossier, Detaildroitrsa, Foyer, Situationdossierrsa,
		 * Adressefoyer, Personne, Adresse, Prestation, Calculdroitrsa.
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getBaseQuery( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );

			$conditions = array(
				array(
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.rgadr' => '01',
					)
				),
				'Prestation.rolepers' => array( 'DEM', 'CJT' ),
				'Dossier.dtdemrsa <=' => "{$annee}-12-31",
			);

			// Seulement les derniers dossiers des allocataires
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, array( 'Dossier' => array( 'dernier' => true ) ) );

			// Condition sur le service instructeur
			$conditions[] = $this->_getConditionsServiceInstructeur( $search );

			// Conditions sur l'adresse de l'allocataire
			$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

			$query = array(
				'joins' => array(
					$Dossier->join( 'Detaildroitrsa', array( 'type' => 'INNER' ) ),
					$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
				),
				'contain' => false,
				'conditions' => $conditions,
			);

			return $query;
		}

		/**
		 * Retourne le query de base pour la partie "Questionnaire orientation",
		 * "1 - Orientation des personnes dans le champ des Droits et Devoirs au
		 * 31 décembre de l'année, au sens du type de parcours".
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getBaseQueryIndicateursOrientations( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );

			$query = $this->_getBaseQuery( $search );
			$query = $this->_completeQueryDspDspRev( $query, $search );

			// On ne prend en compte que la dernière orientation en cours au 31/12 de cette année-là
			$sq = $this->_sqDerniereOrientation( $annee );
			$query['joins'][] = $Dossier->Foyer->Personne->join(
				'Orientstruct',
				array(
					'type' => 'LEFT OUTER',
					'conditions' => array(
						"Orientstruct.id IN ( {$sq} )"
					)
				)
			);
			$query['joins'][] = $Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) );

			return $query;
		}

		/**
		 * Filtre par service instructeur.
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getConditionsServiceInstructeur( $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$serviceinstructeur_id = trim( Hash::get( $search, 'Search.serviceinstructeur' ) );

			if( !empty( $serviceinstructeur_id ) ) {
				$sq = $Dossier->Suiviinstruction->sq(
					array(
						'alias' => 'suivisinstruction',
						'fields' => array( 'suivisinstruction.dossier_id' ),
						'contain' => false,
						'joins' => array(
							array_words_replace(
								$Dossier->Suiviinstruction->join( 'Serviceinstructeur', array( 'type' => 'INNER' ) ),
								array( 'Suiviinstruction' => 'suivisinstruction', 'Serviceinstructeur' => 'servicesinstructeurs' )
							)
						),
						'conditions' => array(
							'servicesinstructeurs.id' => $serviceinstructeur_id
						)
					)
				);

				return array( "Dossier.id IN ( {$sq} )" );
			}

			return array();
		}

		/**
		 * Retourne une sous-requête permettant de cibler la dernière orientation
		 * d'un allocataire pour une année donnée.
		 *
		 * @param type $annee
		 * @return type
		 */
		protected function _sqDerniereOrientation( $annee ) {
			$Orientstruct = ClassRegistry::init( 'Orientstruct' );
			$personneIdFied = 'Personne.id';

			return $Orientstruct->sq(
				array(
					'fields' => array(
						'orientsstructs.id'
					),
					'alias' => 'orientsstructs',
					'conditions' => array(
						"orientsstructs.personne_id = {$personneIdFied}",
						'orientsstructs.statut_orient = \'Orienté\'',
						'orientsstructs.date_valid IS NOT NULL',
						'orientsstructs.date_valid <=' => "{$annee}-12-31",
					),
					'order' => array( 'orientsstructs.date_valid DESC' ),
					'limit' => 1
				)
			);
		}

		/**
		 * Complète le query avec les jointures nécessaires pour obtenir soit les
		 * DspRev (d'une année donnée), soit les Dsp d'un allocataire.
		 *
		 * Utilisées dans les tableaux "1 - Orientation des personnes dans le champ
		 * des Droits et Devoirs au 31 décembre de l'année, au sens du type de
		 * parcours" et "4 - Nombre et profil des personnes réorientées au cours
		 * de l'année, au sens de la loi " de la partie "Questionnaire orientation".
		 *
		 * @see Statistiquedrees::_getFieldsNivetu()
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		protected function _completeQueryDspDspRev( array $query, array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );

			// On prend la dernière DspRev pour l'année en question ou la dernière Dsp
			$sq = $Dossier->Foyer->Personne->DspRev->sq(
				array(
					'alias' => 'dsps_revs',
					'fields' => array( 'dsps_revs.id' ),
					'conditions' => array(
						'dsps_revs.personne_id = Personne.id',
						'dsps_revs.modified <=' => "{$annee}-12-31"
					),
					'contain' => false,
					'order' => array( 'dsps_revs.modified DESC' ),
					'limit' => 1
				)
			);
			$query['joins'][] = $Dossier->Foyer->Personne->join(
				'DspRev',
				array(
					'type' => 'LEFT OUTER',
					'conditions' => array(
						"DspRev.id IN ( {$sq} )"
					)
				)
			);

			$query['joins'][] = $Dossier->Foyer->Personne->join(
				'Dsp',
				array(
					'type' => 'LEFT OUTER',
					'conditions' => array(
						'DspRev.id IS NULL',
						'Dsp.id IN ( '.$Dossier->Foyer->Personne->Dsp->WebrsaDsp->sqDerniereDsp().' )'
					)
				)
			);

			return $query;
		}

		/**
		* Complète le querydata, pour l'année donnée, afin de cibler les
		* allocataires soumis à droits et devoirs, via la table historiquesdroits
		* ou les tables situationsdossiersrsa et calculsdroitsrsa
		*
		* @see clé de configuration Statistiquedrees.useHistoriquedroit
		* @see clé de configuration Statistiquedrees.conditions_droits_et_devoirs
		*
		* @param array $query
		* @param integer $annee
		* @param boolean $soumisDd
		* @param array $conditions Conditions supplémentaires à utiliser dans la sous-requête.
		* @return array
		*/
		protected function _completeQuerySoumisDd( array $query, $annee, $soumisDd = null, array $conditions = array() ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			//@fixme: true au 93
			$useHistoriquedroit = (boolean)Configure::read( 'Statistiquedrees.useHistoriquedroit' );
			if( true === $useHistoriquedroit ) {
				$query = $this->_completeQueryDernierHistoriqueDroit( $query, $annee, $soumisDd, $conditions );
			}
			else {
				$type = true === $soumisDd ? 'INNER' : 'LEFT OUTER';

				// Correction erreur :  Duplicate alias: 7 ERREUR: le nom de la table est spécifié plus d'une fois
				$addJoin = true ;
				foreach ($query['joins'] as $join) {
					if ($join['table'] == '"situationsdossiersrsa"') {
						$addJoin = false;
						break;
					}
				}
				if ($addJoin) {
					$query['joins'][] = $Dossier->join( 'Situationdossierrsa', array( 'type' => $type ) );
				}
				$addJoin = true ;
				foreach ($query['joins'] as $join) {
					if ($join['table'] == '"calculsdroitsrsa"') {
						$addJoin = false;
						break;
					}
				}
				if ($addJoin) {
					$query['joins'][] = $Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => $type ) );
				}
				// FIN Correction erreur

				if( null !== $soumisDd ) {
					if( true === $soumisDd ) {
						$conditions[] = $this->_getConditionsDroitsEtDevoirs();
					}
					else {
						$conditions[] = array(
							'OR' => array(
								'Calculdroitrsa.id IS NULL',
								'Situationdossierrsa.id IS NULL',
								array(
									array(
										'Calculdroitrsa.id IS NOT NULL',
										'Situationdossierrsa.id IS NOT NULL'
									),
									'NOT' => array( $this->_getConditionsDroitsEtDevoirs() )
								)
							)
						);
					}
				}

				$query['conditions'][] = $conditions;
			}

			return $query;
		}

		/**
		* Complète le querydata avec une jointure sur la table historiquesdroits
		* et l'ajout éventuel de conditions pour obtenir ou non des allocataires
		* soumis à droits et devoirs.
		*
		* @param array $query
		* @param integer $annee
		* @param boolean $soumisDd
		* @param array $conditions Conditions supplémentaires à utiliser dans la sous-requête.
		* @return array
		*/
		protected function _completeQueryDernierHistoriqueDroit( array $query, $annee, $soumisDd = null, array $conditions = array() ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$replacements = array(
				'Situationdossierrsa' => 'Historiquedroit',
				'Calculdroitrsa' => 'Historiquedroit'
			);

			$sqHistoriquedroit = $Dossier->Foyer->Personne->sqLatest(
				'Historiquedroit',
				'created',
				array_merge(
					array(
						"Historiquedroit.created::DATE <= '{$annee}-12-31'",
						"Historiquedroit.modified::DATE >= '{$annee}-12-31'"
					),
					$conditions
				),
				false
			);

			$conditions = array();

			$query['joins'][] = $Dossier->Foyer->Personne->join(
				'Historiquedroit',
				array(
					'type' => true === $soumisDd ? 'INNER' : 'LEFT OUTER',
					'conditions' => array( "Historiquedroit.id IN ( {$sqHistoriquedroit} )" )
				)
			);

			if( null !== $soumisDd ) {
				if( true === $soumisDd ) {
					$conditions[] = alias( $this->_getConditionsDroitsEtDevoirs(), $replacements );
				}
				else {
					$conditions[] = array(
						'OR' => array(
							'Historiquedroit.id IS NULL',
							array(
								'Historiquedroit.id IS NOT NULL',
								'NOT' => array(
									alias( $this->_getConditionsDroitsEtDevoirs(), $replacements )
								)
							)
						)
					);
				}
			}

			$query['conditions'][] = $conditions;
			return $query;
		}

		/**
		* Complete query CER
		*
		* @param array $query
		* @return array
		*/
		protected function _completeQueryCer( array $query, $annee, $configuration ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$query['joins'][] = $Dossier->Foyer->Personne->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) );

			if (!$configuration['actionscandidats']) {
				$query['joins'][] = $Dossier->Foyer->Personne->Contratinsertion->join( 'Cer93', array( 'type' => 'LEFT' ) );
			}
			$query['conditions'][] = '"Orientstruct"."id" IS NOT NULL';

			$query['fields'][] = $this->_getFieldsIndicateurCer($annee);
			$query['fields'][] = '"Contratinsertion"."duree_engag" AS "duree_engag"';
			$query['fields'][] = '"Contratinsertion"."duree_engag" AS "duree_cer"';

			return $query;
		}

		/**
		* Complete query CER
		*
		* @param array $query
		* @return array
		*/
		protected function _completeQueryRestrictionCer( array $query, $annee, $configuration ) {
			$query['conditions'][] = '"Structurereferente"."id" NOT IN ('.implode (', ', $configuration['organismes']['orientes_pole_emploi']).')';
			$query['conditions'][] = 'Contratinsertion.decision_ci = \'V\'';
			$query['conditions'][] = array(
				'Contratinsertion.dd_ci <=' => "{$annee}-12-31",
				'Contratinsertion.df_ci >=' => "{$annee}-12-31",
			);

			return $query;
		}

		/**
		 * Retourne les conditions permettant de s'assurer qu'un allocataire soit
		 * dans le champ des droits et devoirs.
		 *
		 * @see Statistiquedrees.conditions_droits_et_devoirs dans le webrsa.inc
		 *
		 * @return array
		 */
		protected function _getConditionsDroitsEtDevoirs() {
			return (array)Configure::read( 'Statistiquedrees.conditions_droits_et_devoirs' );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau( array $search , $annee) {
			$base = $this->_getBaseQueryIndicateursOrientations( $search );
			$base['fields'] = array (
				'DISTINCT ON ("Personne"."id") "Personne"."id" AS "idPersonne"',
				'"Typeorient"."id" AS "idTypeorient"',
				'"Orientstruct"."id" AS "idOrientstruct"',
				'"Orientstruct"."date_propo" AS "dateOrientstruct"',
				'"Foyer"."sitfam" AS "situationFamiliale"',
				'(CASE WHEN ( EXISTS ( SELECT enfants.id FROM personnes AS enfants INNER JOIN prestations ON ( enfants.id = prestations.personne_id AND prestations.natprest = \'RSA\' ) WHERE enfants.foyer_id= "Foyer"."id" AND prestations.rolepers = \'ENF\' ) ) THEN \'1\' ELSE \'0\' END ) AS "enfant"',
				'"Structurereferente"."id" AS "idStructurereferente"',
				'"Personne"."nir" AS "nir"',
				$this->_getFieldsIndicateurAge($search),
				$this->_getFieldsIndicateurAnciennete($search),
				$this->_getFieldsIndicateurNivetu($search),
				$this->_getFieldsIndicateurSexe($search)
			);
			$base = $this->_completeQuerySoumisDd( $base, $annee, true );
			$base['joins'][] = array (
				'table' => 'structuresreferentes',
				'alias' => 'Structurereferente',
				'type' => 'LEFT',
				'conditions' => '"Structurereferente"."id" = "Orientstruct"."structurereferente_id"'
			);

			return $base;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _generateResults( array $results, &$resultats, $configuration, $tableau = 'Tableau1' ) {
			foreach ($results as $key => $value) {
				$row = $value[0];

				// Tranche d'age
				$this->{'_getRowInformations'.$tableau}($row, $resultats, 'age', $row['age'], $configuration);

				// Sexe
				$this->{'_getRowInformations'.$tableau}($row, $resultats, 'sexe', $row['sexe'], $configuration);

				// Situation familiale
				if (in_array ($row['situationFamiliale'], $this->Foyer->sitfam_isole) && $row['enfant'] == '0') {
					$this->{'_getRowInformations'.$tableau}($row, $resultats, 'sitfam', 'Personne seule sans enfant', $configuration);
				}
				else if (in_array ($row['situationFamiliale'], $this->Foyer->sitfam_isole) && $row['enfant'] == '1') {
					$this->{'_getRowInformations'.$tableau}($row, $resultats, 'sitfam', 'Personne seule avec enfant(s)', $configuration);
				}
				else if (in_array ($row['situationFamiliale'], $this->Foyer->sitfam_en_couple) && $row['enfant'] == '0') {
					$this->{'_getRowInformations'.$tableau}($row, $resultats, 'sitfam', 'Personne en couple sans enfant', $configuration);
				}
				else if (in_array ($row['situationFamiliale'], $this->Foyer->sitfam_en_couple) && $row['enfant'] == '1') {
					$this->{'_getRowInformations'.$tableau}($row, $resultats, 'sitfam', 'Personne en couple avec enfant(s)', $configuration);
				}
				else {
					$this->{'_getRowInformations'.$tableau}($row, $resultats, 'sitfam', 'Situation familiale non connue', $configuration);
				}

				// Ancienneté
				$this->{'_getRowInformations'.$tableau}($row, $resultats, 'anciennete', $row['anciennete'], $configuration);

				// Niveau étude
				$this->{'_getRowInformations'.$tableau}($row, $resultats, 'nivetu', $row['nivetu'], $configuration);
			}
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableauCer( array $search , $annee) {
			$base = $this->_getBaseQueryIndicateursOrientations( $search );
			$base['fields'] = array (
				'DISTINCT ON ("Personne"."id") "Personne"."id" AS "idPersonne"',
				'"Typeorient"."id" AS "idTypeorient"',
				'"Orientstruct"."id" AS "idOrientstruct"',
				'"Foyer"."sitfam" AS "situationFamiliale"',
				'(CASE WHEN ( EXISTS ( SELECT enfants.id FROM personnes AS enfants INNER JOIN prestations ON ( enfants.id = prestations.personne_id AND prestations.natprest = \'RSA\' ) WHERE enfants.foyer_id= "Foyer"."id" AND prestations.rolepers = \'ENF\' ) ) THEN \'1\' ELSE \'0\' END ) AS "enfant"',
				'"Structurereferente"."id" AS "idStructurereferente"',
				$this->_getFieldsIndicateurAge($search),
				$this->_getFieldsIndicateurAnciennete($search),
				$this->_getFieldsIndicateurNivetu($search),
				$this->_getFieldsIndicateurSexe($search)
			);
			$base = $this->_completeQuerySoumisDd( $base, $annee, true );
			$base['joins'][] = array (
				'table' => 'structuresreferentes',
				'alias' => 'Structurereferente',
				'type' => 'LEFT',
				'conditions' => '"Structurereferente"."id" = "Orientstruct"."structurereferente_id"'
			);
/*
			$base['joins'][] = array (
				'table' => 'dossiers',
				'alias' => 'Dossier',
				'type' => 'LEFT',
				'conditions' => '"Structurereferente"."id" = "Orientstruct"."structurereferente_id"'
			);
*/
			return $base;
		}

		/**
		 * Retourne le query de base pour la partie "Questionnaire orientation",
		 * "1 - Orientation des personnes dans le champ des Droits et Devoirs au
		 * 31 décembre de l'année, au sens du type de parcours".
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getBaseQueryIndicateursOrientationsCer( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );

			$query = $this->_getBaseQueryCer( $search );
			$query = $this->_completeQueryDspDspRev( $query, $search );

			// On ne prend en compte que la dernière orientation en cours au 31/12 de cette année-là
			$sq = $this->_sqDerniereOrientation( $annee );
			$query['joins'][] = $Dossier->Foyer->Personne->join(
				'Orientstruct',
				array(
					'type' => 'LEFT OUTER',
					'conditions' => array(
						"Orientstruct.id IN ( {$sq} )"
					)
				)
			);
			$query['joins'][] = $Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) );

			return $query;
		}

		/**
		 * Retourne la requête de base utilisée dans les différents questionnaires.
		 *
		 * Les modèles utilisés sont: Dossier, Detaildroitrsa, Foyer, Situationdossierrsa,
		 * Adressefoyer, Personne, Adresse, Prestation, Calculdroitrsa.
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getBaseQueryCer( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );

			$conditions = array(
				array(
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.rgadr' => '01',
					)
				),
				'Prestation.rolepers' => array( 'DEM', 'CJT' ),
				'Dossier.dtdemrsa <=' => "{$annee}-12-31",
			);

			// Seulement les derniers dossiers des allocataires
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, array( 'Dossier' => array( 'dernier' => true ) ) );

			// Condition sur le service instructeur
			$conditions[] = $this->_getConditionsServiceInstructeur( $search );

			// Conditions sur l'adresse de l'allocataire
			$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

			$query = array(
				'joins' => array(
					$Dossier->join( 'Detaildroitrsa', array( 'type' => 'INNER' ) ),
					$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
				),
				'contain' => false,
				'conditions' => $conditions,
			);

			return $query;
		}

		/**
		 * Supprime les jointures dans une requête
		 *
		 * @param array $unsets
		 * @param array $query
		 * @return array
		 */
		protected function _unsetJoinsQuery ( $unsets , $query ) {

			foreach ($unsets as $unset) {
				foreach ($query['joins'] as $key => $value) {
					if ($value['table'] == $unset) {
						unset ($query['joins'][$key]);
						break;
					}
				}
			}

			return $query;
		}

		/**
		 * Supprime les champs dans une requête
		 *
		 * @param array $unsets
		 * @param array $query
		 * @return array
		 */
		protected function _unsetFieldsQuery ( $unsets , $query ) {

			foreach ($unsets as $unset) {
				foreach ($query['fields'] as $key => $value) {
					if ($value == $unset) {
						unset ($query['fields'][$key]);
						break;
					}
				}
			}

			return $query;
		}


		########################################################################################################################
		########################################################################################################################

		/**
		 * Retourne les résultats de la partie "Questionnaire orientation", "1 -
		 * Orientation des personnes dans le champ des Droits et Devoirs au 31
		 * décembre de l'année, au sens du type de parcours".
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableau1( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// Récupération des variables de configuration
			$configuration = $this->_getConfigStatistiqueDrees();

			// Query de base
			$base = $this->_getQueryTableau ($search, $annee);

			// Recherche
			$results = $Dossier->find( 'all', $base);

			// Initialisation tableau de résultats
			$resultats = array ();
			$this->_initialiseResults($resultats, 'Tableau1');

			// Génération du tableau de résultats
			$this->_generateResults($results, $resultats, $configuration, 'Tableau1');

			return $resultats;
		}

		/**
		 *
		 */
		private function _initialiseRowInformationsTableau1 (&$resultats, $categorie, $souscategorie) {
			if (!isset ($resultats[$categorie])) {
				$resultats[$categorie] = array ();
			}

			// Droits et devoirs
			$resultats[$categorie]['droits_et_devoirs'][$souscategorie] = 0;
			// Orientés
			$resultats[$categorie]['orientes'][$souscategorie] = 0;
			// Orientés vers Pôle Emploi
			$resultats[$categorie]['orientes_pole_emploi'][$souscategorie] = 0;
			// Orientés vers autre que Pôle Emploi
			$resultats[$categorie]['orientes_autre_que_pole_emploi'][$souscategorie] = 0;
			// Mission Locale
			$resultats[$categorie]['spe_mission_locale'][$souscategorie] = 0;
			// MDE / MDEF / PLIE / Cap Emploi
			$resultats[$categorie]['spe_mde_mdef_plie'][$souscategorie] = 0;
			// Création développement d'entreprise
			$resultats[$categorie]['spe_creation_entreprise'][$souscategorie] = 0;
			// IAE
			$resultats[$categorie]['spe_iae'][$souscategorie] = 0;
			// Autre organisme de placement / formation professionnelle
			$resultats[$categorie]['spe_autre_placement_pro'][$souscategorie] = 0;
			// SSD
			$resultats[$categorie]['hors_spe_ssd'][$souscategorie] = 0;
			// CAF
			$resultats[$categorie]['hors_spe_caf'][$souscategorie] = 0;
			// MSA
			$resultats[$categorie]['hors_spe_msa'][$souscategorie] = 0;
			// CCAS / CIAS
			$resultats[$categorie]['hors_spe_ccas_cias'][$souscategorie] = 0;
			// Autres organismes
			$resultats[$categorie]['hors_spe_autre_organisme'][$souscategorie] = 0;
			// Non orientés
			$resultats[$categorie]['non_orientes'][$souscategorie] = 0;
		}

		/**
		 *
		 */
		private function _getRowInformationsTableau1 ($row, &$resultats, $categorie, $souscategorie, $configuration) {
			// Droits et devoirs
			$resultats[$categorie]['droits_et_devoirs'][$souscategorie]++;

			// Orientés
			if (is_numeric ($row['idOrientstruct'])) {
				$resultats[$categorie]['orientes'][$souscategorie]++;

				// Orientés vers Pôle Emploi
				if (is_numeric ($row['idOrientstruct']) && in_array($row['idStructurereferente'], $configuration['organismes']['orientes_pole_emploi'])) {
					$resultats[$categorie]['orientes_pole_emploi'][$souscategorie]++;
				}
				// Orientés vers autre que Pôle Emploi
				else {
					$resultats[$categorie]['orientes_autre_que_pole_emploi'][$souscategorie]++;
				}

				// Mission Locale
				if (in_array($row['idStructurereferente'], $configuration['organismes']['spe_mission_locale'])) {
					$resultats[$categorie]['spe_mission_locale'][$souscategorie]++;
				}
				// MDE / MDEF / PLIE / Cap Emploi
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['spe_mde_mdef_plie'])) {
					$resultats[$categorie]['spe_mde_mdef_plie'][$souscategorie]++;
				}
				// Création développement d'entreprise
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['spe_creation_entreprise'])) {
					$resultats[$categorie]['spe_creation_entreprise'][$souscategorie]++;
				}
				// IAE
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['spe_iae'])) {
					$resultats[$categorie]['spe_iae'][$souscategorie]++;
				}
				// Autre organisme de placement / formation professionnelle
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['spe_autre_placement_pro'])) {
					$resultats[$categorie]['spe_autre_placement_pro'][$souscategorie]++;
				}
				// SSD
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['hors_spe_ssd'])) {
					$resultats[$categorie]['hors_spe_ssd'][$souscategorie]++;
				}
				// CAF
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['hors_spe_caf'])) {
					$resultats[$categorie]['hors_spe_caf'][$souscategorie]++;
				}
				// MSA
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['hors_spe_msa'])) {
					$resultats[$categorie]['hors_spe_msa'][$souscategorie]++;
				}
				// CCAS / CIAS
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['hors_spe_ccas_cias'])) {
					$resultats[$categorie]['hors_spe_ccas_cias'][$souscategorie]++;
				}
				// Autres organismes
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['hors_spe_autre_organisme'])) {
					$resultats[$categorie]['hors_spe_autre_organisme'][$souscategorie]++;
				}
			}
			// Non orientés
			else {
				$resultats[$categorie]['non_orientes'][$souscategorie]++;
			}
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableau2( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// Récupération des variables de configuration
			$configuration = $this->_getConfigStatistiqueDrees();

			// Query de base
			$base = $this->_getQueryTableau ($search, $annee);
			$base = $this->_completeQueryCer($base, $annee, $configuration);

			// Recherche
			$results = $Dossier->find( 'all', $base);

			// Initialisation tableau de résultats
			$resultats = array ();
			$this->_initialiseResults($resultats, 'Tableau2');

			// Génération du tableau de résultats
			$this->_generateResults($results, $resultats, $configuration, 'Tableau2PE');

			// Recalcule des données des CER
			$base = $this->_completeQueryRestrictionCer($base, $annee, $configuration);
			$results = $Dossier->find( 'all', $base);
			//$this->_initialiseResultsTableau2($resultats);

			$this->_generateResults($results, $resultats, $configuration, 'Tableau2CER');

			return $resultats;
		}

		/**
		 *
		 */
		private function _initialiseRowInformationsTableau2 (&$resultats, $categorie, $souscategorie) {
			if (!isset ($resultats[$categorie])) {
				$resultats[$categorie] = array ();
			}

			// Droits et devoirs
			//$resultats[$categorie]['droits_et_devoirs'][$souscategorie] = 0;
			// Orientés
			//$resultats[$categorie]['orientes'][$souscategorie] = 0;
			// Orientés vers Pôle Emploi
			$resultats[$categorie]['orientes_pole_emploi'][$souscategorie] = 0;
			// Orientés vers autre que Pôle Emploi
			$resultats[$categorie]['orientes_autre_que_pole_emploi'][$souscategorie] = 0;
			// Mission Locale
			$resultats[$categorie]['spe_mission_locale'][$souscategorie] = 0;
			// MDE / MDEF / PLIE / Cap Emploi
			$resultats[$categorie]['spe_mde_mdef_plie'][$souscategorie] = 0;
			// Création développement d'entreprise
			$resultats[$categorie]['spe_creation_entreprise'][$souscategorie] = 0;
			// IAE
			$resultats[$categorie]['spe_iae'][$souscategorie] = 0;
			// Autre organisme de placement / formation professionnelle
			$resultats[$categorie]['spe_autre_placement_pro'][$souscategorie] = 0;
			// SSD
			$resultats[$categorie]['hors_spe_ssd'][$souscategorie] = 0;
			// CAF
			$resultats[$categorie]['hors_spe_caf'][$souscategorie] = 0;
			// MSA
			$resultats[$categorie]['hors_spe_msa'][$souscategorie] = 0;
			// CCAS / CIAS
			$resultats[$categorie]['hors_spe_ccas_cias'][$souscategorie] = 0;
			// Autres organismes
			$resultats[$categorie]['hors_spe_autre_organisme'][$souscategorie] = 0;
			// Non orientés
			//$resultats[$categorie]['non_orientes'][$souscategorie] = 0;
		}

		/**
		 *
		 */
		private function _getRowInformationsTableau2PE ($row, &$resultats, $categorie, $souscategorie, $configuration) {
			// Inscrits ET orientés vers Pôle Emploi
			if (in_array($row['idStructurereferente'], $configuration['organismes']['orientes_pole_emploi']) && !empty($row['nir'])) {
				$resultats[$categorie]['orientes_pole_emploi'][$souscategorie]++;
			}
		}

		/**
		 *
		 */
		private function _getRowInformationsTableau2CER ($row, &$resultats, $categorie, $souscategorie, $configuration) {
			// Avec un CER ET orientés vers autre que Pôle Emploi
			if (!in_array($row['idStructurereferente'], $configuration['organismes']['orientes_pole_emploi']) && $row['contrat_cer'] == 'cer') {
				$resultats[$categorie]['orientes_autre_que_pole_emploi'][$souscategorie]++;

				// Mission Locale
				if (in_array($row['idStructurereferente'], $configuration['organismes']['spe_mission_locale'])) {
					$resultats[$categorie]['spe_mission_locale'][$souscategorie]++;
				}
				// MDE / MDEF / PLIE / Cap Emploi
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['spe_mde_mdef_plie'])) {
					$resultats[$categorie]['spe_mde_mdef_plie'][$souscategorie]++;
				}
				// Création développement d'entreprise
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['spe_creation_entreprise'])) {
					$resultats[$categorie]['spe_creation_entreprise'][$souscategorie]++;
				}
				// IAE
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['spe_iae'])) {
					$resultats[$categorie]['spe_iae'][$souscategorie]++;
				}
				// Autre organisme de placement / formation professionnelle
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['spe_autre_placement_pro'])) {
					$resultats[$categorie]['spe_autre_placement_pro'][$souscategorie]++;
				}
				// SSD
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['hors_spe_ssd'])) {
					$resultats[$categorie]['hors_spe_ssd'][$souscategorie]++;
				}
				// CAF
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['hors_spe_caf'])) {
					$resultats[$categorie]['hors_spe_caf'][$souscategorie]++;
				}
				// MSA
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['hors_spe_msa'])) {
					$resultats[$categorie]['hors_spe_msa'][$souscategorie]++;
				}
				// CCAS / CIAS
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['hors_spe_ccas_cias'])) {
					$resultats[$categorie]['hors_spe_ccas_cias'][$souscategorie]++;
				}
				// Autres organismes
				else if (in_array($row['idStructurereferente'], $configuration['organismes']['hors_spe_autre_organisme'])) {
					$resultats[$categorie]['hors_spe_autre_organisme'][$souscategorie]++;
				}
			}
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableau3( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// Récupération des variables de configuration
			$configuration = $this->_getConfigStatistiqueDrees();

			// Query de base
			$base = $this->_getQueryTableau ($search, $annee);
			$base = $this->_completeQueryCer ($base, $annee, $configuration);

			// Recherche
			$results = $Dossier->find( 'all', $base);

			// Initialisation tableau de résultats
			$resultats = array ();
			$this->_initialiseResults($resultats, 'Tableau3');

			// Génération du tableau de résultats
			$this->_generateResults($results, $resultats, $configuration, 'Tableau3');

			return $resultats;
		}

		/**
		 *
		 */
		private function _initialiseRowInformationsTableau3 (&$resultats, $categorie, $souscategorie) {
			if (!isset ($resultats[$categorie])) {
				$resultats[$categorie] = array ();
			}

			// CER de moins de 6 mois
			$resultats[$categorie]['cer_moins_6_mois'][$souscategorie] = 0;
			// CER entre 6 mois et 1 an
			$resultats[$categorie]['cer_6_mois_un_an'][$souscategorie] = 0;
			// CER de 1 an et plus
			$resultats[$categorie]['cer_1_an_et_plus'][$souscategorie] = 0;
		}

		/**
		 *
		 */
		private function _getRowInformationsTableau3 ($row, &$resultats, $categorie, $souscategorie, $configuration) {
			// Avec un CER ET orientés vers autre que Pôle Emploi
			if (!in_array($row['idStructurereferente'], $configuration['organismes']['orientes_pole_emploi']) && $row['contrat_cer'] == 'cer') {
				// CER de moins de 6 mois
				if ($row['duree_cer'] < 6) {
					$resultats[$categorie]['cer_moins_6_mois'][$souscategorie]++;
				}
				// CER entre 6 mois et 1 an
				else if ($row['duree_cer'] >= 6 && $row['duree_cer'] < 12) {
					$resultats[$categorie]['cer_6_mois_un_an'][$souscategorie]++;
				}
				// CER de 1 an et plus
				else if ($row['duree_cer'] >= 12) {
					$resultats[$categorie]['cer_1_an_et_plus'][$souscategorie]++;
				}
			}
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableau4( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// Récupération des variables de configuration
			$configuration = $this->_getConfigStatistiqueDrees();

			// Query de base
			$base = $this->_getQueryTableau ($search, $annee);
			$base = $this->_completeQueryCer ($base, $annee, $configuration);
			$base = $this->_completeQueryRestrictionCer($base, $annee, $configuration);

			// Initialisation tableau de résultats
			$resultats = array ();

			if ($configuration['actionscandidats']) {
				// Recherche des personnes
				$results = $Dossier->find( 'all', $base);

				// Extraction des id personnes
				$idPersonnes = '';
				$separateur = '';
				$personnes = array ();
				foreach ($results as $result) {
					$idPersonnes .= $separateur.$result[0]['idPersonne'];
					$separateur = ', ';
					$personnes[$result[0]['idPersonne']] = $result[0];
				}

				// Recherche des actions des fiches de candidature
				$query = array (
					'fields' => array (
						'DISTINCT ON ("ActioncandidatPersonne"."id") "ActioncandidatPersonne"."id" AS "idActioncandidatpersonne"',
						'"Actioncandidat"."dreesactionscer_id" AS "idDreesactioncer"',
						'"Actioncandidat"."id" AS "idActioncandidat"',
						'"ActioncandidatPersonne"."personne_id" AS "idPersonne"',
					),
					'joins' => array (
						array (
							'table' => 'actionscandidats',
							'alias' => 'Actioncandidat',
							'type' => 'INNER',
							'conditions' => '"ActioncandidatPersonne"."actioncandidat_id" = "Actioncandidat"."id"'
						),
					),
					'conditions' => array (
						'"ActioncandidatPersonne"."datesignature" >= \''.$annee.'-01-01\'',
						'"ActioncandidatPersonne"."datesignature" <= \''.$annee.'-12-31\'',
						'"Actioncandidat"."dreesactionscer_id" IS NOT NULL',
						'"ActioncandidatPersonne"."personne_id" IN ('.$idPersonnes.')',
					),
					'contain' => false,
				);

				$Actioncandidatpersonne = ClassRegistry::init( 'ActioncandidatPersonne' );
				$results = $Actioncandidatpersonne->find( 'all', $query);

				// Merge des résultats
				foreach ($results as $key => $result) {
					if (isset ($personnes[$result[0]['idPersonne']])) {
						$results[$key][0] = array_merge ($result[0], $personnes[$result[0]['idPersonne']]);
					} else {
						unset ($results[$key]);
					}
				}
			}
			else {
				$Dossier = ClassRegistry::init( 'Cer93Sujetcer93' );

				// Query
				$base = $this->_adaptQueryTableau4 ($base, $search, $annee, $configuration);

				// Recherche
				$results = $Dossier->find( 'all', $base);
			}

			// Remplissage tableau de résultats
			$this->_initialiseResults($resultats, 'Tableau4');

			// Génération du tableau de résultats
			$this->_generateResults($results, $resultats, $configuration, 'Tableau4');

			return $resultats;
		}

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		private function _adaptQueryTableau4Actioncandidat( $base, array $search , $annee, $configuration ) {
			// Suppression des jointures
			$unsets = array ('"cers93"', '"contratsinsertion"', '"personnes"', '"foyers"');
			$base = $this->_unsetJoinsQuery ($unsets, $base);

			// Suppression des champs
			$unsets = array ('DISTINCT ON ("Personne"."id") "Personne"."id" AS "idPersonne"');
			$base = $this->_unsetFieldsQuery ($unsets, $base);

			// Ajout des champs en remplacement.
			$base['fields'] = array_merge(
				array (
					'DISTINCT ON ("Actioncandidat"."id") "Actioncandidat"."id" AS "idActioncandidat"',
					'"Contratinsertion"."id" AS "idContratinsertion"',
				),
				$base['fields']
			);
			$base['joins'] = array_merge(
				array (
					array (
						'table' => 'contratsinsertion',
						'alias' => 'Contratinsertion',
						'type' => 'INNER',
						'conditions' => '"Contratinsertion"."actioncandidat_id" = "Actioncandidat"."id"'
					),
					array (
						'table' => 'personnes',
						'alias' => 'Personne',
						'type' => 'INNER',
						'conditions' => '"Personne"."id" = "Contratinsertion"."personne_id"'
					),
					array (
						'table' => 'foyers',
						'alias' => 'Foyer',
						'type' => 'INNER',
						'conditions' => '"Foyer"."id" = "Personne"."foyer_id"'
					),
					array (
						'table' => 'dossiers',
						'alias' => 'Dossier',
						'type' => 'INNER',
						'conditions' => '"Dossier"."id" = "Foyer"."dossier_id"'
					)
				),
				$base['joins']
			);

			return $base;
		}

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		private function _adaptQueryTableau4( $base, array $search , $annee, $configuration ) {
			// Suppression des jointures
			$unsets = array ('"cers93"', '"contratsinsertion"', '"personnes"', '"foyers"');
			$base = $this->_unsetJoinsQuery ($unsets, $base);

			// Suppression des champs
			$unsets = array ('DISTINCT ON ("Personne"."id") "Personne"."id" AS "idPersonne"');
			$base = $this->_unsetFieldsQuery ($unsets, $base);

			// Ajout des champs en remplacement.
			$base['fields'] = array_merge(
				array (
					'DISTINCT ON ("Cer93Sujetcer93"."id") "Cer93Sujetcer93"."id" AS "idCer93Sujetcer93"',
					'"Contratinsertion"."id" AS "idContratinsertion"',
					'"Cer93Sujetcer93"."sujetcer93_id" AS "idSujetcer93"',
					'"Cer93Sujetcer93"."soussujetcer93_id" AS "idSoussujetcer93"',
					'"Cer93Sujetcer93"."valeurparsoussujetcer93_id" AS "idValeurparsoussujetcer93"',
				),
				$base['fields']
			);
			$base['joins'] = array_merge(
				array (
					array (
						'table' => 'cers93',
						'alias' => 'Cer93',
						'type' => 'INNER',
						'conditions' => '"Cer93Sujetcer93"."cer93_id" = "Cer93"."id"'
					),
					array (
						'table' => 'contratsinsertion',
						'alias' => 'Contratinsertion',
						'type' => 'INNER',
						'conditions' => '"Cer93"."contratinsertion_id" = "Contratinsertion"."id"'
					),
					array (
						'table' => 'personnes',
						'alias' => 'Personne',
						'type' => 'INNER',
						'conditions' => '"Personne"."id" = "Contratinsertion"."personne_id"'
					),
					array (
						'table' => 'foyers',
						'alias' => 'Foyer',
						'type' => 'INNER',
						'conditions' => '"Foyer"."id" = "Personne"."foyer_id"'
					),
					array (
						'table' => 'dossiers',
						'alias' => 'Dossier',
						'type' => 'INNER',
						'conditions' => '"Dossier"."id" = "Foyer"."dossier_id"'
					)
				),
				$base['joins']
			);

			return $base;
		}


		/**
		 *
		 */
		private function _initialiseRowInformationsTableau4 (&$resultats, $categorie, $souscategorie) {
			if (!isset ($resultats[$categorie])) {
				$resultats[$categorie] = array ();
			}
			$actionsCers = $this->_getConfigActionsCer();

			foreach($actionsCers as $libActionCer => $actionCer) {
				$resultats[$categorie][$libActionCer][$souscategorie] = 0;
			}
		}

		/**
		 *
		 */
		private function _getRowInformationsTableau4 ($row, &$resultats, $categorie, $souscategorie, $configuration) {
			//… au moins une action visant à trouver des activités, stages ou formations destinés à acquérir des compétences professionnelles
			if ($this->_validRowInformationsTableau4($row, $configuration, 'acquerir_competences_pro')) {
				$resultats[$categorie]['acquerir_competences_pro'][$souscategorie]++;
			}
			//… au moins une action visant à s'inscrire dans un parcours de recherche d'emploi
			if ($this->_validRowInformationsTableau4($row, $configuration, 'parcours_recherche_emploi')) {
				$resultats[$categorie]['parcours_recherche_emploi'][$souscategorie]++;
			}
			//… au moins une action visant à s'inscrire dans une mesure d'insertion par l'activité économique (IAE)
			if ($this->_validRowInformationsTableau4($row, $configuration, 'iae')) {
				$resultats[$categorie]['iae'][$souscategorie]++;
			}
			//… au moins une action aidant à la réalisation d’un projet de création, de reprise ou de poursuite d’une activité non salariée 
			if ($this->_validRowInformationsTableau4($row, $configuration, 'activite_non_salariale')) {
				$resultats[$categorie]['activite_non_salariale'][$souscategorie]++;
			}
			//… au moins une action visant à trouver un emploi aidé
			if ($this->_validRowInformationsTableau4($row, $configuration, 'emploi_aide')) {
				$resultats[$categorie]['emploi_aide'][$souscategorie]++;
			}
			//… au moins une action visant à trouver un emploi non aidé
			if ($this->_validRowInformationsTableau4($row, $configuration, 'emploi_non_aide')) {
				$resultats[$categorie]['emploi_non_aide'][$souscategorie]++;
			}
			//… au moins une action visant à faciliter le lien social (développement de l'autonomie sociale, activités collectives,…)
			if ($this->_validRowInformationsTableau4($row, $configuration, 'lien_social')) {
				$resultats[$categorie]['lien_social'][$souscategorie]++;
			}
			//… au moins une action visant la mobilité (permis de conduire, acquisition / location de véhicule, frais de transport…)
			if ($this->_validRowInformationsTableau4($row, $configuration, 'mobilite')) {
				$resultats[$categorie]['mobilite'][$souscategorie]++;
			}
			// … au moins une action visant l'accès à un logement, au relogement ou à l'amélioration de l'habitat
			if ($this->_validRowInformationsTableau4($row, $configuration, 'acces_logement')) {
				$resultats[$categorie]['acces_logement'][$souscategorie]++;
			}
			//… au moins une action visant l'accès aux soins
			if ($this->_validRowInformationsTableau4($row, $configuration, 'acces_soins')) {
				$resultats[$categorie]['acces_soins'][$souscategorie]++;
			}
			//… au moins une action visant l'autonomie financière (constitution d'un dossier de surendettement,...)
			if ($this->_validRowInformationsTableau4($row, $configuration, 'autonomie_financiere')) {
				$resultats[$categorie]['autonomie_financiere'][$souscategorie]++;
			}
			//… au moins une action visant la famille et la parentalité (soutien familial, garde d'enfant, …)
			if ($this->_validRowInformationsTableau4($row, $configuration, 'famille_parentalite')) {
				$resultats[$categorie]['famille_parentalite'][$souscategorie]++;
			}
			// … au moins une action visant la lutte contre l'illettrisme ou l'acquisition des savoirs de base
			if ($this->_validRowInformationsTableau4($row, $configuration, 'illettrisme')) {
				$resultats[$categorie]['illettrisme'][$souscategorie]++;
			}
			//… au moins une action visant l'accès aux droits ou l'aide dans les démarches administratives
			if ($this->_validRowInformationsTableau4($row, $configuration, 'demarches_administratives')) {
				$resultats[$categorie]['demarches_administratives'][$souscategorie]++;
			}
			//… au moins une action non classée dans les items précédents
			if ($this->_validRowInformationsTableau4($row, $configuration, 'autres')) {
				$resultats[$categorie]['autres'][$souscategorie]++;
			}
		}

		/**
		 *
		 */
		private function _validRowInformationsTableau4 ($row, $configuration, $sujet) {
			if ($configuration['actionscandidats']) {
				if (!empty ($configuration['actions_cer'][$sujet]['actioncandidat_id']) && in_array ($row['idActioncandidat'], $configuration['actions_cer'][$sujet]['actioncandidat_id'])) {
					return true;
				}
			}
			else {
				if (!empty ($configuration['actions_cer'][$sujet]['valeurparsoussujetcer93_id']) && in_array ($row['idValeurparsoussujetcer93'], $configuration['actions_cer'][$sujet]['valeurparsoussujetcer93_id'])) {
					return true;
				}
				else if (!empty ($configuration['actions_cer'][$sujet]['soussujetcer93_id']) && in_array ($row['idSoussujetcer93'], $configuration['actions_cer'][$sujet]['soussujetcer93_id'])) {
					return true;
				}
				else if (!empty ($configuration['actions_cer'][$sujet]['sujetcer93_id']) && in_array ($row['idSujetcer93'], $configuration['actions_cer'][$sujet]['sujetcer93_id'])) {
					return true;
				}
			}

			return false;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableau5( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// Récupération des variables de configuration
			$configuration = $this->_getConfigStatistiqueDrees();

			// Query de base
			$base = $this->_getQueryTableau ($search, $annee);
			$base['fields'][] = '"Orientstruct"."rgorient" AS "rgorient"';
			$base['fields'][] = '"Contratinsertion"."rg_ci" AS "rg_ci"';

			$base['fields'][] = '"Dossier"."dtdemrsa" AS "dtdemrsa"';
			$base['fields'][] = '"Orientstruct"."date_propo" AS "debutOrientation"';
			$base['fields'][] = '"Contratinsertion"."dd_ci" AS "debutCer"';
			$base['fields'][] = '( "Orientstruct"."date_valid" - "Dossier"."dtdemrsa" ) AS "delaiOrient"';
			$base['fields'][] = '( "Contratinsertion"."dd_ci" - "Orientstruct"."date_propo" ) AS "delaiCer"';

			// Rentrés au RSA en cours d'année.
			$base['conditions'][] = 'Dossier.dtdemrsa >= \''.$annee.'-01-01\'';

			// Primo-CER
			$base['joins'][] = array (
				'table' => 'contratsinsertion',
				'alias' => 'Contratinsertion',
				'type' => 'LEFT',
				'conditions' => array (
					'"Contratinsertion"."personne_id" = "Personne"."id"',
					'"Contratinsertion"."dd_ci" <= \''.$annee.'-12-31\'',
					'"Contratinsertion"."df_ci" >= \''.$annee.'-12-31\'',
					'"Contratinsertion"."decision_ci" = \'V\'',
					'"Contratinsertion"."rg_ci" = 1'
				)
			);

			// Recherche
			$results = $Dossier->find( 'all', $base);

			// Initialisation tableau de résultats
			$resultats = array ();
			$this->_initialiseResults($resultats, 'Tableau5');

			// Génération du tableau de résultats
			$this->_generateResults($results, $resultats, $configuration, 'Tableau5');

			return $resultats;
		}

		/**
		 *
		 */
		private function _initialiseRowInformationsTableau5 (&$resultats, $categorie, $souscategorie) {
			if (!isset ($resultats[$categorie])) {
				$resultats[$categorie] = array ();
			}

			// Droits et devoirs
			$resultats[$categorie]['droits_et_devoirs'][$souscategorie] = 0;
			// Primo-orientés
			$resultats[$categorie]['primo_orientes'][$souscategorie] = 0;
			// Primo-orientés vers autre que Pôle Emploi
			$resultats[$categorie]['primo_orientes_hors_pe'][$souscategorie] = 0;
			// Primo-orientés vers autre que Pôle Emploi ET primo-CER
			$resultats[$categorie]['primo_orientes_hors_pe_primo_cer'][$souscategorie] = 0;
			// Délai moyen date RSA et date primo-orientation
			$resultats[$categorie]['delai_moyen_primo_orientes'][$souscategorie] = 0;
			// Délai moyen date primo-orientation vers autre que Pôle Emploi et date primo-CER
			$resultats[$categorie]['delai_moyen_hors_pe_primo_orientes_primo_cer'][$souscategorie] = 0;
		}

		/**
		 *
		 */
		private function _getRowInformationsTableau5 ($row, &$resultats, $categorie, $souscategorie, $configuration) {
			// Droits et devoirs
			$resultats[$categorie]['droits_et_devoirs'][$souscategorie]++;

			// Primo-orientés
			if ($row['rgorient'] == 1 && is_numeric ($row['delaiOrient'])) {
				$resultats[$categorie]['primo_orientes'][$souscategorie]++;

				// Délai moyen date RSA et date primo-orientation
				$resultats[$categorie]['delai_moyen_primo_orientes'][$souscategorie] += $row['delaiOrient'];
			}

			// Primo-orientés vers autre que Pôle Emploi
			if ($row['rgorient'] == 1 && !in_array($row['idStructurereferente'], $configuration['organismes']['orientes_pole_emploi'])) {
				$resultats[$categorie]['primo_orientes_hors_pe'][$souscategorie]++;

				// Primo-orientés vers autre que Pôle Emploi ET primo-CER
				if ($row['rg_ci'] == 1) {
					$resultats[$categorie]['primo_orientes_hors_pe_primo_cer'][$souscategorie]++;

					// Délai moyen date primo-orientation vers autre que Pôle Emploi et date primo-CER
					$resultats[$categorie]['delai_moyen_hors_pe_primo_orientes_primo_cer'][$souscategorie] += $row['delaiCer'];
				}
			}
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableau6( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// Récupération des variables de configuration
			$configuration = $this->_getConfigStatistiqueDrees();

			$configuration['spe'] = array_merge (
				$configuration['organismes']['orientes_pole_emploi'],
				$configuration['organismes']['spe_mission_locale'],
				$configuration['organismes']['spe_mde_mdef_plie'],
				$configuration['organismes']['spe_creation_entreprise'],
				$configuration['organismes']['spe_iae'],
				$configuration['organismes']['spe_autre_placement_pro']
			);
			$configuration['hors_spe'] = array_merge (
				$configuration['organismes']['hors_spe_ssd'],
				$configuration['organismes']['hors_spe_caf'],
				$configuration['organismes']['hors_spe_msa'],
				$configuration['organismes']['hors_spe_ccas_cias'],
				$configuration['organismes']['hors_spe_autre_organisme']
			);

			// Type d'orientation emploi
			$TypeOrient = ClassRegistry::init( 'Typeorient' );
			$typesorients = $TypeOrient->find( 'list', array ('conditions' => array ('Parent.code_type_orient' => 'EMPLOI'), 'recursive' => 0));
			if (empty ($typesorients)) {
				$typesorients = $TypeOrient->find( 'list', array ('conditions' => array ('Typeorient.code_type_orient' => 'EMPLOI'), 'recursive' => 0));
			}
			$idTypeOrientEmploi = implode (',', array_keys ($typesorients));

			$results = array ();
			if (!empty ($idTypeOrientEmploi)) {
				// Query de base
				$base = $this->_getQueryTableau ($search, $annee);
				$base = $this->_adaptQueryTableau6($base, $search, $annee, $configuration, $idTypeOrientEmploi);
				$base['fields'][] = '"Orientstruct"."rgorient" AS "rgorient"';

				// Réorientation.
				$base['conditions'][] = '"Orientstruct"."date_valid" >= \''.$annee.'-01-01\'';
				$base['conditions'][] = '"Orientstruct"."date_valid" <= \''.$annee.'-12-31\'';
				$base['conditions'][] = '"Orientstruct"."rgorient" > 1';

				// Recherche
				$results = $Dossier->find( 'all', $base);
			}

			// Initialisation tableau de résultats
			$resultats = array ();
			$this->_initialiseResults($resultats, 'Tableau6');

			// Génération du tableau de résultats
			$this->_generateResults($results, $resultats, $configuration, 'Tableau6');

			return $resultats;
		}

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		private function _adaptQueryTableau6( $base, array $search , $annee, $configuration, $idTypeOrientEmploi ) {
			// Ajout des champs en remplacement.
			$base['joins'][] = array (
				'table' => 'orientsstructs',
				'alias' => 'Orientstructpcd',
				'type' => 'INNER',
				'conditions' => '"Orientstructpcd"."personne_id" = "Personne"."id"'
			);
			$base['joins'][] = array (
				'table' => 'typesorients',
				'alias' => 'Typeorientpcd',
				'type' => 'INNER',
				'conditions' => '"Orientstructpcd"."typeorient_id" = "Typeorientpcd"."id"'
			);

			$base['conditions'][] = '
				"Orientstructpcd"."id" IN (
					SELECT "orientsstructspcds"."id" AS "orientsstructspcds__id"
					FROM orientsstructs AS orientsstructspcds
					WHERE
						"orientsstructspcds"."personne_id" = "Personne"."id"
						AND "orientsstructspcds"."statut_orient" = \'Orienté\'
						AND "orientsstructspcds"."date_valid" IS NOT NULL
						AND "orientsstructspcds"."date_valid" < "Orientstruct"."date_valid"
					ORDER BY "orientsstructspcds"."date_valid" DESC
					LIMIT 1 )';

			$base['conditions'][] = '
				((((NOT ("Typeorient"."id" IN ('.$idTypeOrientEmploi.'))) AND ("Typeorientpcd"."id" IN ('.$idTypeOrientEmploi.')))) OR ((("Typeorient"."id" IN ('.$idTypeOrientEmploi.')) AND (NOT ("Typeorientpcd"."id" IN ('.$idTypeOrientEmploi.'))))))';

			$base['conditions'][] = '
				NOT EXISTS(
					SELECT "changementsorientations"."id" AS "changementsorientations__id"
					FROM orientsstructs AS changementsorientations
						INNER JOIN "public"."typesorients" AS "changementstypesorients" ON ("changementsorientations"."typeorient_id" = "changementstypesorients"."id")
						INNER JOIN "public"."structuresreferentes" AS "changementsstructuresreferentes" ON ("changementsorientations"."structurereferente_id" = "changementsstructuresreferentes"."id")
					WHERE
						"changementsorientations"."personne_id" = "Personne"."id"
						AND "changementsorientations"."statut_orient" = \'Orienté\'
						AND "changementsorientations"."date_valid" IS NOT NULL
						AND "changementsorientations"."date_valid" > "Orientstruct"."date_valid"
						AND "changementsorientations"."date_valid" BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
						AND ((((NOT ("Typeorient"."id" IN ('.$idTypeOrientEmploi.'))) AND ("changementstypesorients"."id" IN ('.$idTypeOrientEmploi.')))) OR ((("Typeorient"."id" IN ('.$idTypeOrientEmploi.')) AND (NOT ("changementstypesorients"."id" IN ('.$idTypeOrientEmploi.')))))) )';

			return $base;
		}

		/**
		 *
		 */
		private function _initialiseRowInformationsTableau6 (&$resultats, $categorie, $souscategorie) {
			if (!isset ($resultats[$categorie])) {
				$resultats[$categorie] = array ();
			}

			// Réorientation
			$resultats[$categorie]['reorientation'][$souscategorie] = 0;
			// Réorientation SPE vers hors SPE
			$resultats[$categorie]['spe_vers_hors_spe'][$souscategorie] = 0;
			// Réorientation hors SPE vers SPE
			$resultats[$categorie]['hors_spe_vers_spe'][$souscategorie] = 0;
		}

		/**
		 *
		 */
		private function _getRowInformationsTableau6 ($row, &$resultats, $categorie, $souscategorie, $configuration) {
			// Réorientation
			$resultats[$categorie]['reorientation'][$souscategorie]++;

			// Réorientation SPE vers hors SPE : la structure référente est la structure cible donc dans ce cas sera hors spe
			if (in_array ($row['idStructurereferente'], $configuration['hors_spe'])) {
				$resultats[$categorie]['spe_vers_hors_spe'][$souscategorie]++;
			}

			// Réorientation hors SPE vers SPE : la structure référente est la structure cible donc dans ce cas sera spe
			if (in_array ($row['idStructurereferente'], $configuration['spe'])) {
				$resultats[$categorie]['hors_spe_vers_spe'][$souscategorie]++;
			}
		}

		/**
		 *
		 */
		private function _getConfigStatistiqueDrees () {
			$conf = array();

			$conf['organismes'] = $this->_getConfigOrganismesDrees();
			$conf['actions_cer'] = $this->_getConfigActionsCer();
			$conf['actionscandidats'] = (boolean)Configure::read( 'Statistiquedrees.actionscandidats' );

			return $conf;
		}

 		/**
		*
		*/
		private function _getConfigOrganismesDrees () {
			$organismes = ClassRegistry::init( 'Dreesorganisme' )->find('all');
			$conf = array();

			foreach ($organismes as $organisme) {
				if(!empty($organisme['Dreesorganisme']['lib_dreesorganisme_code'])) {
					$libOrganisme = $organisme['Dreesorganisme']['lib_dreesorganisme_code'];
					$conf[$libOrganisme] = array();
					foreach ($organisme['Structurereferente'] as $structureRef) {
						$conf[$libOrganisme][]  = $structureRef['id'];
					}
				}
			}

			return $conf;
		}

		/**
		 *
		 */
		private function _getConfigActionsCer () {
			$actions = ClassRegistry::init( 'Dreesactionscer' )->find('all');
			$conf = array();

			foreach ($actions as $action) {
				$libActionCer = $action['Dreesactionscer']['lib_dreesactioncer_code'];
				$conf[$libActionCer] = array();

				$conf[$libActionCer]['sujetcer93_id'] = $this->_getConfigActionsCerParSujet($action['Sujetcer93']);
				$conf[$libActionCer]['soussujetcer93_id'] = $this->_getConfigActionsCerParSujet($action['Soussujetcer93']);
				$conf[$libActionCer]['valeurparsoussujetcer93_id'] = $this->_getConfigActionsCerParSujet($action['Valeurparsoussujetcer93']);

				$conf[$libActionCer]['actioncandidat_id'] = $this->_getConfigActionsCerParSujet($action['Actioncandidat']);
			}

			return $conf;
		}

		/*
		 *
		 * @param array $tab
		 * @return array
		 */
		private function _getConfigActionsCerParSujet ($tab) {
			$result = array();
			foreach ($tab as $sujet) {
				$result[]  = $sujet['id'];
			}
			return $result;
		}
	}
?>