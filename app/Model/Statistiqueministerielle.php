<?php
	/**
	 * Code source de la classe Statistiqueministerielle.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Statistiqueministerielle ...
	 *
	 * @package app.Model
	 */
	class Statistiqueministerielle extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Statistiqueministerielle';

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
				'NC'
			),
			'sitfam' => array(
				'01 - Homme seul sans enfant',
				'02 - Femme seule sans enfant',
				'03 - Homme seul avec enfant, RSA majoré',
				'04 - Homme seul avec enfant, RSA non majoré',
				'05 - Femme seule avec enfant, RSA majoré',
				'06 - Femme seule avec enfant, RSA non majoré',
				'07 - Homme en couple sans enfant',
				'08 - Femme en couple sans enfant',
				'09 - Homme en couple avec enfant',
				'10 - Femme en couple avec enfant',
				'11 - Non connue'
			),
			'nivetu' => array(
				'Vbis et VI',
				'V',
				'IV',
				'III, II, I',
				'NC'
			),
			'anciennete' => array(
				'moins de 6 mois',
				'6 mois et moins 1 an',
				'1 an et moins de 2 ans',
				'2 ans et moins de 5 ans',
				'5 ans et plus',
				'NC',
			)
		);

		/**
		 * Types de CER pour l'écran "Indicateurs de délais"
		 *
		 * @var array
		 */
		public $types_cers = array(
			'ppae' => array(
				'nbMoisTranche1' => 1,
				'nbMoisTranche2' => 3,
			),
			'cer_pro' => array(
				'nbMoisTranche1' => 1,
				'nbMoisTranche2' => 3,
			),
			'cer_pro_social' => array(
				'nbMoisTranche1' => 2,
				'nbMoisTranche2' => 4,
			),
		);

		/**
		 * Conditions concernant les durées du CER, par catégorie.
		 *
		 * @var array
		 */
		public $durees_cers = array(
			'duree_moins_6_mois' => array(
				"( Contratinsertion.df_ci - Contratinsertion.dd_ci || ' days' )::interval < '6 month'::interval"
			),
			'duree_6_mois_moins_1_an' => array(
				"( Contratinsertion.df_ci - Contratinsertion.dd_ci || ' days' )::interval >= '6 month'::interval",
				"( Contratinsertion.df_ci - Contratinsertion.dd_ci || ' days' )::interval < '1 year'::interval"
			),
			'duree_plus_1_an' => array(
				"( Contratinsertion.df_ci - Contratinsertion.dd_ci || ' days' )::interval > '1 year'::interval"
			),
		);

		/**
		 * Catégories du tableau "Indicateurs de natures des actions des contrats".
		 *
		 * @var array
		 */
		public $natures_cers = array(
			'01' => 'activités, stages ou formations destinés à acquérir des compétences professionnelles',
			'02' => 'orientation vers le service public de l\'emploi, parcours de recherche d\'emploi',
			'03' => 'mesures d\'insertion par l\'activité économique (IAE)',
			'04' => 'aide à la réalisation d’un projet de création, de reprise ou de poursuite d’une activité non salariée ',
			'05' => 'emploi aidé (hors CIA)',
			'06' => 'contrat d\'insertion par l\'activité (CIA) (3)',
			'07' => 'emploi non aidé',
			'08' => 'actions facilitant le lien social (développement de l\'autonomie sociale, activités collectives, ...)',
			'09' => 'actions facilitant la mobilité (permis de conduire, acquisition / location de véhicule, frais de transport...)',
			'10' => 'actions visant l\'accès à un logement, relogement ou à l\'amélioration de l\'habitat',
			'11' => 'actions facilitant l\'accès aux soins',
			'12' => 'actions visant l\'autonomie financière (constitution d\'un dossier de surendettement,...)',
			'13' => 'actions visant la famille et la parentalité (soutien familial, garde d\'enfant, ...)',
			'14' => 'lutte contre l\'illettrisme ; acquisition des savoirs de base',
			'15' => 'autres actions'
		);

		/**
		 * Constante définissant un parcours professionnel.
		 *
		 * @see Statistiqueministerielle::getConditionsTypeParcours()
		 */
		const PARCOURS_PROFESSIONNEL = 'PARCOURS_PROFESSIONNEL';

		/**
		 * Constante définissant un parcours socioprofessionnel.
		 *
		 * @see Statistiqueministerielle::getConditionsTypeParcours()
		 */
		const PARCOURS_SOCIOPROFESSIONNEL = 'PARCOURS_SOCIOPROFESSIONNEL';

		/**
		 * Constante définissant un parcours social.
		 *
		 * @see Statistiqueministerielle::getConditionsTypeParcours()
		 */
		const PARCOURS_SOCIAL = 'PARCOURS_SOCIAL';

		/**
		 * Constante définissant un organisme SPE.
		 *
		 * @see Statistiqueministerielle::getConditionsTypeOrganisme()
		 */
		const ORGANISME_SPE = 'ORGANISME_SPE';

		/**
		 * Constante définissant un organisme Hors SPE.
		 *
		 * @see Statistiqueministerielle::getConditionsTypeOrganisme()
		 */
		const ORGANISME_HORS_SPE = 'ORGANISME_HORS_SPE';

		/**
		 * Constante définissant l'organisme Pôle Emploi (faisant partie du SPE)
		 *
		 * @see Statistiqueministerielle::getConditionsTypeOrganisme()
		 */
		const ORGANISME_SPE_POLE_EMPLOI = 'ORGANISME_SPE_POLE_EMPLOI';

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
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getFieldsTrancheAge( array $search ) {
			$annee = Hash::get( $search, 'Search.annee' );

			return array(
				'(
					CASE
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP\''.$annee.'-12-31\', "Personne"."dtnai" ) ) BETWEEN 0 AND 24 THEN \'0 - 24\'
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP \''.$annee.'-12-31\', "Personne"."dtnai" ) ) BETWEEN 25 AND 29 THEN \'25 - 29\'
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP \''.$annee.'-12-31\', "Personne"."dtnai" ) ) BETWEEN 30 AND 39 THEN \'30 - 39\'
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP \''.$annee.'-12-31\', "Personne"."dtnai" ) ) BETWEEN 40 AND 49 THEN \'40 - 49\'
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP \''.$annee.'-12-31\', "Personne"."dtnai" ) ) BETWEEN 50 AND 59 THEN \'50 - 59\'
						WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP \''.$annee.'-12-31\', "Personne"."dtnai" ) ) >= 60 THEN \'>= 60\'
						ELSE \'NC\'
					END
				) AS "age_range"',
				'COUNT(DISTINCT(Personne.id)) AS "count"'
			);
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getFieldsSitfam( array $search ) {
			$parts = array(
				'femme' => '"Personne"."sexe" = \'2\'',
				'homme' => '"Personne"."sexe" = \'1\'',
				'en_couple' => '"Foyer"."sitfam" IN ('.'\''.implode( '\', \'', $this->Foyer->sitfam_en_couple ).'\''.')',
				'seul' => '"Foyer"."sitfam" IN ('.'\''.implode( '\', \'', $this->Foyer->sitfam_isole ).'\''.')',
				'avec_enfant' => 'EXISTS ( SELECT enfants.id FROM personnes AS enfants INNER JOIN prestations ON ( enfants.id = prestations.personne_id AND prestations.natprest = \'RSA\' ) WHERE enfants.foyer_id= "Foyer"."id" AND prestations.rolepers = \'ENF\' )',
				'rsa_majore' => 'EXISTS(
					SELECT * FROM detailsdroitsrsa
						INNER JOIN detailscalculsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id )
						WHERE
							detailsdroitsrsa.dossier_id = "Foyer"."dossier_id"
							AND detailscalculsdroitsrsa.natpf IN ( \'RCI\', \'RSI\' )
				)',
			);
			$parts['sans_enfant'] = "NOT {$parts['avec_enfant']}";
			$parts['rsa_non_majore'] = "NOT {$parts['rsa_majore']}";

			return array(
				'(
					CASE
						WHEN (
							'.$parts['homme'].'
							AND '.$parts['seul'].'
							AND '.$parts['sans_enfant'].'
						) THEN \'01 - Homme seul sans enfant\'
						WHEN (
							'.$parts['femme'].'
							AND '.$parts['seul'].'
							AND '.$parts['sans_enfant'].'
						) THEN \'02 - Femme seule sans enfant\'
						WHEN (
							'.$parts['homme'].'
							AND '.$parts['seul'].'
							AND '.$parts['avec_enfant'].'
							AND '.$parts['rsa_majore'].'
						) THEN \'03 - Homme seul avec enfant, RSA majoré\'
						WHEN (
							'.$parts['homme'].'
							AND '.$parts['seul'].'
							AND '.$parts['avec_enfant'].'
							AND '.$parts['rsa_non_majore'].'
						) THEN \'04 - Homme seul avec enfant, RSA non majoré\'
						WHEN (
							'.$parts['femme'].'
							AND '.$parts['seul'].'
							AND '.$parts['avec_enfant'].'
							AND '.$parts['rsa_majore'].'
						) THEN \'05 - Femme seule avec enfant, RSA majoré\'
						WHEN (
							'.$parts['femme'].'
							AND '.$parts['seul'].'
							AND '.$parts['avec_enfant'].'
							AND '.$parts['rsa_non_majore'].'
						) THEN \'06 - Femme seule avec enfant, RSA non majoré\'
						WHEN (
							'.$parts['homme'].'
							AND '.$parts['en_couple'].'
							AND '.$parts['sans_enfant'].'
						) THEN \'07 - Homme en couple sans enfant\'
						WHEN (
							'.$parts['femme'].'
							AND '.$parts['en_couple'].'
							AND '.$parts['sans_enfant'].'
						) THEN \'08 - Femme en couple sans enfant\'
						WHEN (
							'.$parts['homme'].'
							AND '.$parts['en_couple'].'
							AND '.$parts['avec_enfant'].'
						) THEN \'09 - Homme en couple avec enfant\'
						WHEN (
							'.$parts['femme'].'
							AND '.$parts['en_couple'].'
							AND '.$parts['avec_enfant'].'
						) THEN \'10 - Femme en couple avec enfant\'

						ELSE \'11 - Non connue\'
					END
				) AS "sitfam_range"',
				'COUNT(DISTINCT(Personne.id)) AS "count"'
			);
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getFieldsAnciennete( array $search ) {
			$annee = Hash::get( $search, 'Search.annee' );

			return array(
				'(
					CASE
						WHEN "Dossier"."dtdemrsa" BETWEEN ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'6\' MONTH - INTERVAL \'1\' DAY ) AND ( TIMESTAMP \''.$annee.'-12-31\' ) THEN \'moins de 6 mois\'
						WHEN "Dossier"."dtdemrsa" BETWEEN ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'1\' YEAR - INTERVAL \'1\' DAY ) AND ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'6\' MONTH ) THEN \'6 mois et moins 1 an\'
						WHEN "Dossier"."dtdemrsa" BETWEEN ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'2\' YEAR - INTERVAL \'1\' DAY ) AND ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'1\' YEAR ) THEN \'1 an et moins de 2 ans\'
						WHEN "Dossier"."dtdemrsa" BETWEEN ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'5\' YEAR - INTERVAL \'1\' DAY ) AND ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'2\' YEAR ) THEN \'2 ans et moins de 5 ans\'
						WHEN "Dossier"."dtdemrsa" < ( TIMESTAMP \''.$annee.'-12-31\' - INTERVAL \'5\' YEAR ) THEN \'5 ans et plus\'
						ELSE \'NC\'
					END
				) AS "anciennete_range"',
				'COUNT(DISTINCT(Personne.id)) AS count'
			);
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
		 * Retourne une sous-requête permettant de cibler la toute première
		 * orientation d'un allocataire donné..
		 *
		 * @return string
		 */
		protected function _sqPremiereOrientation() {
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
						'orientsstructs.date_valid IS NOT NULL'
					),
					'order' => array( 'orientsstructs.date_valid ASC' ),
					'limit' => 1
				)
			);
		}

		/**
		 * Retourne une sous-requête permettant de cibler le premier contrat
		 * d'insertion signé par un allocataire.
		 *
		 * @return string
		 */
		protected function _sqPremierContratinsertion() {
			$Contratinsertion = ClassRegistry::init( 'Contratinsertion' );
			$personneIdFied = 'Personne.id';

			return $Contratinsertion->sq(
				array(
					'fields' => array(
						'contratsinsertion.id'
					),
					'alias' => 'contratsinsertion',
					'conditions' => array(
						"contratsinsertion.personne_id = {$personneIdFied}",
						'contratsinsertion.decision_ci = \'V\'',
						'contratsinsertion.datevalidation_ci IS NOT NULL',
					),
					'order' => array( 'contratsinsertion.datevalidation_ci ASC' ),
					'limit' => 1
				)
			);
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
		 * @see Statistiqueministerielle::_completeQueryDspDspRev()
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getFieldsNivetu( array $search ) {
			return array(
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
						ELSE \'NC\'
					END
				) AS "nivetu_range"',
				'COUNT(DISTINCT(Personne.id)) AS "count"'
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
		 * @see Statistiqueministerielle::_getFieldsNivetu()
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
		 * Retourn les conditions permettant de s'assurer qu'un allocataire soit
		 * dans le champ des droits et devoirs.
		 *
		 * @see Statistiqueministerielle.conditions_droits_et_devoirs dans le webrsa.inc
		 *
		 * @return array
		 */
		protected function _getConditionsDroitsEtDevoirs() {
			return (array)Configure::read( 'Statistiqueministerielle.conditions_droits_et_devoirs' );
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
		 * Retourne les résultats d'un groupement particulier pour la partie
		 * "Questionnaire orientation", "1 - Orientation des personnes dans le
		 * champ des Droits et Devoirs au 31 décembre de l'année, au sens du
		 * type de parcours".
		 *
		 * @param string $name
		 * @param array $search
		 * @param array $fields
		 * @param string $group
		 * @return array
		 */
		protected function _getRowIndicateurOrientation( $name, array $search, array $fields, $group ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// 0. Query de base
			$base = $this->_getBaseQueryIndicateursOrientations( $search );
			$base['fields'] = $fields;
			$base['conditions'][] = $this->_getConditionsDroitsEtDevoirs();
			$base['group'] = $group;
			$base['order'] = $group;

			// 1. Personnes dans le champ des Droits et Devoirs L262-28
			$query = $base;
			$tmp = $Dossier->find( 'all', $query);
			$results[$name]['sdd'] = Hash::combine( $tmp, "{n}.0.{$group}", '{n}.0.count' );

			// 2. Dont: Orientation à dominante professionnelle
			$query = $base;
			$query['conditions'][] = $this->getConditionsTypeParcours( self::PARCOURS_PROFESSIONNEL );
			$tmp = $Dossier->find( 'all', $query);
			$results[$name]['orient_pro'] = Hash::combine( $tmp, "{n}.0.{$group}", '{n}.0.count' );

			// 3. Dont: Orientation à dominante socioprofessionnelle
			$query = $base;
			$query['conditions'][] = $this->getConditionsTypeParcours( self::PARCOURS_SOCIOPROFESSIONNEL );
			$tmp = $Dossier->find( 'all', $query);
			$results[$name]['orient_sociopro'] = Hash::combine( $tmp, "{n}.0.{$group}", '{n}.0.count' );

			// 4. Dont: Orientation à dominante sociale
			$query = $base;
			$query['conditions'][] = $this->getConditionsTypeParcours( self::PARCOURS_SOCIAL );
			$tmp = $Dossier->find( 'all', $query);
			$results[$name]['orient_sociale'] = Hash::combine( $tmp, "{n}.0.{$group}", '{n}.0.count' );

			// 5. Dont: En attente d'orientation
			$query = $base;
			$query['conditions'][] = 'Orientstruct.id IS NULL';
			$tmp = $Dossier->find( 'all', $query);
			$results[$name]['attente_orient'] = Hash::combine( $tmp, "{n}.0.{$group}", '{n}.0.count' );

			return $results;
		}

		/**
		 * Retourne les résultats de la partie "Questionnaire orientation", "1 -
		 * Orientation des personnes dans le champ des Droits et Devoirs au 31
		 * décembre de l'année, au sens du type de parcours".
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursOrientations( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			return Hash::merge(
				$this->_getRowIndicateurOrientation(
					'Indicateurage',
					$search,
					$this->_getFieldsTrancheAge( $search ),
					'age_range'
				),
				$this->_getRowIndicateurOrientation(
					'Indicateursitfam',
					$search,
					$this->_getFieldsSitfam( $search ),
					'sitfam_range'
				),
				$this->_getRowIndicateurOrientation(
					'Indicateurnivetu',
					$search,
					$this->_getFieldsNivetu( $search ),
					'nivetu_range'
				),
				$this->_getRowIndicateurOrientation(
					'Indicateuranciennete',
					$search,
					$this->_getFieldsAnciennete( $search ),
					'anciennete_range'
				)
			);
		}

		/**
		 * Retourne les résultats de la partie "Questionnaire orientation", "2 -
		 * Organismes de prise en charge des personnes dans le champ des Droits
		 * et Devoirs au 31 décembre de l'année, dont le référent unique a été
		 * désigné.".
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursOrganismes( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// 0. Query de base
			$base = $this->_getBaseQueryIndicateursOrientations( $search );
			$base['fields'] = array( 'COUNT(DISTINCT(Personne.id)) AS "count"' );
			$base['conditions'][] = $this->_getConditionsDroitsEtDevoirs();

			// 0. Dont le référent unique a été désigné
			$base['joins'][] = $Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) );

			// 1. Nombre de personnes dans le champ des Droits et Devoirs (L262-28) au 31 décembre de l'année
			$query = $base;
			$results['Indicateurorganisme']['total'] = Hash::get( $Dossier->find( 'all', $query ), '0.0.count' );

			// 2. Nombre de personnes en attente d'orientation
			$query = $base;
			$query['conditions'][] = 'Orientstruct.id IS NULL';
			$results['Indicateurorganisme']['attente_orient'] = Hash::get( $Dossier->find( 'all', $query ), '0.0.count' );

			// 3. Dont le référent appartient à...
			$organismes = (array)Configure::read( 'Statistiqueministerielle.conditions_indicateurs_organismes' );
			foreach( $organismes as $organisme => $conditions ) {
				if( !empty( $conditions ) ) {
					$query = $base;
					$query['conditions'][] = 'Orientstruct.id IS NOT NULL';
					$query['conditions'][] = $conditions;
					$results['Indicateurorganisme'][$organisme] = Hash::get( $Dossier->find( 'all', $query ), '0.0.count' );
				}
				else {
					$results['Indicateurorganisme'][$organisme] = null;
				}
			}

			return $results;
		}

		/**
		 * Retourne les résultats concernant un type de CER particulier pour la
		 * partie "Questionnaire orientation", "3 - Délais entre les différentes
		 * étapes de l'orientation au cours de l'année".
		 *
		 * @param array $search
		 * @param string $type_cer
		 * @param array $querydataTypecerOriginal
		 * @param integer $nbMoisTranche1
		 * @param integer $nbMoisTranche2
		 * @return array
		 */
		protected function _getIndicateursDelaisParTypeCer( $search, $type_cer, $querydataTypecerOriginal, $nbMoisTranche1, $nbMoisTranche2 ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			$conditions = (array)Configure::read( "Statistiqueministerielle.conditions_types_cers.{$type_cer}" );

			if( $type_cer == 'cer_pro_social' ) {
				$conditions[] = array(
					'OR' => array(
						$this->getConditionsTypeParcours( self::PARCOURS_SOCIAL, 'Typeorientcer' ),
						$this->getConditionsTypeParcours( self::PARCOURS_SOCIOPROFESSIONNEL, 'Typeorientcer' )
					)
				);
			}
			else if( $type_cer == 'cer_pro' ) {
				$conditions = $this->getConditionsTypeParcours( self::PARCOURS_PROFESSIONNEL, 'Typeorientcer' );
				$conditionsPpae = (array)Configure::read( "Statistiqueministerielle.conditions_types_cers.ppae" );
				if( !empty( $conditionsPpae ) ) {
					$conditions[] = array( 'NOT' => $conditionsPpae );
				}
			}

			if( !empty( $conditions ) ) {
				$querydataTypecer = $querydataTypecerOriginal;

				$querydataTypecer['conditions'][] = $conditions;

				// Délai moyen
				$querydataDelaimoyen = $querydataTypecer;
				$querydataDelaimoyen['fields'] = array( 'COALESCE( AVG( "Contratinsertion"."date_saisi_ci" - "Orientstruct"."date_valid" ), 0 ) AS "count"' );
				$results['Indicateurdelai']["{$type_cer}_delai_moyen"] = Hash::get( $Dossier->find( 'all', $querydataDelaimoyen ), '0.0.count' );

				// Nombre moyen au cours de l'année
				$querydataTypecer['fields'] = array( 'COUNT(Contratinsertion.id) AS "count"' );
				$results['Indicateurdelai']["{$type_cer}_nombre_moyen"] = Hash::get( $Dossier->find( 'all', $querydataTypecer ), '0.0.count' );

				// Dont contrats signés dans le mois après la décision d'orientation
				$querydataTypecerMois = $querydataTypecer;
				$querydataTypecerMois['conditions'][] = array(
					"AGE( Contratinsertion.date_saisi_ci, Orientstruct.date_valid ) <= INTERVAL '{$nbMoisTranche1} month'"
				);
				$results['Indicateurdelai']["{$type_cer}_delai_{$nbMoisTranche1}_mois"] = Hash::get( $Dossier->find( 'all', $querydataTypecerMois ), '0.0.count' );

				// Dont contrats signés entre un mois et trois mois après la décision d'orientation
				$querydataTypecerMois = $querydataTypecer;
				$querydataTypecerMois['conditions'][] = array(
					"AGE( Contratinsertion.date_saisi_ci, Orientstruct.date_valid ) > INTERVAL '{$nbMoisTranche1} month'",
					"AGE( Contratinsertion.date_saisi_ci, Orientstruct.date_valid ) <= INTERVAL '{$nbMoisTranche2} months'",
				);
				$results['Indicateurdelai']["{$type_cer}_delai_{$nbMoisTranche1}_{$nbMoisTranche2}_mois"] = Hash::get( $Dossier->find( 'all', $querydataTypecerMois ), '0.0.count' );

				// Dont contrats signés plus de trois mois après la décision d'orientation
				$querydataTypecerMois = $querydataTypecer;
				$querydataTypecerMois['conditions'][] = array(
					"AGE( Contratinsertion.date_saisi_ci, Orientstruct.date_valid ) > INTERVAL '{$nbMoisTranche2} months'",
				);
				$results['Indicateurdelai']["{$type_cer}_delai_plus_{$nbMoisTranche2}_mois"] = Hash::get( $Dossier->find( 'all', $querydataTypecerMois ), '0.0.count' );
			}
			else {
				$results['Indicateurdelai']["{$type_cer}_delai_moyen"] = null;
				$results['Indicateurdelai']["{$type_cer}_nombre_moyen"] = null;
				$results['Indicateurdelai']["{$type_cer}_delai_{$nbMoisTranche1}_mois"] = null;
				$results['Indicateurdelai']["{$type_cer}_delai_{$nbMoisTranche1}_{$nbMoisTranche2}_mois"] = null;
				$results['Indicateurdelai']["{$type_cer}_delai_plus_{$nbMoisTranche2}_mois"] = null;
			}

			return $results;
		}

		/**
		 * Retourne les résultats de la partie "Questionnaire orientation", "3 -
		 * Délais entre les différentes étapes de l'orientation au cours de
		 * l'année".
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursDelais( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// 0. On est dans le champ des bénéficiaires soumis à droits et devoirs.
			$query = $this->_getBaseQuery( $search );
			$query['conditions'][] = $this->_getConditionsDroitsEtDevoirs();

			// 0. On ne prend en compte que la toute première orientation, si elle a lieu cette année-là
			$sq = $this->_sqPremiereOrientation();
			$query['joins'][] = $Dossier->Foyer->Personne->join(
				'Orientstruct',
				array(
					'type' => 'INNER',
					'conditions' => array(
						"Orientstruct.id IN ( {$sq} )",
						// Validation de l'orientation dans l'année
						'Orientstruct.date_valid >=' => "{$annee}-01-01",
						'Orientstruct.date_valid <=' => "{$annee}-12-31"
					)
				)
			);
			$query['joins'][] = $Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) );

			// Délai moyen pour la première orientation
			$queryOrientation = $query;
			$queryOrientation['fields'] = array( 'AVG( DATE_PART( \'DAYS\', "Orientstruct"."date_valid" - DATE_TRUNC( \'MONTH\', "Dossier"."dtdemrsa" ) ) ) AS "count"' );
			$results['Indicateurdelai']['delai_moyen_orientation'] = Hash::get( $Dossier->find( 'all', $queryOrientation ), '0.0.count' );

			// Délai moyen pour la signature du premier CER
			$querySignature = $query;
			$querySignature['fields'] = array( 'AVG("Contratinsertion"."datevalidation_ci" - "Orientstruct"."date_valid") AS "count"' );
			$sq = $this->_sqPremierContratinsertion();
			$querySignature['joins'][] = $Dossier->Foyer->Personne->join(
				'Contratinsertion',
				array(
					'type' => 'INNER',
					'conditions' => array(
						// Dont le CER est le premier
						"Contratinsertion.id IN ( {$sq} )",
						// Signature du CER dans l'année
						'Contratinsertion.datevalidation_ci >=' => "{$annee}-01-01",
						'Contratinsertion.datevalidation_ci <=' => "{$annee}-12-31"
					)
				)
			);
			$results['Indicateurdelai']['delai_moyen_signature'] = Hash::get( $Dossier->find( 'all', $querySignature ), '0.0.count' );

			// Préparation du query par type de CER
			$replacements = array( 'Structurereferente' => 'Structurereferentecer', 'Typeorient' => 'Typeorientcer' );
			$queryTypecerOriginal = $querySignature;
			$queryTypecerOriginal['joins'][] = array_words_replace(
				$Dossier->Foyer->Personne->Contratinsertion->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
				$replacements
			);

			$queryTypecerOriginal['joins'][] = array_words_replace(
				$Dossier->Foyer->Personne->Contratinsertion->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) ),
				$replacements
			);

			foreach( array_keys( $this->types_cers ) as $type_cer ) {
				$results = Hash::merge(
					$results,
					$this->_getIndicateursDelaisParTypeCer(
						$search,
						$type_cer,
						$queryTypecerOriginal,
						$this->types_cers[$type_cer]['nbMoisTranche1'],
						$this->types_cers[$type_cer]['nbMoisTranche2']
					)
				);
			}

			return $results;
		}

		/**
		 * Retourne les conditions concernant un type d'orientation, suivant le
		 * CG connecté. Le seul modèle utilisable est Typeorient ou un de ses alias.
		 *
		 * Les valeurs lues dans la configuration (webrsa.inc) sont:
		 *	- Cg.departement: le numéro de département
		 *	- clés primaires de la table typesorients:
		 *		* CG 58: Typeorient.emploi_id
		 *		* CG 66: Orientstruct.typeorientprincipale.Emploi et Orientstruct.typeorientprincipale.SOCIAL
		 *		* CG 93: Orientstruct.typeorientprincipale.Emploi, Orientstruct.typeorientprincipale.Social et Orientstruct.typeorientprincipale.Socioprofessionnelle
		 *
		 * @param string $type Une des constantes PARCOURS_PROFESSIONNEL, PARCOURS_SOCIOPROFESSIONNEL ou PARCOURS_SOCIAL
		 * @param string $alias L'alias pour le modèle Typeorient
		 * @return array
		 * @throws RuntimeException
		 */
		public function getConditionsTypeParcours( $type, $alias = 'Typeorient' ) {
			$departement = Configure::read( 'Cg.departement' );

			// Vérification du département
			if( !in_array( $departement, array( 58, 66, 93, 976 ) ) ) {
				$msgstr = sprintf( 'La configuration de Cg.departement n\'est pas correcte dans le webrsa.inc: %s', $departement );
				throw new RuntimeException( $msgstr );
			}

			// Choix du champ
			if( Configure::read( 'with_parentid' ) ) {
				$field = 'parentid';
			}
			else {
				$field = 'id';
			}

			$conditions = array();

			if( $type === self::PARCOURS_PROFESSIONNEL ) {
				$configureKey = 'Statistiqueministerielle.conditions_types_parcours.professionnel';
			}
			else if( $type === self::PARCOURS_SOCIOPROFESSIONNEL ) {
				$configureKey = 'Statistiqueministerielle.conditions_types_parcours.socioprofessionnel';
			}
			else if( $type === self::PARCOURS_SOCIAL ) {
				$configureKey = 'Statistiqueministerielle.conditions_types_parcours.social';
			}
			else {
				$msgstr = sprintf( 'Le type de parcours suivant n\'est pas défini: %s', $type );
				throw new RuntimeException( $msgstr );
			}

			$conditions = (array)Configure::read( $configureKey );
			$conditions = array(
				"{$alias}.{$field} IS NOT NULL",
				$conditions
			);
			// FIXME: le champ ne fonctionne pas dans les remplacements
			// -> il faut spécifier Typeorient.parentid... dans le webrsa.inc
			// -> On peut supprimer le choix du champ ci-dessus, il ne sert à rien
			$replacements = array( 'Typeorient' => $alias, 'id' => $field );
			$conditions = array_words_replace( $conditions, $replacements );

			return $conditions;
		}

		/**
		 * Retourne les conditions ...
		 * Les modèles utilisables sont Typeorient, Structurereferente ou un de
		 * leurs alias.
		 *
		 * @param string $type Une des constantes ORGANISME_SPE, ORGANISME_SPE_POLE_EMPLOI ou ORGANISME_HORS_SPE
		 * @param string $alias L'alias pour les modèles Typeorient et Structurereferente
		 * @return array
		 * @throws RuntimeException
		 */
		public function getConditionsTypeOrganisme( $type, array $aliases = array() ) {
			if( $type === self::ORGANISME_SPE ) {
				$configureKey = 'Statistiqueministerielle.conditions_organismes.SPE';
			}
			else if( $type === self::ORGANISME_SPE_POLE_EMPLOI ) {
				$configureKey = 'Statistiqueministerielle.conditions_organismes.SPE_PoleEmploi';
			}
			else if( $type === self::ORGANISME_HORS_SPE ) {
				$configureKey = 'Statistiqueministerielle.conditions_organismes.HorsSPE';
			}
			else {
				$msgstr = sprintf( 'Le type d\'organisme suivant n\'est pas défini: %s', $type );
				throw new RuntimeException( $msgstr );
			}

			$conditions = array_words_replace(
				(array)Configure::read( $configureKey ),
				$aliases
			);

			return $conditions;
		}

		/**
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		protected function _completeQueryReorientationsSpeHorsSpe( array $query, array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );

			$query['joins'][] = $Dossier->Foyer->Personne->join(
				'Orientstruct',
				array(
					'type' => 'INNER',
					'conditions' => array(
						'Orientstruct.statut_orient' => 'Orienté',
						'Orientstruct.date_valid IS NOT NULL',
						"Orientstruct.date_valid BETWEEN '{$annee}-01-01' AND '{$annee}-12-31'"
					)
				)
			);
			$query['joins'][] = $Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) );
			$query['joins'][] = $Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) );

			// Orientation précédente
			$replacements = array( 'Orientstruct' => 'Orientstructpcd', 'Typeorient' => 'Typeorientpcd', 'Structurereferente' => 'Structurereferentepcd' );
			$query['joins'][] = array_words_replace(
				$Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'INNER' ) ),
				$replacements
			);
			$query['joins'][] = array_words_replace(
				$Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
				$replacements
			);
			$query['joins'][] = array_words_replace(
				$Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
				$replacements
			);
			$sq = $Dossier->Foyer->Personne->Orientstruct->sq(
				array(
					'alias' => 'orientsstructspcds',
					'fields' => array( 'orientsstructspcds.id' ),
					'contain' => false,
					'conditions' => array(
						'orientsstructspcds.personne_id = Personne.id',
						'orientsstructspcds.statut_orient' => 'Orienté',
						'orientsstructspcds.date_valid IS NOT NULL',
						'orientsstructspcds.date_valid < Orientstruct.date_valid',
					),
					'order' => array(
						'orientsstructspcds.date_valid DESC'
					),
					'limit' => 1
				)
			);
			$query['conditions'][] = "Orientstructpcd.id IN ( {$sq} )";

			// Qui ont été réorienté(e)s
			$query['conditions'][] = array(
				'OR' => array(
					array(
						$this->getConditionsTypeOrganisme( self::ORGANISME_HORS_SPE ),
						$this->getConditionsTypeOrganisme( self::ORGANISME_SPE, $replacements )
					),
					array(
						$this->getConditionsTypeOrganisme( self::ORGANISME_SPE ),
						$this->getConditionsTypeOrganisme( self::ORGANISME_HORS_SPE, $replacements )
					)
				)
			);

			// On s'assure que ce soit la dernière réorientation de l'année
			$replacements = array( 'Typeorient' => 'changementstypesorients', 'Structurereferente' => 'changementsstructuresreferentes' );
			$sq = $Dossier->Foyer->Personne->Orientstruct->sq(
				array(
					'alias' => 'changementsorientations',
					'fields' => array( 'changementsorientations.id' ),
					'contain' => false,
					'joins' => array(
						array_words_replace(
							$Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
							array( 'Orientstruct' => 'changementsorientations' ) + $replacements
						),
						array_words_replace(
							$Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
							array( 'Orientstruct' => 'changementsorientations' ) + $replacements
						)
					),
					'conditions' => array(
						'changementsorientations.personne_id = Personne.id',
						'changementsorientations.statut_orient' => 'Orienté',
						'changementsorientations.date_valid IS NOT NULL',
						'changementsorientations.date_valid > Orientstruct.date_valid',
						"changementsorientations.date_valid BETWEEN '{$annee}-01-01' AND '{$annee}-12-31'",
						'OR' => array(
							array(
								$this->getConditionsTypeOrganisme( self::ORGANISME_HORS_SPE ),
								$this->getConditionsTypeOrganisme( self::ORGANISME_SPE, $replacements )
							),
							array(
								$this->getConditionsTypeOrganisme( self::ORGANISME_SPE ),
								$this->getConditionsTypeOrganisme( self::ORGANISME_HORS_SPE, $replacements )
							)
						)
					)
				)
			);
			$query['conditions'][] = "NOT EXISTS( {$sq} )";

			return $query;
		}

		/**
		 * Retourne le query de base pour la partie "Questionnaire orientation",
		 * "4 - Nombre et profil des personnes réorientées au cours de l'année,
		 * au sens de la loi".
		 *
		 * FIXME: docs...
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getBaseQueryIndicateursReorientationsSpeHorsSpe( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );

			$query = $this->_getBaseQuery( $search );
			$query = $this->_completeQueryDspDspRev( $query, $search );
			$query = $this->_completeQueryReorientationsSpeHorsSpe( $query, $search );

			return $query;
		}

		/**
		 * Retourne les résultats d'un groupement particulier pour la partie
		 * "Questionnaire orientation", "4 - Nombre et profil des personnes
		 * réorientées au cours de l'année, au sens de la loi".
		 *
		 * @param string $name
		 * @param array $search
		 * @param array $fields
		 * @param string $group
		 * @return array
		 */
		protected function _getRowIndicateurReorientation( $name, array $search, array $fields, $group ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// 0. Personnes réorientées au cours de l'année
 			$base = $this->_getBaseQueryIndicateursReorientationsSpeHorsSpe( $search );
			$query = $base;

			$query['fields'] = $fields;
			$query['group'] = $group;
			$query['order'] = $group;
			$results[$name]['reorientes'] = Hash::combine( $Dossier->find( 'all', $query ), "{n}.0.{$group}", '{n}.0.count' );

			// 1. Organismes appartenant ou participant au SPE vers organismes hors SPE
			$query = $base;
			$query['fields'] = $fields;
			$query['group'] = $group;
			$query['order'] = $group;
			$query['conditions'][] = $this->getConditionsTypeOrganisme( self::ORGANISME_HORS_SPE );
			$results[$name]['organismes_hors_spe'] = Hash::combine( $Dossier->find( 'all', $query ), "{n}.0.{$group}", '{n}.0.count' );

			// 2. Organismes hors SPE vers organismes appartenant ou participant au SPE
			$query = $base;
			$query['fields'] = $fields;
			$query['group'] = $group;
			$query['order'] = $group;
			$query['conditions'][] = $this->getConditionsTypeOrganisme( self::ORGANISME_SPE );
			$results[$name]['organismes_spe'] = Hash::combine( $Dossier->find( 'all', $query ), "{n}.0.{$group}", '{n}.0.count' );

			return $results;
		}

		/**
		 * Retourne les résultats de la partie "Questionnaire orientation", "4 -
		 * Nombre et profil des personnes réorientées au cours de l'année, au
		 * sens de la loi".
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursReorientations( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			return Hash::merge(
				$this->_getRowIndicateurReorientation(
					'Indicateurage',
					$search,
					$this->_getFieldsTrancheAge( $search ),
					'age_range'
				),
				$this->_getRowIndicateurReorientation(
					'Indicateursitfam',
					$search,
					$this->_getFieldsSitfam( $search ),
					'sitfam_range'
				),
				$this->_getRowIndicateurReorientation(
					'Indicateurnivetu',
					$search,
					$this->_getFieldsNivetu( $search ),
					'nivetu_range'
				),
				$this->_getRowIndicateurReorientation(
					'Indicateuranciennete',
					$search,
					$this->_getFieldsAnciennete( $search ),
					'anciennete_range'
				)
			);
		}

		/**
		 *
		 * @return string
		 * @throws InternalErrorException
		 */
		protected function _getModeleNonOrientationProEp() {
			$departement = Configure::read( 'Cg.departement' );

			switch( $departement ) {
				case 58:
					return 'Nonorientationproep58';
					break;
				case 66:
					return 'Nonorientationproep66';
					break;
				case 93:
					return 'Nonorientationproep93';
					break;
				case 976:
					return 'Nonorientationproep58'; // Tant qu'il n'ont pas de thématique à eux
					break;
				default:
					throw new InternalErrorException( 'La configuration de Cg.departement n\'est pas correcte dans le webrsa.inc' );
			}
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryPassageNonOrientationProEp( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$modeleNonorientationproep = $this->_getModeleNonOrientationProEp();

			// FIXME
			//$query = $this->_getBaseQueryIndicateursReorientationsSpeHorsSpe( $search );
			$query = $this->_getBaseQuery( $search );
			$query = $this->_completeQueryDspDspRev( $query, $search );

			$query['conditions'] = Hash::merge(
				$query['conditions'],
				array(
					'Dossierep.themeep' => Inflector::tableize( $modeleNonorientationproep ),
					'Passagecommissionep.etatdossierep' => array( 'traite', 'annule' ),
					'Commissionep.etatcommissionep' => 'traite',
					"Commissionep.dateseance BETWEEN '{$annee}-01-01' AND '{$annee}-12-31'"
				)
			);

			$query['fields'] = array( 'COUNT(DISTINCT(Personne.id)) AS "count"' );

			$query['joins'][] = $Dossier->Foyer->Personne->join( 'Dossierep', array( 'type' => 'INNER' ) );
			$query['joins'][] = $Dossier->Foyer->Personne->Dossierep->join( 'Passagecommissionep', array( 'type' => 'INNER' ) );
			$query['joins'][] = $Dossier->Foyer->Personne->Dossierep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'INNER' ) );

			// La décision du niveau le plus important
			$modeleDecision = Inflector::camelize( 'decision'.Inflector::underscore( $modeleNonorientationproep ) );
			$table = Inflector::tableize( $modeleDecision );
			$sq = $Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modeleDecision}->sq(
				array(
					'alias' => $table,
					'fields' => array( "{$table}.id" ),
					'conditions' => array(
						"{$table}.passagecommissionep_id = {$modeleDecision}.passagecommissionep_id"
					),
					'order' => array(
						"( CASE WHEN {$table}.etape = 'cg' THEN 2 ELSE 1 END ) DESC"
					),
					'limit' => 1
				)
			);
			$query['joins'][] = $Dossier->Foyer->Personne->Dossierep->Passagecommissionep->join(
				$modeleDecision,
				array(
					'type' => 'INNER',
					'conditions' => array(
						"{$modeleDecision}.id IN ( {$sq} )"
					)
				)
			);

			return $query;
		}

		/**
		 *
		 * @url http://www.legifrance.gouv.fr/affichCodeArticle.do?cidTexte=LEGITEXT000006074069&idArticle=LEGIARTI000019868953&dateTexte=20140725
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getIndicateursMotifsReorientationEp( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$modeleNonorientationproep = $this->_getModeleNonOrientationProEp();
			$modeleDecision = Inflector::camelize( 'decision'.Inflector::underscore( $modeleNonorientationproep ) );
			$results = array();

			// 1. Passages en EP
			$queryPassage = $this->_getQueryPassageNonOrientationProEp( $search );
			$results['Indicateurep']['total'] = Hash::get( $Dossier->find( 'all', $queryPassage ), '0.0.count' );

			// 2. Maintiens de l'orientation (SPE)
			$queryMaintien = $this->_getQueryPassageNonOrientationProEp( $search );
			// FIXME (?) au CG 58, c'est maintienref / reorientation, et les autres ?
			$queryMaintien['conditions'][] = array( "{$modeleDecision}.decision" => array( 'maintienref', 'annule' ) );
			$results['Indicateurep']['maintien'] = Hash::get( $Dossier->find( 'all', $queryMaintien ), '0.0.count' );

			// 3. Réorientation vers un organisme SPE
			$queryReorientation = $this->_getQueryPassageNonOrientationProEp( $search );

			$replacements = array( 'Structurereferente' => 'Structurereferentedecision', 'Typeorient' => 'Typeorientdecision' );
			$queryReorientation['joins'][] = array_words_replace(
				$Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modeleDecision}->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
				$replacements
			);
			$queryReorientation['joins'][] = array_words_replace(
				$Dossier->Foyer->Personne->Dossierep->Passagecommissionep->{$modeleDecision}->join( 'Typeorient', array( 'type' => 'INNER' ) ),
				$replacements
			);
			$queryReorientation = $this->_completeQueryReorientationsSpeHorsSpe( $queryReorientation, $search );
			$queryReorientation['joins'][] = $Dossier->Foyer->Personne->Dossierep->join( $modeleNonorientationproep, array( 'type' => 'INNER' ) );
			$queryReorientation['conditions'][] = array( "{$modeleDecision}.decision" => 'reorientation' );
			// FIXME: vérifier si on a les mêmes chiffres qu'avant au CG 58
			//$queryReorientation['conditions'][] = "{$modeleNonorientationproep}.nvorientstruct_id = Orientstruct.id";
			$queryReorientation['conditions'][] = "{$modeleNonorientationproep}.orientstruct_id = Orientstructpcd.id";
			$queryReorientation['conditions'][] = $this->getConditionsTypeOrganisme( self::ORGANISME_SPE );

			$results['Indicateurep']['reorientation'] = Hash::get( $Dossier->find( 'all', $queryReorientation ), '0.0.count' );

			return $results;
		}

		/**
		 * Retourne les résultats de la partie "Questionnaire orientation", "4a
		 * - Motifs des réorientations d'un organisme appartenant ou participant
		 * au SPE vers un organisme hors SPE au cours de l'année" et "4b - Recours
		 * à l'article L262-31 au cours de l'année".
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursMotifsReorientation( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// 1. Nombre de personnes réorientées d'un organisme appartenant ou participant au SPE vers un organisme hors SPE au cours de l'année (1) :
			$query = $this->_getBaseQueryIndicateursReorientationsSpeHorsSpe( $search );
			$query['fields'] = array( 'COUNT(DISTINCT(Personne.id)) AS "count"' );
			$query['conditions'][] = $this->getConditionsTypeOrganisme( self::ORGANISME_HORS_SPE );
			$results['Indicateursocial']['total'] = Hash::get( $Dossier->find( 'all', $query ), '0.0.count' );

			// Total par catégorie de motifs
			$conditionsAutre = array( 'NOT' => array() );
			$motifs = array( 'orientation_initiale_inadaptee', 'changement_situation_allocataire' );
			foreach( $motifs as $motif ) {
				$conditions = (array)Configure::read( "Statistiqueministerielle.conditions_indicateurs_motifs_reorientation.{$motif}" );
				if( !empty( $conditions ) ) {
					$conditionsAutre['NOT'][] = $conditions;
					$queryMotif = $query;
					$queryMotif['conditions'][] = $conditions;
					$results['Indicateursocial'][$motif] = Hash::get( $Dossier->find( 'all', $queryMotif ), '0.0.count' );
				}
				else {
					$results['Indicateursocial'][$motif] = null;
				}
			}

			$queryAutre = $query;
			$queryAutre['conditions'][] = $conditions;
			$results['Indicateursocial']['autre'] = Hash::get( $Dossier->find( 'all', $queryAutre ), '0.0.count' );

			return Hash::merge(
				$results,
				$this->_getIndicateursMotifsReorientationEp( $search )
			);
		}

		// ---------------------------------------------------------------------

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursCaracteristiquesContrats( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			$query = $this->_getBaseQuery( $search );

			// En cours de validité au 31 décembre
			$query['conditions']['Contratinsertion.decision_ci'] = 'V';
			$query['conditions'][] = array(
				'Contratinsertion.dd_ci <=' => "{$annee}-12-31",
				'Contratinsertion.df_ci >=' => "{$annee}-12-31",
			);

			$query['fields'] = array( 'COUNT( DISTINCT "Contratinsertion"."id" ) AS "Contratinsertion__count"' );

			$replacements = array( 'Structurereferente' => 'Structurereferentecer', 'Typeorient' => 'Typeorientcer' );
			$query['joins'][] = $Dossier->Foyer->Personne->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) );
			$query['joins'][] = array_words_replace(
				$Dossier->Foyer->Personne->Contratinsertion->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
				$replacements
			);
			$query['joins'][] = array_words_replace(
				$Dossier->Foyer->Personne->Contratinsertion->Structurereferente->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
				$replacements
			);

			// Pour chacune des lignes
			$categories_cers = array(
				'cer' => array(),
				'ppae' => $this->getConditionsTypeOrganisme( self::ORGANISME_SPE_POLE_EMPLOI, $replacements ),
				'cer_pro' => array(
					$this->getConditionsTypeOrganisme( self::ORGANISME_SPE, $replacements ),
					'NOT' => array(
						$this->getConditionsTypeOrganisme( self::ORGANISME_SPE_POLE_EMPLOI, $replacements )
					)
				),
				'cer_social_pro' => $this->getConditionsTypeOrganisme( self::ORGANISME_HORS_SPE, $replacements ),
			);

			// Pour chacune des colonnes
			foreach( Hash::normalize( $categories_cers ) as $categorie_cer => $conditionsCategorie ) {
				$conditions = (array)Configure::read( "Statistiqueministerielle.conditions_caracteristiques_contrats.{$categorie_cer}" );

				if( $conditionsCategorie !== false ) {
					// 1. Nombre total
					$queryTotal = $query;
					$queryTotal['conditions'] = Hash::merge(
						$queryTotal['conditions'],
						$conditions,
						$conditionsCategorie
					);
					$results['Indicateurcaracteristique']["{$categorie_cer}_total"] = Hash::get( $Dossier->find( 'all', $queryTotal ), '0.Contratinsertion.count' );

					// 2. Nombre dont le bénéficiaire est soumis à droits et devoirs
					$queryChampDd = $query;
					$queryChampDd['conditions'] = Hash::merge(
						$queryChampDd['conditions'],
						$conditions,
						$conditionsCategorie
					);
					$queryChampDd['conditions'][] = $this->_getConditionsDroitsEtDevoirs();
					$results['Indicateurcaracteristique']["{$categorie_cer}_droitsdevoirs"] = Hash::get( $Dossier->find( 'all', $queryChampDd ), '0.Contratinsertion.count' );

					// 3. Nombre dont le bénéficiaire n'est pas soumis à droits et devoirs
					$queryHorsChampDd = $query;
					$queryHorsChampDd['conditions'] = Hash::merge(
						$queryHorsChampDd['conditions'],
						$conditions,
						$conditionsCategorie
					);
					$queryHorsChampDd['conditions'][] = array( 'NOT' => array( $this->_getConditionsDroitsEtDevoirs() ) );
					$results['Indicateurcaracteristique']["{$categorie_cer}_horsdroitsdevoirs"] = Hash::get( $Dossier->find( 'all', $queryHorsChampDd ), '0.Contratinsertion.count' );
				}
			}

			// Partie durées, pour chacune des colonnes
			foreach( Hash::normalize( $categories_cers ) as $categorie_cer => $conditionsCategorie ) {
				if( in_array( $categorie_cer, array( 'cer_pro', 'cer_social_pro' ) ) ) {
					foreach( $this->durees_cers as $duree_cer => $conditionsDureescers ) {
						$queryDureeCer = $query;
						$queryDureeCer['conditions'] = Hash::merge(
							$queryDureeCer['conditions'],
							$conditionsCategorie,
							$conditionsDureescers
						);

						// 1. Nombre total
						$queryTotal = $queryDureeCer;
						$results['Indicateurcaracteristique']["{$categorie_cer}_{$duree_cer}_total"] = Hash::get( $Dossier->find( 'all', $queryTotal ), '0.Contratinsertion.count' );

						// 2. Nombre dont le bénéficiaire est soumis à droits et devoirs
						$queryChampDd = $queryDureeCer;
						$queryChampDd['conditions'][] = $this->_getConditionsDroitsEtDevoirs();
						$results['Indicateurcaracteristique']["{$categorie_cer}_{$duree_cer}_droitsdevoirs"] = Hash::get( $Dossier->find( 'all', $queryChampDd ), '0.Contratinsertion.count' );

						// 3. Nombre dont le bénéficiaire n'est pas soumis à droits et devoirs
						$queryHorsChampDd = $queryDureeCer;
						$queryHorsChampDd['conditions'][] = array( 'NOT' => array( $this->_getConditionsDroitsEtDevoirs() ) );
						$results['Indicateurcaracteristique']["{$categorie_cer}_{$duree_cer}_horsdroitsdevoirs"] = Hash::get( $Dossier->find( 'all', $queryHorsChampDd ), '0.Contratinsertion.count' );
					}
				}
			}

			return $results;
		}

		protected function _conditionsNatures() {
			$results = array();
			$departement = (int)Configure::read( 'Cg.departement' );

			if( $departement === 66 ) {
				$Actioncandidat = ClassRegistry::init( 'Actioncandidat' );
				$query = array(
					'fields' => array(
						'Actioncandidat.id',
						'Actioncandidat.naturecer'
					),
					'conditions' => array(
						'Actioncandidat.naturecer IS NOT NULL'
					),
					'contain' => false
				);

				// 1. Préparation du tableau de conditions
				$results = $this->natures_cers;
				foreach( array_keys( $results ) as $key ) {
					$results[$key] = array( 'Actioncandidat.id' => array() );
				}

				// 2. Remplissage du tableau de conditions
				foreach( $Actioncandidat->find( 'list', $query ) as $id => $naturecer ) {
					$results[$naturecer]['Actioncandidat.id'][] = $id;
				}

				// 3. Nettoyage du tableau de conditions
				foreach( $results as $key => $conditions ) {
					if( empty( $conditions['Actioncandidat.id'] ) ) {
						$results[$key] = null;
					}
				}
			}
			else {
				 $results = (array)Configure::read( 'Statistiqueministerielle.conditions_natures_contrats' )
					+ array_combine( array_keys( $this->natures_cers ), array_fill( 0, count( $this->natures_cers ), null ) );
			}

			if( !empty( $results ) ) {
				$tmp = $results;
				$results = array();
				foreach( $tmp as $key => $value ) {
					$results["{$key} - {$this->natures_cers[$key]}"] = $value;
				}
			}

			return $results;
		}

		/**
		 * ...
		 *
		 * @see Configure Statistiquesministerielles.indicateurs_natures_contrats.optim
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursNaturesContrats( array $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			$base = $this->_getBaseQuery( $search );

			// En cours de validité au 31 décembre
			$base['conditions']['Contratinsertion.decision_ci'] = 'V';
			$base['conditions'][] = array(
				'Contratinsertion.dd_ci <=' => "{$annee}-12-31",
				'Contratinsertion.df_ci >=' => "{$annee}-12-31",
			);

			$base['fields'] = array( 'COUNT( DISTINCT "Contratinsertion"."id" ) AS "Contratinsertion__count"' );

			$replacements = array( 'Structurereferente' => 'Structurereferentecer', 'Typeorient' => 'Typeorientcer' );
			$base['joins'][] = $Dossier->Foyer->Personne->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) );
			$base['joins'][] = array_words_replace(
				$Dossier->Foyer->Personne->Contratinsertion->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
				$replacements
			);
			$base['joins'][] = array_words_replace(
				$Dossier->Foyer->Personne->Contratinsertion->Structurereferente->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
				$replacements
			);

			$departement = (int)Configure::read( 'Cg.departement' );
			if( $departement === 93 ) {
				$base['joins'][] = $Dossier->Foyer->Personne->Contratinsertion->join( 'Cer93', array( 'type' => 'INNER' ) );
				$base['joins'][] = $Dossier->Foyer->Personne->Contratinsertion->Cer93->join( 'Cer93Sujetcer93', array( 'type' => 'LEFT OUTER' ) );
			}
			else if( $departement === 66 ) {
				$base['joins'][] = $Dossier->Foyer->Personne->Contratinsertion->join( 'Actioncandidat', array( 'type' => 'LEFT OUTER' ) );
			}

			$organismes = array(
				'spe' => array(
					$this->getConditionsTypeOrganisme(
						self::ORGANISME_SPE,
						$replacements
					),
					'NOT' => array(
						$this->getConditionsTypeOrganisme(
							self::ORGANISME_SPE_POLE_EMPLOI,
							$replacements
						)
					)
				),
				'horsspe' => $this->getConditionsTypeOrganisme(
					self::ORGANISME_HORS_SPE,
					$replacements
				)
			);

			$conditionsNatures = $this->_conditionsNatures();

			$optim = Configure::read( 'Statistiquesministerielles.indicateurs_natures_contrats.optim' );
			if( $optim === false ) {
				foreach( $organismes as $organisme => $conditionsOrganisme ) {
					foreach( $conditionsNatures as $nature_cer => $conditionsNature ) {
						if( !empty( $conditionsNature ) ) {
							$query = $base;
							$query['conditions'][] = $conditionsOrganisme;
							$query['conditions'][] = $conditionsNature;

							$results['Indicateurnature'][$organisme][$nature_cer] = Hash::get( $Dossier->find( 'all', $query ), '0.Contratinsertion.count' );
						}
						else {
							$results['Indicateurnature'][$organisme][$nature_cer] = null;
						}
					}
				}
			}
			else {
				$Dbo = $Dossier->getDataSource();
				$casesOrganisme = '';
				$casesNatures = '';

				$query = $base;
				$query['group'] = array();

				foreach( $organismes as $organisme => $conditionsOrganisme ) {
					$casesOrganisme .= " WHEN ".$Dbo->conditions( $conditionsOrganisme, true, false, $Dossier )." THEN '".Sanitize::clean( $organisme, array( 'encode' => false ) )."'";
				}
				if( !empty( $casesOrganisme ) ) {
					$casesOrganisme = "( CASE {$casesOrganisme} ELSE NULL END )";
				}
				else {
					$casesOrganisme = "( NULL )";
				}

				$query['fields'][] = "{$casesOrganisme} AS \"Contratinsertion__organisme\"";
				$query['conditions'][] = "{$casesOrganisme} IS NOT NULL";
				if( $casesOrganisme !== "( NULL )" ) {
					$query['group'][] = $casesOrganisme;
				}

				foreach( $conditionsNatures as $nature_cer => $conditionsNature ) {
					if( !empty( $conditionsNature ) ) {
						$casesNatures .= " WHEN ".$Dbo->conditions( $conditionsNature, true, false, $Dossier )." THEN '".Sanitize::clean( $nature_cer, array( 'encode' => false ) )."'";
					}
				}

				foreach( $organismes as $organisme => $conditionsOrganisme ) {
					foreach( $conditionsNatures as $nature_cer => $conditionsNature ) {
						if( !empty( $conditionsNature ) ) {
							$results[$organisme][$nature_cer] = 0;
						}
						else {
							$results[$organisme][$nature_cer] = null;
						}
					}
				}

				if( !empty( $casesNatures ) ) {
					$casesNatures = "( CASE {$casesNatures} ELSE NULL END )";
				}
				else {
					$casesNatures = "( NULL )";
				}

				$query['fields'][] = "{$casesNatures} AS \"Contratinsertion__nature_cer\"";
				$query['conditions'][] = "{$casesNatures} IS NOT NULL";
				if( $casesNatures !== "( NULL )" ) {
					$query['group'][] = $casesNatures;
				}

				if( (int)Configure::read( 'Cg.departement' ) === 93 ) {
					$query['group'][] = 'Cer93Sujetcer93.sujetcer93_id';
					$query['group'][] = 'Cer93Sujetcer93.soussujetcer93_id';
					$query['group'][] = 'Cer93Sujetcer93.valeurparsoussujetcer93_id';
				}

				$sql = $Dossier->sq( $query );
				$sql = str_replace( '("IAE")', '(IAE)', $sql ).'/*'.microtime( true ).'*/';
				$entries = $Dossier->query( $sql );

				foreach( $entries as $entry ) {
					$nature_cer = str_replace( '("IAE")', '(IAE)', $entry['Contratinsertion']['nature_cer'] );
					$results[$entry['Contratinsertion']['organisme']][$nature_cer] += $entry['Contratinsertion']['count'];
				}
				foreach( array_keys( $results ) as $organisme ) {
					ksort( $results[$organisme] );
				}
				$results = array( 'Indicateurnature' => $results );
			}

			return $results;
		}

		/**
		 * Vérification du paramétrage pour le tableau "Indicateurs de natures
		 * des actions des contrats" (CG 93).
		 *
		 * @return array
		 */
		public function getTableauNaturesContrats() {
			$departement = (int)Configure::read( 'Cg.departement' );
			$results = array();

			$base = array();
			$replacements = array();

			if( $departement === 93 ) {
				$Sujetcer93 = ClassRegistry::init( 'Sujetcer93' );
				$base = array(
					'fields' => array(
						'Sujetcer93.name',
						'Soussujetcer93.name',
						'Valeurparsoussujetcer93.name'
					),
					'joins' => array(
						$Sujetcer93->join( 'Soussujetcer93', array( 'type' => 'LEFT OUTER' ) ),
						$Sujetcer93->Soussujetcer93->join( 'Valeurparsoussujetcer93', array( 'type' => 'LEFT OUTER' ) )
					),
					'conditions' => array(),
					'order' => array(
						'Sujetcer93.name',
						'Soussujetcer93.name',
						'Valeurparsoussujetcer93.name'
					)
				);

				$replacements = array(
					'Cer93Sujetcer93.sujetcer93_id' => 'Sujetcer93.id',
					'Cer93Sujetcer93.soussujetcer93_id' => 'Soussujetcer93.id',
					'Cer93Sujetcer93.valeurparsoussujetcer93_id' => 'Valeurparsoussujetcer93.id',
				);

				$Model = $Sujetcer93;
			}
			else if( $departement === 66 ) {
				$base = array(
					'fields' => array(
						'Actioncandidat.name'
					),
					'conditions' => array(),
					'order' => array(
						'Actioncandidat.name'
					)
				);

				$Model = ClassRegistry::init( 'Actioncandidat' );
			}

			$conditionsNatures = $this->_conditionsNatures();
			if( !empty( $base ) ) {
				foreach( $conditionsNatures as $nature_cer => $conditionsNature ) {
					if( !empty( $conditionsNature ) ) {
						$query = $base;
						$query['conditions'][] = $conditionsNature;
						$query = array_words_replace( $query, $replacements );

						$results[$nature_cer] = $Model->find( 'all', $query );
					}
					else {
						$results[$nature_cer] = null;
					}
				}
			}
			else {
				foreach( $conditionsNatures as $nature_cer => $conditionsNature ) {
					$results[$nature_cer] = null;
				}
			}

			return $results;
		}

		/**
		 *
		 * @return array
		 */
		public function getTableauxConditions() {
			$departement = (int)Configure::read( 'Cg.departement' );

			$result = array();
			if( in_array( $departement, array( 66, 93 ), true ) ) {
				$result = array(
					$this->alias => array(
						'conditions_natures_contrats' => array(
							'fields' => (
								$departement === 66
									? array(
										'Actioncandidat.name'
									)
									: array(
										'Sujetcer93.name',
										'Soussujetcer93.name',
										'Valeurparsoussujetcer93.name'
									)
							),
							'records' => $this->getTableauNaturesContrats()
						)
					)
				);
			}

			return $result;
		}

		/**
		 * Permet de tester toutes les clés de configuration du webrsa.inc pour
		 * le module "Statistiques ministérielles":
		 *	- Statistiqueministerielle.conditions_droits_et_devoirs
		 *	- Statistiqueministerielle.conditions_types_parcours
		 *	- Statistiqueministerielle.conditions_indicateurs_organismes
		 *	- Statistiqueministerielle.conditions_organismes
		 *	- Statistiqueministerielle.conditions_indicateurs_motifs_reorientation
		 *	- Statistiqueministerielle.conditions_caracteristiques_contrats
		 *	- Statistiqueministerielle.conditions_types_cers
		 *
		 * Pour chacune de ces clés, on retourne un tableau contenant un clé
		 * 'success' et une clé 'message' qui est remplie lorsqu'on a une erreur
		 * (il s'agira de l'erreur SQL).
		 *
		 * @return array
		 */
		public function querydataFragmentsErrors() {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$search = array( 'Search' => array( 'annee' => 2000 ) );
			$return = array();

			// 1. conditions_droits_et_devoirs
			$query = $this->_getBaseQuery( $search );
			$query['conditions'][] = $this->_getConditionsDroitsEtDevoirs();
			$return['Statistiqueministerielle.conditions_droits_et_devoirs'] = $query;

			// 2. conditions_types_parcours
			$query = $this->getIndicateursOrientations( $search );
			$return['Statistiqueministerielle.conditions_types_parcours'] = $query;

			// 3. conditions_indicateurs_organismes
			$query = $this->getIndicateursOrganismes( $search );
			$return['Statistiqueministerielle.conditions_indicateurs_organismes'] = $query;

			// 4. conditions_organismes
			$query = $this->getIndicateursReorientations( $search );
			$return['Statistiqueministerielle.conditions_organismes'] = $query;

			// 5. conditions_indicateurs_motifs_reorientation
			$query = $this->getIndicateursMotifsReorientation( $search );
			$return['Statistiqueministerielle.conditions_indicateurs_motifs_reorientation'] = $query;

			// 6. conditions_caracteristiques_contrats
			$query = $this->getIndicateursCaracteristiquesContrats( $search );
			$return['Statistiqueministerielle.conditions_caracteristiques_contrats'] = $query;

			// 7. conditions_types_cers
			$query = $this->getIndicateursDelais( $search );
			$return['Statistiqueministerielle.conditions_types_cers'] = $query;

			// 8. conditions_natures_contrats
			$query = $this->getIndicateursNaturesContrats( $search );
			$return['Statistiqueministerielle.conditions_natures_contrats'] = $query;

			// Test des différentes requêtes.
			foreach( $return as $key => $query ) {
				try {
					@$Dossier->find( 'first', $query );
					$message = null;
				} catch ( Exception $e ) {
					$message = $e->getMessage();
				}
				$return[$key] = array(
					'success' => is_null( $message ),
					'message' => $message
				);
			}

			return $return;
		}
	}
?>