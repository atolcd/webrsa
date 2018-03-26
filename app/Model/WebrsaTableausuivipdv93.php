<?php
	/**
	 * Code source de la classe WebrsaTableausuivipdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe WebrsaTableausuivipdv93 contient la logique métier concernant
	 * les tableaux de suivi PDV pour le CG 93.
	 *
	 * @package app.Model
	 */
	class WebrsaTableausuivipdv93 extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaTableausuivipdv93';

		/**
		 * Ce modèle n'est pas lié à une table.
		 *
		 * @var string|boolean
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array();

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Tableausuivipdv93', 'Thematiquefp93','User' );

		/**
		 * Liste des filtres acceptés en fonction du tableau.
		 *
		 * @var array
		 */
		public $filters = array(
			'tableaud1' => array(
				'Search.annee',
				'Search.communautesr_id',
				'Search.structurereferente_id',
				'Search.referent_id',
				'Search.soumis_dd_dans_annee'
			),
			'tableaud2' => array(
				'Search.annee',
				'Search.communautesr_id',
				'Search.structurereferente_id',
				'Search.referent_id',
				'Search.soumis_dd_dans_annee'
			),
			'tableau1b3' => array(
				'Search.annee',
				'Search.communautesr_id',
				'Search.structurereferente_id',
				'Search.referent_id',
				'Search.dsps_maj_dans_annee',
			),
			'tableau1b4' => array(
				'Search.annee',
				'Search.communautesr_id',
				'Search.structurereferente_id',
				'Search.referent_id',
				'Search.typethematiquefp93_id',
				'Search.yearthematiquefp93_id',
				'Search.rdv_structurereferente',
			),
			'tableau1b5' => array(
				'Search.annee',
				'Search.communautesr_id',
				'Search.structurereferente_id',
				'Search.referent_id',
				'Search.typethematiquefp93_id',
				'Search.yearthematiquefp93_id',
				'Search.rdv_structurereferente',
			),
			'tableau1b6' => array(
				'Search.annee',
				'Search.communautesr_id',
				'Search.structurereferente_id',
				'Search.referent_id',
				'Search.rdv_structurereferente',
			)
		);

		/**
		 * Problématiques à utiliser dans le tableau 1 B3
		 *
		 * @var array
		 */
		public $problematiques = array(
			'sante',
			'logement',
			'familiales',
			'modes_gardes',
			'surendettement',
			'administratives',
			'linguistiques',
			'mobilisation',
			'qualification_professionnelle',
			'acces_emploi',
			'autres',
		);

		/**
		 * Problématiques à utiliser dans le tableau 1 B3
		 *
		 * @var array
		 */
		public $acteurs = array(
			'acteurs_sociaux',
			'acteurs_sante',
			'acteurs_culture',
		);

		/**
		 * Liste des tableaux disponibles
		 *
		 * @var array
		 */
		public $tableaux = array(
			'tableaud1' => 'D 1',
			'tableaud2' => 'D 2',
			'tableau1b3' => 'B 3',
			'tableau1b4' => 'B 4',
			'tableau1b5' => 'B 5',
			'tableau1b6' => 'B 6',
		);

		/**
		 * Liste des tranches d'âges pour le tableau D1
		 *
		 * @var array
		 */
		public $tranches_ages = array(
			'0_14' => 'Participants de moins de 15 ans',
			'15_24' => 'Participants de 15 à 24 ans',
			'25_44' => 'Participants de 25 à 44 ans',
			'45_54' => 'Participants de 45 à 54 ans',
			'55_64' => 'Participants de 55 à 64 ans',
			'65_999' => 'Participants de 65 ans et plus',
		);

		/**
		 * Liste des natures de prestation.
		 *
		 * @var array
		 */
		public $natpf = array(
			'socle' => 'Bénéficiaires RSA socle',
			'majore' => 'Bénéficiaires RSA majoré',
			'socle_activite' => 'Bénéficiaires  RSA socle+activité',
		);

		/**
		 * Liste des nationalités.
		 *
		 * @var array
		 */
		public $nati = array(
			'F' => 'Française',
			'C' => 'Union Européenne',
			'A' => 'Hors Union Européenne',
		);

		/**
		 * Liste des situations familiales.
		 *
		 * @var array
		 */
		public $sitfam = array(
			'isole_sans_enfant' => 'Isolé(e) sans enfant(s) à charge',
			'isole_avec_enfant' => 'Isolé(e) avec enfant(s) à charge',
			'en_couple_sans_enfant' => 'En couple sans enfant(s) à charge',
			'en_couple_avec_enfant' => 'En couple avec enfant(s) à charge',
		);

		/**
		 * Liste des inscriptions à Pôle Emploi.
		 *
		 * @var array
		 */
		public $inscritpe = array(
			'1' => 'Inscrits',
			'0' => 'Non inscrits',
		);

		/**
		 * Liste des catégories d'ancienneté du dispositif pour le tableau D1
		 *
		 * @var array
		 */
		public $anciennetes_dispositif = array(
			'0_0' => 'Moins de 1 an',
			'1_2' => 'De 1 an à moins de 3 ans',
			'3_5' => 'De 3 ans à moins de 6 ans',
			'6_8' => 'De 6 ans à moins de 9 ans',
			'9_999' => 'Plus de 9 ans',
		);

		/**
		 * Liste des non scolarisés.
		 *
		 * @var array
		 */
		public $non_scolarise = array(
			'1207' => 'Non scolarisé',
		);

		/**
		 * Liste des diplômes étrangers non reconnus en France.
		 *
		 * @var array
		 */
		public $diplomes_etrangers = array(
			'1' => 'Diplômes étrangers non reconnus en France',
		);

		/**
		 * Liste des colonnes du tableau D1.
		 */
		public $columns_d1 = array(
			'previsionnel',
			'reports_total',
			'reports_homme',
			'reports_femme',
			'entrees_total',
			'entrees_homme',
			'entrees_femme',
			'sorties_total',
			'sorties_homme',
			'sorties_femme',
			'participants_total',
			'participants_homme',
			'participants_femme',
		);

		/**
		 * "Cache" mémoire des catégories du tableau 1B4.
		 *
		 * @var array
		 */
		protected $_categories1b4 = null;

		/**
		 * "Cache" mémoire des catégories du tableau 1B5.
		 *
		 * @var array
		 */
		protected $_categories1b5 = null;

		/**
		 * Liste des catégories utilisées pour le tableau 1B3 avec, pour chacune
		 * d'entre elles, un array contenant la table (liée) concernée, son alias,
		 * la colonne concernée et les valeurs concernées.
		 *
		 * @var array
		 */
		protected $_categories1b3 = array(
			'sante' => array(
				'table' => 'detailsdifsocs',
				'alias' => 'sante',
				'column' => 'difsoc',
				'values' => array( '0402', '0403' )
			),
			'logement' => array(
				'table' => 'detailsdiflogs',
				'alias' => 'detailsdiflogs',
				'column' => 'diflog',
				'values' => array( '1004', '1005', '1006', '1007', '1008', '1009' )
			),
			'familiales' => array(
				'table' => 'detailsaccosocfams',
				'alias' => 'detailsaccosocfams',
				'column' => 'nataccosocfam',
				'values' => array( '0412' )
			),
			'modes_gardes' => array(
				'table' => 'detailsdifdisps',
				'alias' => 'detailsdifdisps',
				'column' => 'difdisp',
				'values' => array( '0502', '0503', '0504' )
			),
			'surendettement' => array(
				'table' => 'detailsdifsocs',
				'alias' => 'surendettement',
				'column' => 'difsoc',
				'values' => array( '0406' )
			),
			'administratives' => array(
				'table' => 'detailsdifsocs',
				'alias' => 'administratives',
				'column' => 'difsoc',
				'values' => array( '0405' )
			),
			'linguistiques' => array(
				'table' => 'detailsdifsocs',
				'alias' => 'linguistiques',
				'column' => 'difsoc',
				'values' => array( '0404' )
			),
			'mobilisation' => array(
				'table' => 'dsps',
				'alias' => 'id',
				'column' => 'id',
				// INFO: valeur impossible, pour avoir la colonne dans l'export CSV
				'values' => array( '0' )
			),
			'qualification_professionnelle' => array(
				'table' => 'dsps',
				'alias' => 'nivetu',
				'column' => 'nivetu',
				'values' => array( '1206', '1207' )
			),
			'acces_emploi' => array(
				'table' => 'dsps',
				'alias' => 'topengdemarechemploi',
				'column' => 'topengdemarechemploi',
				'values' => array( '0' )
			),
			'autres' => array(
				'table' => 'detailsaccosocindis',
				'alias' => 'detailsaccosocindis',
				'column' => 'nataccosocindi',
				'values' => array( '0420' )
			)
		);

		/**
		 * Liste, pour achacun des tableaux de suivi, le modèle sur lequel
		 * effectuer la requête pour obtenir l'export CSV.
		 *
		 * @var array
		 */
		public $modelsCorpus = array(
			'tableaud1' => 'Questionnaired1pdv93',
			'tableaud2' => 'Questionnaired2pdv93',
			'tableau1b3' => 'Rendezvous',
			'tableau1b4' => 'Ficheprescription93',
			'tableau1b5' => 'Ficheprescription93',
			'tableau1b6' => 'Rendezvous'
		);

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1Sexe( array $search ) {
			$fields = array(
				'"Situationallocataire"."sexe" AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud2Total( array $search ) {
			$cer = 'EXISTS( SELECT contratsinsertion.id FROM contratsinsertion WHERE contratsinsertion.personne_id = "Rendezvous"."personne_id" AND contratsinsertion.decision_ci = \'V\' AND contratsinsertion.dd_ci <= DATE_TRUNC( \'day\', "Questionnaired1pdv93"."date_validation" ) AND contratsinsertion.df_ci >= \''.date( 'Y-m-d' ).'\' )';

			$fields = array(
				'"Situationallocataire"."sexe" AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'hommes\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femmes\'
					END
				) AS "sexe"',
				'(
					CASE
						WHEN '.$cer.' THEN 1
						ELSE 0
					END
				) AS "cer"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Situationallocataire.sexe', $cer );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1MarcheTravail( array $search ) {
			$fields = array(
				'"Questionnaired1pdv93"."marche_travail" AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Questionnaired1pdv93.marche_travail', 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1TrancheAge( array $search ) {
			$annee = Sanitize::clean( Hash::get( $search, 'Search.annee' ), array( 'encode' => false ) );

			$cases = array();
			foreach( array_keys( $this->tranches_ages ) as $tranche_age ) {
				list( $min, $max ) = explode( '_', $tranche_age );
				$cases[] = 'WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP\''.$annee.'-12-31\', "Situationallocataire"."dtnai" ) ) BETWEEN '.$min.' AND '.$max.' THEN \''.$tranche_age.'\'';
			}

			$tranche_age = '(
				CASE
					'.implode( "\n", $cases ).'
					ELSE \'NC\'
				END
			)';

			$fields = array(
				$tranche_age.' AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( $tranche_age, 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1Vulnerable( array $search ) {
			$fields = array(
				'"Questionnaired1pdv93"."vulnerable" AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Questionnaired1pdv93.vulnerable', 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1Nivetu( array $search ) {
			$fields = array(
				'"Questionnaired1pdv93"."nivetu" AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Questionnaired1pdv93.nivetu', 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1CategorieSociopro( array $search ) {
			$fields = array(
				'"Questionnaired1pdv93"."categorie_sociopro" AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Questionnaired1pdv93.categorie_sociopro', 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1AutreCaracteristique( array $search ) {
			$fields = array(
				'"Questionnaired1pdv93"."autre_caracteristique" AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Questionnaired1pdv93.autre_caracteristique', 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1Natpf( array $search ) {
			$Situationallocataire = ClassRegistry::init( 'Situationallocataire' );

			$natpf = $Situationallocataire->virtualFields['natpf_d1'];
			$natpf = str_replace( 'ENUM::NATPF_D1::', '', $natpf );

			$fields = array(
				$natpf.' AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( $natpf, 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1Nati( array $search ) {
			$nati = '(
				CASE
					WHEN "Situationallocataire"."nati" IS NOT NULL THEN "Situationallocataire"."nati"
					ELSE \'NC\'
				END
			)';

			$fields = array(
				$nati.' AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Situationallocataire.nati', 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1Sitfam( array $search ) {
			$parts = array(
				'isole' => '"Situationallocataire"."sitfam" IN (\'CEL\', \'DIV\', \'ISO\', \'SEF\', \'SEL\', \'VEU\')',
				'en_couple' => '"Situationallocataire"."sitfam" IN (\'MAR\', \'PAC\', \'RPA\', \'RVC\', \'RVM\', \'VIM\')',
				'sans_enfant' => '"Situationallocataire"."nbenfants" = 0',
				'avec_enfant' => '"Situationallocataire"."nbenfants" > 0',
			);

			$sitfam = '(
				CASE
					WHEN '.$parts['isole'].' AND '.$parts['sans_enfant'].' THEN \'isole_sans_enfant\'
					WHEN '.$parts['isole'].' AND '.$parts['avec_enfant'].' THEN \'isole_avec_enfant\'
					WHEN '.$parts['en_couple'].' AND '.$parts['sans_enfant'].' THEN \'en_couple_sans_enfant\'
					WHEN '.$parts['en_couple'].' AND '.$parts['avec_enfant'].' THEN \'en_couple_avec_enfant\'
					ELSE \'NC\'
				END
			)';

			$fields = array(
				$sitfam.' AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( $sitfam, 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1ConditionsLogement( array $search ) {
			$fields = array(
				'"Questionnaired1pdv93"."conditions_logement" AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Questionnaired1pdv93.conditions_logement', 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1Inscritpe( array $search ) {
			$fields = array(
				'"Questionnaired1pdv93"."inscritpe" AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Questionnaired1pdv93.inscritpe', 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1AncienneteDispositif( array $search ) {
			$annee = Sanitize::clean( Hash::get( $search, 'Search.annee' ), array( 'encode' => false ) );

			$cases = array();
			foreach( array_keys( $this->anciennetes_dispositif ) as $anciennete_dispositif ) {
				list( $min, $max ) = explode( '_', $anciennete_dispositif );
				$cases[] = 'WHEN EXTRACT( YEAR FROM AGE(TIMESTAMP\''.$annee.'-12-31\', "Situationallocataire"."dtdemrsa" ) ) BETWEEN '.$min.' AND '.$max.' THEN \''.$anciennete_dispositif.'\'';
			}

			$anciennete_dispositif = '(
				CASE
					'.implode( "\n", $cases ).'
					ELSE \'NC\'
				END
			)';

			$fields = array(
				$anciennete_dispositif.' AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( $anciennete_dispositif, 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1NonScolarise( array $search ) {
			$fields = array(
				'"Questionnaired1pdv93"."nivetu" AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Questionnaired1pdv93.nivetu', 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableaud1DiplomesEtrangers( array $search ) {
			$fields = array(
				'"Questionnaired1pdv93"."diplomes_etrangers" AS "categorie"',
				'(
					CASE
						WHEN "Situationallocataire"."sexe" = \'1\' THEN \'homme\'
						WHEN "Situationallocataire"."sexe" = \'2\' THEN \'femme\'
						ELSE \'NC\'
					END
				) AS "sexe"',
				'COUNT( DISTINCT( "Questionnaired1pdv93"."id" ) ) AS "count"'
			);

			$group = array( 'Questionnaired1pdv93.diplomes_etrangers', 'Situationallocataire.sexe' );

			return array( $fields, $group );
		}

		/**
		 *
		 * @return array
		 */
		public function tableaud1Categories() {
			$Questionnaired1pdv93 = ClassRegistry::init( 'Questionnaired1pdv93' );

			$enums = Hash::merge(
				$Questionnaired1pdv93->enums(),
				$Questionnaired1pdv93->Situationallocataire->enums()
			);

			unset( $enums['Questionnaired1pdv93']['nivetu']['1207'] ); // Les non scolarisés ont une catégorie à part

			$categories = array(
				'sexe' => array(
					1 => 'Hommes',
					2 => 'Femmes',
				),
				'marche_travail' => $enums['Questionnaired1pdv93']['marche_travail'],
				'tranche_age' => $this->tranches_ages,
				'vulnerable' => $enums['Questionnaired1pdv93']['vulnerable'],
				'nivetu' => array_reverse( $enums['Questionnaired1pdv93']['nivetu'], true ),
				'categorie_sociopro' => $enums['Questionnaired1pdv93']['categorie_sociopro'],
				'autre_caracteristique' => $enums['Questionnaired1pdv93']['autre_caracteristique'],
				'natpf' => $this->natpf + array( 'NC' => 'Non défini' ),
				'nati' => $this->nati,
				'sitfam' => $this->sitfam,
				'conditions_logement' =>  $enums['Questionnaired1pdv93']['conditions_logement'],
				'inscritpe' => $this->inscritpe,
				'anciennete_dispositif' => $this->anciennetes_dispositif,
				'non_scolarise' => $this->non_scolarise,
				'diplomes_etrangers' => $this->diplomes_etrangers,
			);

			return $categories;
		}

		/**
		 * Filtre sur l'ensemble du CG, une communauté de structures référentes
		 * ou un PDV ?
		 *
		 * @param array $search
		 * @param array $fields
		 * @param boolean $and
		 * @return string
		 */
		protected function _conditionpdv( array $search, array $fields, $and = false ) {
			$fields += array(
				'structurereferente_id' => null,
				'referent_id' => null
			);

			$Dbo = $this->Tableausuivipdv93->getDataSource();

			$conditionpdv = array();

			// Filtre interne sur un ensemble de structures referentes ?
			$structuresreferentes_ids = (array)Hash::get( $search, 'Search.Structurereferente.Structurereferente' );
			if( !empty( $structuresreferentes_ids ) ) {
				$conditionpdv[] = $Dbo->conditions( array( $fields['structurereferente_id'] => $structuresreferentes_ids ), true, false );
			}
			else {
				// Filtre sur une communauté de structures référentes en particulier ?
				$communautesr_id = Hash::get( $search, 'Search.communautesr_id' );
				if( !empty( $communautesr_id ) ) {
					$sql = $this->Tableausuivipdv93->Communautesr->sqStructuresreferentes( $communautesr_id );
					$conditionpdv[] = $Dbo->conditions( array( "{$fields['structurereferente_id']} IN ( {$sql} )" ), true, false );
				}
				else {
					// Filtre sur une structures référente en particulier ?
					$pdv_id = Hash::get( $search, 'Search.structurereferente_id' );
					if( !empty( $pdv_id ) ) {
						$conditionpdv[] = $Dbo->conditions( array( $fields['structurereferente_id'] => $pdv_id ), true, false );
					}
					else {
						// Filtre sur un référent en particulier ?
						$referent_id = Hash::get( $search, 'Search.referent_id' );
						if( !empty( $referent_id ) ) {
							$conditionpdv[] = $Dbo->conditions( array( $fields['referent_id'] => suffix( $referent_id ) ), true, false );
						}
					}
				}
			}

			return ( $and ? 'AND ' : '' ).$Dbo->conditions( $conditionpdv, true, false );
		}

		/**
		 * Retourne les conditions issues des filtres du moteur de recherche à
		 * utiliser dans les tableaux D1 et D2.
		 * Celles-ci se trouvent sous les clés "annee", "conditionpdv", "conditiondd".
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _conditionpdvD1D2( array $search ) {
			return array(
				'annee' => Sanitize::clean( Hash::get( $search, 'Search.annee' ), array( 'encode' => false ) ),
				'conditionpdv' => $this->_conditionpdv(
					$search,
					array(
						'structurereferente_id' => 'Rendezvous.structurereferente_id',
						'referent_id' => 'Rendezvous.referent_id'
					)
				),
				'conditiondd' => $this->_conditionTableauxD1D2SoumisDD( $search )
			);
		}

		/**
		 * Retourne le querydata utilisé pour la tableau D1.
		 *
		 * @param array $search
		 * @return array
		 */
		public function qdTableaud1( array $search ) {
			$Questionnaired1pdv93 = ClassRegistry::init( 'Questionnaired1pdv93' );

			$conditions = $this->_conditionpdvD1D2( $search );

			$querydata = array(
				'fields' => array(),
				'conditions' => array(
					'EXTRACT( \'YEAR\' FROM Questionnaired1pdv93.date_validation )' => $conditions['annee'],
					$conditions['conditionpdv'],
					$conditions['conditiondd']
				),
				'contain' => false,
				'joins' => array(
					$Questionnaired1pdv93->join( 'Rendezvous', array( 'type' => 'INNER' ) ),
					$Questionnaired1pdv93->join( 'Situationallocataire', array( 'type' => 'INNER' ) )
				),
				'group' => array()
			);

			// Dernier RDV dont la SR est sur la communauté
			$type = Hash::get( $search, 'Search.type' );
			if( $type === 'communaute' ) {
				$sqRendezvous = array(
					'alias' => 'rendezvous',
					'fields' => array( 'rendezvous.id' ),
					'contain' => false,
					'joins' => array(
						array_words_replace(
							$Questionnaired1pdv93->Rendezvous->join( 'Questionnaired1pdv93', array( 'type' => 'INNER' ) ),
							array( 'Questionnaired1pdv93' => 'questionnairesd1pdvs93', 'Rendezvous' => 'rendezvous' )
						)
					),
					'conditions' => array(
						'rendezvous.personne_id = Rendezvous.personne_id',
						'EXTRACT( \'YEAR\' FROM questionnairesd1pdvs93.date_validation )' => $conditions['annee'],
						words_replace( $conditions['conditionpdv'], array( 'Rendezvous' => 'rendezvous' ) ),
					),
					'order' => array( 'questionnairesd1pdvs93.date_validation DESC' ),
					'limit' => 1
				);
				$sql =  $Questionnaired1pdv93->Rendezvous->sq( $sqRendezvous );
				$querydata['conditions'][] = "Rendezvous.id IN ( {$sql} )";
			}

			return $querydata;
		}

		/**
		 * Retourne le querydata nécessaire à l'export CSV du corpus pris en
		 * compte dans un historique de tableau D1.
		 *
		 * @param integer $id La clé primaire du tableau de suivi D1 historisé
		 * @return array
		 */
		public function qdExportcsvCorpusd1( $id ) {
			$querydata = array(
				'fields' => array_merge(
					$this->Tableausuivipdv93->Populationd1d2pdv93->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired2pdv93->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired2pdv93->Sortieaccompagnementd2pdv93->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Situationallocataire->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Rendezvous->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Rendezvous->Structurereferente->fields(),
					array( $this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Rendezvous->Referent->sqVirtualField( 'nom_complet' ) )
				),
				'conditions' => array(
					'Tableausuivipdv93.id' => $id,
					'Tableausuivipdv93.name' => 'tableaud1',
				),
				'joins' => array(
					$this->Tableausuivipdv93->join( 'Populationd1d2pdv93', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->join( 'Questionnaired1pdv93', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->join( 'Questionnaired2pdv93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired2pdv93->join( 'Sortieaccompagnementd2pdv93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->join( 'Situationallocataire', array( 'INNER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->join( 'Rendezvous', array( 'INNER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Rendezvous->join( 'Structurereferente', array( 'INNER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Rendezvous->join( 'Referent', array( 'INNER' ) ),
				),
				'order' => array(
					'Rendezvous.daterdv ASC',
					'Questionnaired2pdv93.date_validation ASC'
				)
			);

			return $querydata;
		}

		/**
		 * Retourne le querydata nécessaire à l'export CSV du corpus pris en
		 * compte dans un historique de tableau D2.
		 *
		 * @param integer $id La clé primaire du tableau de suivi D2 historisé
		 * @return array
		 */
		public function qdExportcsvCorpusd2( $id ) {
			$querydata = array(
				'fields' => array_merge(
					$this->Tableausuivipdv93->Populationd1d2pdv93->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired2pdv93->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired2pdv93->Sortieaccompagnementd2pdv93->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Situationallocataire->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Rendezvous->fields(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Rendezvous->Structurereferente->fields(),
					array( $this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Rendezvous->Referent->sqVirtualField( 'nom_complet' ) )
				),
				'conditions' => array(
					'Tableausuivipdv93.id' => $id,
					'Tableausuivipdv93.name' => 'tableaud2',
				),
				'joins' => array(
					$this->Tableausuivipdv93->join( 'Populationd1d2pdv93', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->join( 'Questionnaired2pdv93', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired2pdv93->join( 'Questionnaired1pdv93', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired2pdv93->join( 'Sortieaccompagnementd2pdv93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->join( 'Situationallocataire', array( 'INNER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->join( 'Rendezvous', array( 'INNER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Rendezvous->join( 'Structurereferente', array( 'INNER' ) ),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Rendezvous->join( 'Referent', array( 'INNER' ) ),
				),
				'order' => array(
					'Rendezvous.daterdv ASC',
					'Questionnaired2pdv93.date_validation ASC'
				)
			);

			return $querydata;
		}

		protected function _conditionTableauxD1D2SoumisDD( array $search ) {
			$Questionnaired1pdv93 = ClassRegistry::init( 'Questionnaired1pdv93' );
			$annee = Sanitize::clean( Hash::get( $search, 'Search.annee' ), array( 'encode' => false ) );

			// Soumis à droits et devoirs / au moins soumis une fois durant l'année
			$conditiondd = 'Situationallocataire.toppersdrodevorsa = \'1\'';

			$dd_annee = Hash::get( $search, 'Search.soumis_dd_dans_annee' );
			if( $dd_annee ) {
				$sq = $Questionnaired1pdv93->Personne->Historiquedroit->sq(
					array(
						'alias' => 'historiquesdroits',
						'fields' => array( 'historiquesdroits.personne_id' ),
						'contain' => false,
						'conditions' => array(
							'historiquesdroits.personne_id = Questionnaired1pdv93.personne_id',
							'historiquesdroits.toppersdrodevorsa' => 1,
							"( historiquesdroits.created, historiquesdroits.modified ) OVERLAPS ( DATE '{$annee}-01-01', DATE '{$annee}-12-31' )"
						)
					)
				);

				$conditiondd = array(
					'OR' => array(
						$conditiondd,
						"Questionnaired1pdv93.personne_id IN ( {$sq} )"
					)
				);
			}

			return $conditiondd;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		public function tableaud1( array $search ) {
			$Questionnaired1pdv93 = ClassRegistry::init( 'Questionnaired1pdv93' );

			$results = array();
			$categories = array_keys( $this->tableaud1Categories() );

			$qdBase = $this->qdTableaud1( $search );

			foreach( $categories as $categorie ) {
				$method = '_tableaud1'.Inflector::camelize( $categorie );

				list( $fields, $group ) = $this->{$method}( $search );

				$querydata = $qdBase;
				$querydata['fields'] = $fields;
				$querydata['group'] = $group;

				$lines = $Questionnaired1pdv93->find( 'all', $querydata );
				if( !empty( $lines ) ) {
					foreach( $lines as $line ) {
						$results[$categorie][$line[0]['categorie']]['entrees'][$line[0]['sexe']] = $line[0]['count'];
					}
				}
				else {
					$results[$categorie]['NC']['entrees']['homme'] = 0;
					$results[$categorie]['NC']['entrees']['femme'] = 0;
				}
			}

			$empty = array(
				'previsionnel' => null,
				'reports_total' => null,
				'reports_homme' => null,
				'reports_femme' => null,
				'entrees_total' => 0,
				'entrees_homme' => 0,
				'entrees_femme' => 0,
				'sorties_total' => null,
				'sorties_homme' => null,
				'sorties_femme' => null,
				'participants_total' => 0,
				'participants_homme' => 0,
				'participants_femme' => 0,
			);

			$tmp = array_keys( Hash::flatten( $this->tableaud1Categories() ) );
			$return = Hash::expand( array_fill_keys( $tmp, null ) );
			foreach( $return as $categorie1 => $data1 ) {
				$return[$categorie1] = $empty;
				$return[$categorie1]['dont'] = array();

				foreach( $data1 as $categorie2 => $data2 ) {
					$return[$categorie1]['dont'][$categorie2] = $empty;
				}
			}

			foreach( $results as $categorie1 => $data ) {
				foreach( $data as $categorie2 => $data2 ) {
					if( !isset( $return[$categorie1]['dont'][$categorie2] ) ) {
						$return[$categorie1]['dont'][$categorie2] = $empty;
					}

					foreach( $data2['entrees'] as $sexe => $nombre ) {
						$return[$categorie1]['dont'][$categorie2]["entrees_{$sexe}"] = $nombre;
						$return[$categorie1]['dont'][$categorie2]["entrees_total"] = (int)$return[$categorie1]['dont'][$categorie2]["entrees_total"] + $nombre;

						$return[$categorie1]["entrees_{$sexe}"] = (int)$return[$categorie1]["entrees_{$sexe}"] + $nombre;
						$return[$categorie1]["entrees_total"] = (int)$return[$categorie1]["entrees_total"] + $nombre;
					}
				}
			}

			// Catégories spéciales
			$return['diplomes_etrangers'] = $return['diplomes_etrangers']['dont']['1'];
			unset( $return['diplomes_etrangers']['dont'] );

			// Non scolarisé, 1207
			// Il faut en plus les comptabiliser dans la ligne 5, sous l'intitulé 1206
			$return['non_scolarise'] = $return['non_scolarise']['dont']['1207'];
			foreach( $return['non_scolarise'] as $key => $value ) {
				if( $return['nivetu']['dont']['1206'][$key] !== null || $value !== null  ) {
					$return['nivetu']['dont']['1206'][$key] = (int)$return['nivetu']['dont']['1206'][$key] + $value;
				}
			}

			unset( $return['non_scolarise']['dont'] );
			unset( $return['nivetu']['dont']['1207'] );

			// Calcul des participants
			foreach( $return as $categorie => $data ) {
				foreach( array( 'total', 'homme', 'femme' ) as $column ) {
					$reports = $return[$categorie]["reports_{$column}"];
					$entrees = $return[$categorie]["entrees_{$column}"];
					$sorties = $return[$categorie]["sorties_{$column}"];

					if( !is_null( $reports ) || !is_null( $entrees ) || !is_null( $sorties ) ) {
						$participants = (int)$reports + (int)$entrees - (int)$sorties;
					}
					else {
						$participants = null;
					}

					$return[$categorie]["participants_{$column}"] = $participants;
				}

				if( isset( $data['dont'] ) ) {
					foreach( $data['dont'] as $categorie2 => $data2 ) {
						foreach( array( 'total', 'homme', 'femme' ) as $column ) {
							$reports = $return[$categorie]['dont'][$categorie2]["reports_{$column}"];
							$entrees = $return[$categorie]['dont'][$categorie2]["entrees_{$column}"];
							$sorties = $return[$categorie]['dont'][$categorie2]["sorties_{$column}"];

							if( !is_null( $reports ) || !is_null( $entrees ) || !is_null( $sorties ) ) {
								$participants = (int)$reports + (int)$entrees - (int)$sorties;
							}
							else {
								$participants = null;
							}

							$return[$categorie]['dont'][$categorie2]["participants_{$column}"] = $participants;
						}
					}
				}
			}

			// Suppression des NC
			foreach( $return as $categorie => $data ) {
				unset( $return[$categorie]['dont']['NC'] );
			}

			return $return;
		}

		/**
		 *
		 * @return array
		 */
		public function tableaud2Categories() {
			$Questionnaired2pdv93 = ClassRegistry::init( 'Questionnaired2pdv93' );

			// Liste des sorties de l'accompagnement
			$querydata = array(
				'fields' => array(
					'Sortieaccompagnementd2pdv93.id',
					'Sortieaccompagnementd2pdv93.name',
					'Parent.name',
				),
				'joins' => array(
					$Questionnaired2pdv93->Sortieaccompagnementd2pdv93->join( 'Parent' )
				),
				'conditions' => array(
					'Sortieaccompagnementd2pdv93.parent_id IS NOT NULL'
				),
				'order' => array(
					'Parent.id ASC',
					'Sortieaccompagnementd2pdv93.id ASC',
				)
			);
			$sortiesaccompagnement = $Questionnaired2pdv93->Sortieaccompagnementd2pdv93->find( 'list', $querydata );

			foreach( $sortiesaccompagnement as $group => $sortiesniveau2 ) {
				$sortiesaccompagnement[$group] = array();
				foreach( $sortiesniveau2 as $id => $sortieniveau2 ) {
					$sortiesaccompagnement[$group][$sortieniveau2] = null;
				}
			}

			$enums = $Questionnaired2pdv93->enums();

			$categories = Hash::normalize( array_keys( $enums['Questionnaired2pdv93']['situationaccompagnement'] ) );
			$categories['sortie_obligation'] = $sortiesaccompagnement;
			$categories['changement_situation'] = Hash::normalize( array_values( $enums['Questionnaired2pdv93']['chgmentsituationadmin'] ) );

			return $categories;
		}

		/**
		 * Retourne le querydata utilisé pour la tableau D2.
		 *
		 * @param array $search
		 * @return array
		 */
		public function qdTableaud2( array $search ) {
			$Questionnaired2pdv93 = ClassRegistry::init( 'Questionnaired2pdv93' );

			$conditions = $this->_conditionpdvD1D2( $search );

			$querydata = array(
				'fields' => array(
					'"Questionnaired2pdv93"."situationaccompagnement" AS "Tableaud2pdv93__categorie1"',
					'( CASE WHEN ( "Questionnaired2pdv93"."situationaccompagnement" = \'changement_situation\' ) THEN "Questionnaired2pdv93"."chgmentsituationadmin" WHEN ( "Questionnaired2pdv93"."situationaccompagnement" = \'sortie_obligation\' ) THEN ( SELECT sortiesaccompagnementsd2pdvs93.name FROM sortiesaccompagnementsd2pdvs93 WHERE sortiesaccompagnementsd2pdvs93.id = "Sortieaccompagnementd2pdv93"."parent_id" ) ELSE NULL END ) AS "Tableaud2pdv93__categorie2"',
					'"Sortieaccompagnementd2pdv93"."name" AS "Tableaud2pdv93__categorie3"',
					'COUNT("Questionnaired2pdv93"."id") AS "Tableaud2pdv93__nombre"',
					'COUNT(CASE WHEN ( "Personne"."sexe" = \'1\' ) THEN "Questionnaired2pdv93"."id" ELSE NULL END) AS "Tableaud2pdv93__hommes"',
					'COUNT(CASE WHEN ( "Personne"."sexe" = \'2\' ) THEN "Questionnaired2pdv93"."id" ELSE NULL END) AS "Tableaud2pdv93__femmes"',
					'COUNT(CASE WHEN ( EXISTS( SELECT contratsinsertion.id FROM contratsinsertion WHERE contratsinsertion.personne_id = "Personne"."id" AND contratsinsertion.decision_ci = \'V\' AND contratsinsertion.dd_ci <= DATE_TRUNC( \'day\', "Questionnaired2pdv93"."date_validation" ) AND contratsinsertion.df_ci >= DATE_TRUNC( \'day\', "Questionnaired2pdv93"."date_validation" ) ) ) THEN 1 ELSE NULL END ) AS "Tableaud2pdv93__cer"',
				),
				'conditions' => array(
					'EXTRACT( \'YEAR\' FROM Questionnaired2pdv93.date_validation )' => $conditions['annee'],
					$conditions['conditionpdv'],
					$conditions['conditiondd']
				),
				'joins' => array(
					$Questionnaired2pdv93->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Questionnaired2pdv93->join( 'Questionnaired1pdv93', array( 'type' => 'INNER' ) ),
					$Questionnaired2pdv93->join( 'Sortieaccompagnementd2pdv93', array( 'type' => 'LEFT OUTER' ) ),
					$Questionnaired2pdv93->Questionnaired1pdv93->join( 'Situationallocataire', array( 'type' => 'INNER' ) ),
					$Questionnaired2pdv93->Questionnaired1pdv93->join( 'Rendezvous', array( 'type' => 'INNER' ) ),
				),
				'contain' => false,
				'group' => array(
					'Questionnaired2pdv93.situationaccompagnement',
					'Questionnaired2pdv93.chgmentsituationadmin',
					'Sortieaccompagnementd2pdv93.parent_id',
					'Sortieaccompagnementd2pdv93.name',
				)
			);

			// Dernier RDV dont la SR est sur la communauté
			$type = Hash::get( $search, 'Search.type' );
			if( $type === 'communaute' ) {
				$sqRendezvous = array(
					'alias' => 'rendezvous',
					'fields' => array( 'rendezvous.id' ),
					'contain' => false,
					'joins' => array(
						array_words_replace(
							$Questionnaired2pdv93->Questionnaired1pdv93->Rendezvous->join( 'Questionnaired1pdv93', array( 'type' => 'INNER' ) ),
							array( 'Questionnaired1pdv93' => 'questionnairesd1pdvs93', 'Rendezvous' => 'rendezvous' )
						),
						array_words_replace(
							$Questionnaired2pdv93->Questionnaired1pdv93->join( 'Questionnaired2pdv93', array( 'type' => 'INNER' ) ),
							array( 'Questionnaired2pdv93' => 'questionnairesd2pdvs93', 'Questionnaired1pdv93' => 'questionnairesd1pdvs93' )
						)
					),
					'conditions' => array(
						'rendezvous.personne_id = Rendezvous.personne_id',
						'EXTRACT( \'YEAR\' FROM questionnairesd2pdvs93.date_validation )' => $conditions['annee'],
						words_replace( $conditions['conditionpdv'], array( 'Rendezvous' => 'rendezvous' ) ),
					),
					'order' => array( 'questionnairesd2pdvs93.date_validation DESC' ),
					'limit' => 1
				);
				$sql =  $Questionnaired2pdv93->Questionnaired1pdv93->Rendezvous->sq( $sqRendezvous );
				$querydata['conditions'][] = "Rendezvous.id IN ( {$sql} )";
			}

			return $querydata;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		public function tableaud2( array $search ) {
			$Questionnaired2pdv93 = ClassRegistry::init( 'Questionnaired2pdv93' );

			$querydata = $this->qdTableaud2( $search );

			// categorie1, categorie2, categorie3, nombre, hommes, femmes, couvertcer
			$results = $Questionnaired2pdv93->find( 'all', $querydata );

			$return = $this->tableaud2Categories();

			$dimensions = array();
			foreach( array_keys( $return ) as $key ) {
				$dimensions[$key] = Hash::dimensions( (array)$return[$key] ) + 1;
			}

			// Formattage du tableau de résultats
			$enums = $Questionnaired2pdv93->enums();

			foreach( $results as $result ) {
				$data = $result['Tableaud2pdv93'];
				unset( $data['categorie1'], $data['categorie2'], $data['categorie3'] );

				// Si on n'a que la catégorie 1
//				if( empty( $result['Tableaud2pdv93']['categorie2'] ) ) {
				if( $dimensions[$result['Tableaud2pdv93']['categorie1']] == 1 ) {
					$return[$result['Tableaud2pdv93']['categorie1']] = $data;
				}
				// Si on a les catégories 1 et 2
//				else if( empty( $result['Tableaud2pdv93']['categorie3'] ) ) {
				else if( $dimensions[$result['Tableaud2pdv93']['categorie1']] == 2 ) {
					if( isset( $enums['Questionnaired2pdv93']['chgmentsituationadmin'][$result['Tableaud2pdv93']['categorie2']] ) ) {
						$categorie2 = $enums['Questionnaired2pdv93']['chgmentsituationadmin'][$result['Tableaud2pdv93']['categorie2']];
					}
					else {
						$categorie2 = $result['Tableaud2pdv93']['categorie2'];
					}
					$return[$result['Tableaud2pdv93']['categorie1']][$categorie2] = $data;
				}
				// Si on a les catégories 1, 2 et 3
				else {
					if( isset( $enums['Questionnaired2pdv93']['chgmentsituationadmin'][$result['Tableaud2pdv93']['categorie2']] ) ) {
						$categorie2 = $enums['Questionnaired2pdv93']['chgmentsituationadmin'][$result['Tableaud2pdv93']['categorie2']];
					}
					else {
						$categorie2 = $result['Tableaud2pdv93']['categorie2'];
					}
					$return[$result['Tableaud2pdv93']['categorie1']][$categorie2][$result['Tableaud2pdv93']['categorie3']] = $data;
				}
			}

			// Total des participants, c'est à dire ceux pris en compte dans la tableau D1
			$Questionnaired1pdv93 = ClassRegistry::init( 'Questionnaired1pdv93' );
			$querydata = $this->qdTableaud1( $search );

			$conditiondd = $this->_conditionTableauxD1D2SoumisDD( $search );
			$querydata['conditions'][] = $conditiondd;

			list( $fields, $group ) = $this->_tableaud2Total( $search );
			$querydata['fields'] = $fields;
			$querydata['group'] = $group;
			$results = $Questionnaired1pdv93->find( 'all', $querydata );

			$totaux = array(
				'nombre' => 0,
				'hommes' => 0,
				'femmes' => 0,
				'cer' => 0
			);
			foreach( $results as $result ) {
				$totaux[$result[0]['sexe']] += $result[0]['count'];
				if( $result[0]['cer'] ) {
					$totaux['cer'] += $result[0]['count'];
				}
			}
			$totaux['nombre'] = $totaux['hommes'] + $totaux['femmes'];

			$nombre_total = max( array( $totaux['nombre'], 1 ) );

			// Ajout de la ligne de totaux au début du tableau de résultats
			$return = array( 'totaux' => $totaux ) + $return;

			// On complète le tableau pour les catégories vides
			$return = Hash::flatten( $return );
			foreach( $return as $key => $value ) {
				if( is_null( $value ) ) {
					$return[$key] = array(
						'nombre' => 0,
						'nombre_%' => 0,
						'hommes' => 0,
						'hommes_%' => 0,
						'femmes' => 0,
						'femmes_%' => 0,
						'cer' => 0,
						'cer_%' => 0,
					);
				}
			}
			$return = Hash::expand( $return );

			// Calcul des pourcentages
			foreach( $return as $categorie1 => $data1 ) {
				if( isset( $data1['nombre'] ) ) {
					foreach( array( 'nombre', 'hommes', 'femmes', 'cer' ) as $key ) {
						$return[$categorie1]["{$key}_%"] = $data1[$key] / $nombre_total * 100;
					}
				}
				else {
					foreach( $data1 as $categorie2 => $data2 ) {
						if( isset( $data2['nombre'] ) ) {
							foreach( array( 'nombre', 'hommes', 'femmes', 'cer' ) as $key ) {
								$return[$categorie1][$categorie2]["{$key}_%"] = $data2[$key] / $nombre_total * 100;
							}
						}
						else {
							foreach( $data2 as $categorie3 => $data3 ) {
								if( isset( $data3['nombre'] ) ) {
									foreach( array( 'nombre', 'hommes', 'femmes', 'cer' ) as $key ) {
										$return[$categorie1][$categorie2][$categorie3]["{$key}_%"] = $data3[$key] / $nombre_total * 100;
									}
								}
							}
						}
					}
				}
			}

			return $return;
		}

		/**
		 *
		 * @param string $field
		 * @return string
		 */
		protected function _conditionStatutRdv( $field = 'statutrdv_id' ) {
			$field = '"'.implode( '"."', explode( '.', $field ) ).'"';
			$values = "'".implode( "', '", (array)Configure::read( 'Tableausuivipdv93.statutrdv_id' ) )."'";
			return "{$field} IN ( {$values} )";
		}

		/**
		 *
		 * @param string $field
		 * @return string
		 */
		protected function _conditionNumcodefamille( $field = 'numcodefamille', $typeacteur = null ) {
			$configureKey = 'Tableausuivipdv93.numcodefamille';
			if( !is_null( $typeacteur ) ) {
				$configureKey = "{$configureKey}.{$typeacteur}";
			}

			$values = "'".implode( "', '", Hash::flatten( (array)Configure::read( $configureKey ) ) )."'";
			return "numcodefamille IN ( {$values} )";
		}

		/**
		 * Retourne une condition permettant de limiter les résultats du niveau
		 * CG aux seuls PDV définis dans la configuration.
		 *
		 * @see WebrsaTableausuivipdv93::listePdvs()
		 *
		 * @param string $field
		 * @return string
		 */
		protected function _conditionStructurereferenteIsPdv( $field = 'structurereferente_id' ) {
			$ids = array_keys( (array)$this->listePdvs() );

			if( !empty( $ids ) ) {
				return $field.' IN ( '.implode( ',', $ids ).' )';
			}

			return '1 = 0';
		}

		/**
		 * Retourne les conditions - dans les clés "annee", "conditionpdv" et
		 * "conditionmaj" - à utiliser dans les requêtes du tableau 1B3 et issues
		 * des filtres du moteur de recherche.
		 *
		 * @param array $search Les filtres du moteur de recherche
		 * @return array
		 */
		protected function _tableau1b3Conditions( array $search ) { // Conditions venant du filtre de recherche
			$Dbo = $this->Tableausuivipdv93->getDataSource();

			$conditions = array(
				'annee' => Sanitize::clean( Hash::get( $search, 'Search.annee' ), array( 'encode' => false ) ),
				'conditionpdv' => $this->_conditionpdv(
					$search,
					array(
						'structurereferente_id' => 'rendezvous.structurereferente_id',
						'referent_id' => 'rendezvous.referent_id'
					),
					true
				),
				'conditionmaj' => null,
			);

			// Filtre sur les DSP mises à jour dans l'année
			// @see _tableau1b3ConditionDspMajDansAnnee
			$dsp_maj = Hash::get( $search, 'Search.dsps_maj_dans_annee' );
			if( !empty( $dsp_maj ) ) {
				$conditions['conditionmaj'] = "AND dsps_revs.id IS NOT NULL";
			}

			return $conditions;
		}

		/**
		 * Retourne la condition (avec la clause WHERE) à appliquer dans la jointure
		 * sur dsps_revs pour le tableau 1B3 lorsque la case "Dont les DSP ont été
		 * mises à jour dans l'année" est cochée.
		 *
		 * @param array $search Les filtres du moteur de recherche
		 * @return string
		 */
		protected function _tableau1b3ConditionDspMajDansAnnee( array $search ) {
			$conditions = $this->_tableau1b3Conditions( $search );
			$dsp_maj = Hash::get( $search, 'Search.dsps_maj_dans_annee' );

			if( $dsp_maj ) {
				$result = "WHERE EXTRACT( 'YEAR' FROM dsps_revs.modified ) = '{$conditions['annee']}'";
			}
			else {
				$result = '';
			}

			return $result;
		}

		/**
		 * Retourne la sous-requête permettant de comptabiliser le nombre de DSP
		 * ou DSP CG par catégorie pour les valeurs d'une certaine colonne d'une
		 * table liée à la table dsps(_revs) en fonction des filtres du moteur
		 * de recherche.
		 *
		 * @param array $search Les filtres du moteur de recherche
		 * @param string $categorie Le nom de la catégorie calculée
		 * @param string $table Le nom de la table liée à la table dsps(_revs)
		 * @param string $alias L'alias pour la table liée
		 * @param string $column Le colonne à prendre en compte
		 * @param array $values Les valeurs de la colonne entrant dans la catégorie
		 * @return string
		 */
		protected function _tableau1b3CategorieSubtable( array $search, $categorie, $table, $alias, $column, array $values ) {
			$conditions = $this->_tableau1b3Conditions( $search );

			$conditionsDspRev = $this->_tableau1b3ConditionDspMajDansAnnee( $search );

			$sql = "SELECT
							'{$categorie}'::text AS \"difficultes_exprimees\",
							dsps.id AS dsp, dsps_revs.id AS dsp_rev
						FROM personnes
							INNER JOIN dsps on (personnes.id=dsps.personne_id)
							INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
							--
							LEFT OUTER JOIN {$table} AS {$alias} ON (
								dsps.id = {$alias}.dsp_id
								AND {$alias}.{$column} IN ( '".implode( "', '", $values )."' )
							)
							LEFT OUTER JOIN dsps_revs ON (
								dsps.personne_id = dsps_revs.personne_id
								AND (dsps_revs.personne_id, dsps_revs.id) IN (
									SELECT personne_id, MAX(dsps_revs.id)
										FROM dsps_revs
										{$conditionsDspRev}
										GROUP BY personne_id
								)
							)
							LEFT OUTER JOIN {$table}_revs {$alias}_revs ON (
								dsps_revs.id = {$alias}_revs.dsp_rev_id
								AND {$alias}_revs.{$column} IN ( '".implode( "', '", $values )."' )
							)
							--
						WHERE
							-- si pas de DSP MAJ on prend la DSP CAF
							(
								(dsps_revs.id IS NULL AND {$alias}.{$column} IS NOT NULL)
								OR (dsps_revs.id IS NOT NULL AND {$alias}_revs.{$column} IS NOT NULL)
							)
							-- Dont le type de RDV est individuel
							AND rendezvous.typerdv_id IN ( ".implode( ',', (array)Configure::read( 'Tableausuivipdv93.typerdv_id' ) )." )
							-- avec un RDV honore durant l'annee N
							AND EXTRACT('YEAR' FROM rendezvous.daterdv) = '{$conditions['annee']}' AND ".$this->_conditionStatutRdv()."
							-- pour la structure referente X (eventuellement)
							{$conditions['conditionpdv']}
							{$conditions['conditionmaj']}
							-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select
							AND ".$this->_conditionStructurereferenteIsPdv();

			return $sql;
		}

		/**
		 * Retourne la sous-requête permettant de comptabiliser le nombre de DSP
		 * ou DSP CG par catégorie pour les valeurs d'une certaine colonne de la
		 * table dsps ou dsps_revs en fonction des filtres du moteur de recherche.
		 *
		 * @param array $search Les filtres du moteur de recherche
		 * @param string $categorie Le nom de la catégorie calculée
		 * @param string $alias L'alias pour la table dsps(_revs)
		 * @param string $column Le colonne à prendre en compte
		 * @param array $values Les valeurs de la colonne entrant dans la catégorie
		 * @return string
		 */
		protected function _tableau1b3Categorie( array $search, $categorie, $alias, $column, array $values ) {
			$conditions = $this->_tableau1b3Conditions( $search );

			$conditionsDspRev = $this->_tableau1b3ConditionDspMajDansAnnee( $search );

			$sql = "SELECT
							'{$categorie}'::text AS \"difficultes_exprimees\",
							dsps.id AS dsp, dsps_revs.id AS dsp_rev
						FROM personnes
							INNER JOIN dsps on (personnes.id=dsps.personne_id)
							INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
							--
							LEFT OUTER JOIN dsps_revs ON (
								dsps.personne_id = dsps_revs.personne_id
								AND (dsps_revs.personne_id, dsps_revs.id) IN (
									SELECT personne_id, MAX(dsps_revs.id)
										FROM dsps_revs
										{$conditionsDspRev}
										GROUP BY personne_id
								)
							)
							LEFT OUTER JOIN dsps AS {$alias} ON (
								dsps.id = {$alias}.id
								AND {$alias}.{$column} IN ( '".implode( "', '", $values )."' )
							)
							LEFT OUTER JOIN dsps_revs AS {$alias}_revs ON (
								dsps_revs.id = {$alias}_revs.id
								AND {$alias}_revs.{$column} IN ( '".implode( "', '", $values )."' )
							)
							--
						WHERE
							-- si pas de DSP MAJ on prend la DSP CAF
							(
								(dsps_revs.id IS NULL AND {$alias}.{$column} IS NOT NULL)
								OR (dsps_revs.id IS NOT NULL AND {$alias}_revs.{$column} IS NOT NULL)
							)
							-- Dont le type de RDV est individuel
							AND rendezvous.typerdv_id IN ( ".implode( ',', (array)Configure::read( 'Tableausuivipdv93.typerdv_id' ) )." )
							-- avec un RDV honore durant l'annee N
							AND EXTRACT('YEAR' FROM rendezvous.daterdv) = '{$conditions['annee']}' AND ".$this->_conditionStatutRdv()."
							-- pour la structure referente X (eventuellement)
							{$conditions['conditionpdv']}
							{$conditions['conditionmaj']}
							-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select
							AND ".$this->_conditionStructurereferenteIsPdv();

			return $sql;
		}

		/**
		 * Retourne la requête d'insertion dans la table de la population 1B3.
		 *
		 * @param array $search
		 * @return string
		 */
		public function sqlInsertTableau1b3( array $search ) {
			$sqls = array();

			foreach( $this->_categories1b3 as $categorie => $params ) {
				if( $params['table'] === 'dsps' ) {
					$sql = '( '.$this->_tableau1b3Categorie(
						$search,
						$categorie,
						$params['alias'],
						$params['column'],
						$params['values']
					).' )';
				}
				else {
					$sql = '( '.$this->_tableau1b3CategorieSubtable(
						$search,
						$categorie,
						$params['table'],
						$params['alias'],
						$params['column'],
						$params['values']
					).' )';
				}

				$sqls[] = preg_replace( '/\'.*\'::text AS "difficultes_exprimees"/', 'rendezvous.id AS rdv', $sql );
			}

			$sql = 'SELECT "ids"."rdv", "ids"."dsp", "ids"."dsp_rev", \''.$this->Tableausuivipdv93->id.'\', NOW(), NOW() FROM ( '
				.implode( ' UNION ', $sqls )
				.' ) AS "ids" GROUP BY "ids"."rdv", "ids"."dsp", "ids"."dsp_rev"';


			$Dbo = $this->Tableausuivipdv93->getDataSource();
			$table = $Dbo->fullTableName( $this->Tableausuivipdv93->Populationb3pdv93 );
			$sql = "INSERT INTO {$table} ( rendezvous_id, dsp_id, dsp_rev_id, tableausuivipdv93_id, created, modified ) {$sql};";

			return $sql;
		}

		/**
		 * Volet I problématiques 1-B-3: problématiques des bénéficiaires de
		 * l'opération.
		 *
		 * @param array $search
		 * @return array
		 */
		public function tableau1b3( array $search ) {
			$Dsp = ClassRegistry::init( 'Dsp' );
			$sqls = array();

			foreach( $this->_categories1b3 as $categorie => $params ) {
				if( $params['table'] === 'dsps' ) {
					$sqls[] = '( '.$this->_tableau1b3Categorie(
						$search,
						$categorie,
						$params['alias'],
						$params['column'],
						$params['values']
					).' )';
				}
				else {
					$sqls[] = '( '.$this->_tableau1b3CategorieSubtable(
						$search,
						$categorie,
						$params['table'],
						$params['alias'],
						$params['column'],
						$params['values']
					).' )';
				}
			}

			$sql = 'SELECT "difficultes_exprimees", COUNT(*) FROM ( '
				.implode( ' UNION ', $sqls )
				.' )  AS "liste_difficultes" GROUP BY "difficultes_exprimees";';

			$results = $Dsp->query( $sql );
			$results = Hash::combine( $results, '{n}.0.difficultes_exprimees', '{n}.0.count' );

			unset( $results[''] );
			$results['total'] = array_sum( array_values( $results ) );

			return $results;
		}

		/**
		 * Filtre sur un PDV ou sur l'ensemble du CG ?
		 * S'assure-ton qu'il existe au moins un RDV individuel ?
		 *
		 *
		 * @param array $search
		 * @param type $operand
		 * @return string
		 */
		protected function _conditionsFicheprescription93Rendezvous( array $search, $operand ) {
			// FIXME: vérifier que l'on obtienne bien la même chose
			$query = array(
				'fields' => array(
					'Rendezvous.personne_id'
				),
				'conditions' => array(
					// Avec un RDV honoré durant l'année N
					"EXTRACT('YEAR' FROM Rendezvous.daterdv)" => Hash::get( $search, 'Search.annee' ),
					// Dont le type de RDV est individuel
					'Rendezvous.typerdv_id' => (array)Configure::read( 'Tableausuivipdv93.typerdv_id' ),
					$this->_conditionStatutRdv( 'Rendezvous.statutrdv_id' ),
					// Dont la SR du référent de la fiche est la SR du RDV
					'Referent.structurereferente_id = Rendezvous.structurereferente_id'
				)
			);

			// FIXME
			// Filtre sur un PDV ou sur l'ensemble du CG ?
			$pdv_id = Hash::get( $search, 'Search.structurereferente_id' );
			if( !empty( $pdv_id ) ) {
				$query['conditions']['Referent.structurereferente_id'] = $pdv_id;
			}

			// S'assure-ton qu'il existe au moins un RDV individuel ?
			$rdv_structurereferente = Hash::get( $search, 'Search.rdv_structurereferente' );
			if( $rdv_structurereferente ) {
				$query['alias'] = 'Rendezvous';
				$query = array_words_replace( $query, array( 'Rendezvous' => 'rendezvous' ) );
				$sq = ClassRegistry::init( 'Rendezvous' )->sq( $query );
				return "{$operand} \"Ficheprescription93\".\"personne_id\" IN ( {$sq} )";
			}

			return null;
		}

		/**
		 * Retourne la query de base utillisé dans les tableaux 1B et 1B5, suivant
		 * le tableau.
		 *
		 * @param array $search
		 * @param string $tableau
		 * @return array
		 */
		protected function _qdTableau1b41b5( array $search, $tableau ) {
			// Début TODO factorisaton query de base
			$Ficheprescription93 = ClassRegistry::init( 'Ficheprescription93' );
			$Dbo = $this->Tableausuivipdv93->getDataSource();

			// Filtre sur l'année
			$annee = Sanitize::clean( Hash::get( $search, 'Search.annee' ), array( 'encode' => false ) );

			// Filtre sur un PDV ou sur l'ensemble du CG ?
			$conditionpdv = $this->_conditionpdv(
				$search,
				array(
					'structurereferente_id' => 'Referent.structurereferente_id',
					'referent_id' => 'Referent.id'
				)
			);

			// Filtre sur le type d'action
			$conditiontype = null;
			$typethematiquefp93_id = Hash::get( $search, 'Search.typethematiquefp93_id' );
			if( !empty( $typethematiquefp93_id ) ) {
				$conditiontype = $Dbo->conditions( array( 'Thematiquefp93.type' => $typethematiquefp93_id ), true, false );
			}

			// Filtre sur l'année de l'action
			$conditionyear = null;
			$yearthematiquefp93_id = Hash::get( $search, 'Search.yearthematiquefp93_id' );
			if( !empty( $yearthematiquefp93_id ) ) {
				$conditionyear = $Dbo->conditions( array( 'Thematiquefp93.yearthema' => $yearthematiquefp93_id ), true, false );
			}

			// Filtre sur le RDV individuel
			$conditionsrdv = $this->_conditionsFicheprescription93Rendezvous( $search, 'AND' );
			if( $conditionsrdv !== null ) {
				$conditionsrdv = preg_replace( '/^AND /', '', $conditionsrdv );
			}

			// Le query de base
			$query = array(
				'fields' => array(),
				'joins' => array(
					$Ficheprescription93->join( 'Actionfp93', array( 'type' => 'LEFT OUTER' ) ),
					$Ficheprescription93->join( 'Adresseprestatairefp93', array( 'type' => 'LEFT OUTER' ) ),
					$Ficheprescription93->join( 'Filierefp93', array( 'type' => 'INNER' ) ),
					$Ficheprescription93->join( 'Referent', array( 'type' => 'INNER' ) ),
					$Ficheprescription93->Adresseprestatairefp93->join( 'Prestatairefp93', array( 'type' => 'LEFT OUTER' ) ),
					$Ficheprescription93->Filierefp93->join( 'Categoriefp93', array( 'type' => 'INNER' ) ),
					$Ficheprescription93->Filierefp93->Categoriefp93->join( 'Thematiquefp93', array( 'type' => 'INNER' ) ),
				),
				'conditions' => array(
					'"Ficheprescription93"."statut" <>' => '99annulee',
					"EXTRACT( 'YEAR' FROM \"Ficheprescription93\".\"date_signature\" )" => $annee,
					$this->_conditionStructurereferenteIsPdv( 'Referent.structurereferente_id' ),
					$conditionpdv,
					$conditionsrdv,
					$conditiontype,
					$conditionyear
				),
				'contain' => false
			);

			// Ajout des conditions de base définies dans le webrsa.inc pour l'ensemble du tableau
			$conditions = (array)Configure::read( "Tableausuivi93.{$tableau}.conditions" );
			if( !empty( $conditions ) ) {
				$query['conditions'][] = $conditions;
			}

			return $query;
		}

		/**
		 * Retourne le query de base pour le tableau 1B4.
		 *
		 * @param array $search
		 * @return array
		 */
		public function qdTableau1b4( array $search ) {
			return $this->_qdTableau1b41b5( $search, 'tableau1b4' );
		}

		/**
		 * Retourne le query "de base" pour le tableau 1B5 (pour l'insertion dans
		 * la table de la population).
		 *
		 * @param array $search
		 * @return array
		 */
		public function qdTableau1b5( array $search ) {
			return $this->_qdTableau1b41b5( $search, 'tableau1b5' );
		}

		/**
		 * Tableau 1-B-4: prescriptions vers les acteurs sociaux,
		 * culturels et de sante.
		 *
		 * @param array $search
		 * @return array
		 */
		public function tableau1b4( array $search ) {
			$Ficheprescription93 = ClassRegistry::init( 'Ficheprescription93' );

			$base = $this->qdTableau1b4( $search );
			// Ajout des libellés des catégories et des thématiques
			$Dbo = $this->Tableausuivipdv93->getDataSource();
			$categories = $this->_tableau1b41b5Categories( 'tableau1b4', $search );

			$conditionsTotal = array( 'OR' => array() );
			$sqls = array();
			$counter = 0;
			foreach( $categories as $categorieName => $thematiques ) {
				$conditionsSousTotal = array( 'OR' => array() );

				foreach( $thematiques as $thematiqueName => $conditions ) {
					$categorieName = Sanitize::clean( $categorieName, array( 'encode' => false ) );
					$thematiqueName = Sanitize::clean( $thematiqueName, array( 'encode' => false ) );

					$conditionsSousTotal['OR'][] = $conditions;
					$conditionsTotal['OR'][] = $conditions;
					$conditions = $Dbo->conditions( $conditions, true, false );

					// 1 requête par ligne
					$query = $base;
					$query['fields'] = array(
						"'{$categorieName}' AS \"categorie\"",
						"'{$thematiqueName}' AS \"thematique\"",
						"{$counter} AS \"counter\"",
						'COUNT( "Ficheprescription93"."id" ) AS "nombre"',
						'COUNT( DISTINCT "Ficheprescription93"."personne_id" ) AS "nombre_unique"'
					);
					$query['conditions'][] = $conditions;

					$sqls[] = $Ficheprescription93->sq( $query );
					$counter++;
				}

				// requête pour le sous-total
				$query = $base;
				$query['fields'] = array(
					"'{$categorieName}' AS \"categorie\"",
					"'Sous-total' AS \"thematique\"",
					"{$counter} AS \"counter\"",
					'COUNT( Ficheprescription93.id ) AS "nombre"',
					'COUNT( DISTINCT Ficheprescription93.personne_id ) AS "nombre_unique"'
				);
				$query['conditions'][] = $conditionsSousTotal;

				$sqls[] = $Ficheprescription93->sq( $query );
				$counter++;
			}

			// requête pour le total
			$query = $base;
			$query['fields'] = array(
				"'Total' AS \"categorie\"",
				"NULL AS \"thematique\"",
				"{$counter} AS \"counter\"",
				'COUNT( Ficheprescription93.id ) AS "nombre"',
				'COUNT( DISTINCT Ficheprescription93.personne_id ) AS "nombre_unique"'
			);
			$query['conditions'][] = $conditionsTotal;

			// Ajout des conditions des différentes catégories
			$categories = $this->_tableau1b41b5Categories( 'tableau1b4', $search );
			if( !empty( $categories ) ) {
				$query['conditions'][] = array( 'OR' => Hash::extract( $categories, '{s}.{s}' ) );
			}
			else {
				$query['conditions'][] = '0 = 1';
			}

			$sqls[] = $Ficheprescription93->sq( $query );
			$counter++;

			// Requête complète
			$results = $Ficheprescription93->query( '( '.implode( $sqls, ' UNION ' ).' ) ORDER BY "counter" ASC;' );
			$results = Hash::remove( $results, '{n}.0.counter' );

			return $results;
		}

		/**
		 * Retourne le query contenant tous les champs pour l'export CSV du corpus
		 * du tableau 1B3 dont les résultats ont été enregistrés dans les modèles
		 * Tableausuivipdv93 et Populationb3pdv93.
		 *
		 * @param integer $id L'id de l'enregistrement du tableau 1B3 dans Tableausuivipdv93
		 * @return string
		 */
		public function qdExportcsvCorpus1b3( $id ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = array(
					'fields' => ConfigurableQueryFields::getModelsFields(
						array(
							$this->Tableausuivipdv93->Populationb3pdv93,
							$this->Tableausuivipdv93->Populationb3pdv93->Dsp,
							$this->Tableausuivipdv93->Populationb3pdv93->DspRev,
							$this->Tableausuivipdv93->Populationb3pdv93->Rendezvous,
							$this->Tableausuivipdv93->Populationb3pdv93->Rendezvous->Personne,
							$this->Tableausuivipdv93->Populationb3pdv93->Rendezvous->Structurereferente,
							$this->Tableausuivipdv93->Populationb3pdv93->Rendezvous->Referent
						)
					),
					'conditions' => array(
						// INFO: la condition sur l'id se trouvera plus bas à cause de la mise en cache
						'Tableausuivipdv93.name' => 'tableau1b3',
					),
					'joins' => array(
						$this->Tableausuivipdv93->join( 'Populationb3pdv93', array( 'type' => 'INNER' ) ),
						$this->Tableausuivipdv93->Populationb3pdv93->join( 'Dsp', array( 'type' => 'INNER' ) ),
						$this->Tableausuivipdv93->Populationb3pdv93->join( 'DspRev', array( 'type' => 'LEFT OUTER' ) ),
						// On s'arrange pour avoir le rendez-vous le plus récent de la population
						$this->Tableausuivipdv93->Populationb3pdv93->join(
							'Rendezvous',
								array(
								'type' => 'INNER',
								'conditions' => array(
									'Rendezvous.id IN (
										SELECT rendezvous.id
											FROM populationsb3pdvs93
												INNER JOIN rendezvous ON ( populationsb3pdvs93.rendezvous_id = rendezvous.id )
											WHERE rendezvous.personne_id = "Dsp"."personne_id"
											ORDER BY rendezvous.daterdv DESC, rendezvous.heurerdv DESC
											LIMIT 1

									)'
								)
							)
						),
						$this->Tableausuivipdv93->Populationb3pdv93->Rendezvous->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Tableausuivipdv93->Populationb3pdv93->Rendezvous->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$this->Tableausuivipdv93->Populationb3pdv93->Rendezvous->join( 'Referent', array( 'type' => 'INNER' ) )
					),
					'order' => array(
						'Personne.nom' => 'ASC',
						'Personne.prenom' => 'ASC'
					)
				);

				$query = $this->_completeQueryDonneesCaf( $query );

				foreach( $this->_categories1b3 as $categorie => $params ) {
					if( $params['table'] === 'dsps' ) {
						$query['fields']["Difficulte.{$categorie}"] = "(
							\"Dsp\".{$params['column']} IN ( '".implode( "', '", $params['values'] )."' )
							OR
							\"DspRev\".{$params['column']} IN ( '".implode( "', '", $params['values'] )."' )
						) AS \"Difficulte__{$categorie}\"";
					}
					else {
						$query['fields']["Difficulte.{$categorie}"] = "( EXISTS(
							SELECT *
							FROM {$params['table']} AS {$params['alias']}
							WHERE {$params['alias']}.dsp_id = \"Dsp\".\"id\"
							AND {$params['alias']}.{$params['column']} IN ( '".implode( "', '", $params['values'] )."' )
						)
						OR EXISTS(
							SELECT *
							FROM {$params['table']}_revs AS {$params['alias']}_revs
							WHERE {$params['alias']}_revs.dsp_rev_id = \"DspRev\".\"id\"
							AND {$params['alias']}_revs.{$params['column']} IN ( '".implode( "', '", $params['values'] )."' )
						) ) AS \"Difficulte__{$categorie}\"";
					}
				}

				Cache::write( $cacheKey, $query );
			}

			// Ajout de la condition sur l'id en-dehors de la partie mise en cache
			$query['conditions']['Tableausuivipdv93.id'] = $id;

			return $query;
		}

		/**
		 * Retourne le querydata nécessaire à l'export du corpus pris en compte
		 * dans les historiques de tableau 1B4 et 1B5 suivant le nom du tableau.
		 *
		 * @param string $tableau Le nom du tableau
		 * @return array
		 */
		protected function _qdExportcsvCorpus1b41b5( $tableau ) {
			$query = array(
				'fields' => ConfigurableQueryFields::getModelsFields(
					array(
						$this->Tableausuivipdv93->Populationb4b5pdv93,
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93,
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Actionfp93,
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Adresseprestatairefp93,
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Filierefp93,
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne,
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Referent,
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Adresseprestatairefp93->Prestatairefp93,
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Filierefp93->Categoriefp93,
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Filierefp93->Categoriefp93->Thematiquefp93,
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Referent->Structurereferente
					)
				),
				'conditions' => array(
					// INFO: la condition sur l'id se trouvera en-dehors à cause de la mise en cache
					'Tableausuivipdv93.name' => $tableau,
					'EXTRACT( \'YEAR\' FROM Ficheprescription93.date_signature ) = Tableausuivipdv93.annee'
				),
				'joins' => array(
					$this->Tableausuivipdv93->join( 'Populationb4b5pdv93', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationb4b5pdv93->join( 'Ficheprescription93', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->join( 'Actionfp93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->join( 'Adresseprestatairefp93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->join( 'Filierefp93', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->join( 'Referent', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Adresseprestatairefp93->join( 'Prestatairefp93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Filierefp93->join( 'Categoriefp93', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Filierefp93->Categoriefp93->join( 'Thematiquefp93', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) )
				),
				'order' => array(
					'Personne.nom' => 'ASC',
					'Personne.prenom' => 'ASC'
				)
			);

			$query = $this->_completeQueryDonneesCaf( $query );

			// Ajout des conditions communes concernant les exports CSV
			$query['conditions']['Ficheprescription93.statut <>'] = '99annulee';

			// Ajout des conditions de base définies dans le webrsa.inc pour l'ensemble du tableau
			$conditions = (array)Configure::read( "Tableausuivi93.{$tableau}.conditions" );
			if( !empty( $conditions ) ) {
				$query['conditions'][] = $conditions;
			}

			return $query;
		}

		/**
		 * Retourne le querydata nécessaire à l'export CSV du corpus pris en
		 * compte dans un historique de tableau 1B4.
		 *
		 * @param integer $id La clé primaire du tableau de suivi 1B4 historisé
		 * @return array
		 */
		public function qdExportcsvCorpus1b4( $id ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->_qdExportcsvCorpus1b41b5( 'tableau1b4' );

				Cache::write( $cacheKey, $query );
			}

			// Ajout de la condition sur l'id en-dehors de la partie mise en cache
			$query['conditions']['Tableausuivipdv93.id'] = $id;

			return $query;
		}

		/**
		 * Fournit la vérification des morceaux de querydata définis dans le
		 * webrsa.inc pour les clés Tableausuivi93.tableau1b4 et
		 * Tableausuivi93.tableau1b5.
		 *
		 * @return array
		 */
		public function querydataFragmentsErrors() {
			$return = array();

			// Tableausuivi93.tableau1b4 et Tableausuivi93.tableau1b5 (conditions et categories)
			foreach( array( 'tableau1b4', 'tableau1b5' ) as $name ) {
				$search = array(
					'Search' => array(
						'annee' => '2009',
						'structurereferente_id' => ''
					)
				);
				try {
					$method = "{$name}";
					@$this->{$method}( $search );
					$message = null;
				} catch ( Exception $e ) {
					$message = $e->getMessage();
				}
				$return["Tableausuivi93.{$name}"] = array(
					'success' => is_null( $message ),
					'message' => $message
				);
			}

			// Pour chacune des catégories du tableau 1B4 et 1B5, on vérifie que les conditions correspondent à du paramétrage
			$Dbo = $this->Tableausuivipdv93->getDataSource();

			$Thematiquefp93 = $this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Filierefp93->Categoriefp93->Thematiquefp93;
			$base = array(
				'fields' => array( 'Thematiquefp93.id' ),
				'joins' => array(
					$Thematiquefp93->join( 'Categoriefp93', array( 'type' => 'INNER' ) ),
					$Thematiquefp93->Categoriefp93->join( 'Filierefp93', array( 'type' => 'INNER' ) )
				),
				'contain' => false
			);

			foreach( array( 'tableau1b4', 'tableau1b5' ) as $tableau ) {
				$expected = (array)Configure::read( "Tableausuivi93.{$tableau}.categories" );
				$found = $this->_tableau1b41b5Categories( $tableau, array() );

				foreach( array_keys( $expected ) as $thematique ) {
					foreach( array_keys( $expected[$thematique] ) as $categorie ) {
						if( !isset( $found[$thematique][$categorie] ) ) {
							$conditions = $Dbo->conditions( $expected[$thematique][$categorie], true, false );

							$query = $base;
							$query['conditions'] = $conditions;
							$sql = str_replace( '"Thematiquefp93"."id" AS "Thematiquefp93__id"', '*', $Thematiquefp93->sq( $query ) );

							$return["Tableausuivi93.{$tableau}.categories.{$thematique}.{$categorie}"] = array(
								'success' => false,
								'message' => sprintf( 'Aucun paramétrage trouvé pour les conditions %s, à vérifier avec la requête suivante: %s', $conditions, $sql )
							);
						}
					}
				}
			}

			return $return;
		}

		/**
		 * Vérification que les champs spécifiés dans le paramétrage par les clés
		 * Tableauxsuivispdvs93.<tableau>.exportcsvcorpus dans le webrsa.inc
		 * existent bien dans la requête d'export des corpus, où <tableau> peut
		 * valoir tableau1b3, tableau1b4, tableau1b5 ou tableau1b6.
		 *
		 * @param array $params Paramètres supplémentaires (clé 'query' possible)
		 * @return array
		 * @todo Utiliser AbstractWebrsaRecherche
		 */
		public function checkParametrage( array $params = array() ) {
			$return = array();

			foreach( array_keys( $this->tableaux ) as $tableau ) {
				if( !in_array( $tableau, array( 'tableaud1', 'tableaud2' ) ) ) {
					$keys = array( "Tableauxsuivispdvs93.{$tableau}.exportcsvcorpus" );
					$method = "qdExportcsvCorpus".str_replace( 'tableau', '', $tableau );
					$query = $this->{$method}( null );

					$return = Hash::merge( $return, ConfigurableQueryFields::getErrors( $keys, $query ) );
				}
			}

			return $return;
		}

		/**
		 * Tableau 1-B-5: prescription sur les actions à caractère socio-professionnel
		 * et professionnel.
		 *
		 * Partie "Tableaux périphériques".
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableau1b5Totaux( array $search ) {
			$Ficheprescription93 = ClassRegistry::init( 'Ficheprescription93' );
			$query = $this->_qdTableau1b41b5( $search, 'tableau1b5' );

			// Ajout des conditions des différentes catégories
			$categories = $this->_tableau1b41b5Categories( 'tableau1b5', $search );
			if( !empty( $categories ) ) {
				$query['conditions'][] = array( 'OR' => Hash::extract( $categories, '{s}.{s}' ) );
			}
			else {
				$query['conditions'][] = '0 = 1';
			}

			// Ajout des champs spécifiques à cette requête
			$query['fields'] = array(
				'COUNT( DISTINCT "Ficheprescription93"."personne_id" ) AS "distinct_personnes_prescription"',
				// "Suivi de l'action", on a L'allocataire a intégré l'action=oui.
				'COUNT( DISTINCT ( CASE WHEN "Ficheprescription93"."personne_a_integre" = \'1\' THEN "Ficheprescription93"."personne_id" ELSE NULL END ) ) AS "distinct_personnes_action"',
				// H. Cadre effectivité : La personne s'est présentée=non ou s'est excusée
				'COALESCE( SUM( CASE WHEN "Ficheprescription93"."benef_retour_presente" IN ( \'non\', \'excuse\' ) THEN 1 ELSE 0 END ), 0 ) AS "beneficiaires_pas_deplaces"',
				// I. Cadre effectivité : "Signé par le partenaire le"=vide et La personne s'est présentée=vide ou =oui.
				'COALESCE( SUM( CASE WHEN ( "Ficheprescription93"."benef_retour_presente" IS NULL OR "Ficheprescription93"."benef_retour_presente" = \'oui\' ) THEN 1 ELSE 0 END ), 0 ) AS "nombre_fiches_attente"',
			);

			$results = $Ficheprescription93->find( 'all', $query );
			return $results;
		}

		/**
		 * Retourne les catégories définies dans le webrsa.inc, sous la clé
		 * "Tableausuivi93.tableau1b5.categories", si et seulement si le paramétrage
		 * (modulo la valeur de Search.typethematiquefp93_id passé dans le paramètre
		 * $search) permet de retrouver ces informations dans les tables
		 * thematiquesfps93, categoriesfps93 et filieresfps93.
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableau1b41b5Categories( $tableau, array $search ) {
			if( !in_array( $tableau, array( 'tableau1b4', 'tableau1b5' ) ) ) {
				$msgstr = 'Mauvais paramètre passé pour le tableau: %s';
				throw new RuntimeException( sprintf( $msgstr, $tableau ) );
			}

			$attribute = ( $tableau === 'tableau1b4' ? '_categories1b4' : '_categories1b5' );
			$modelName = ( $tableau === 'tableau1b4' ? 'Tableau1b4' : 'Tableau1b5' );

			if( $this->{$attribute} === null ) {
				$categories = (array)Configure::read( "Tableausuivi93.{$tableau}.categories" );

				$typethematiquefp93_id = Hash::get( $search, 'Search.typethematiquefp93_id' );
				$yearthematiquefp93_id = Hash::get( $search, 'Search.yearthematiquefp93_id' );

				$Thematiquefp93 = ClassRegistry::init( 'Thematiquefp93' );
				$Dbo = $this->Tableausuivipdv93->getDataSource();

				$base = array(
					'fields' => array(
						'Thematiquefp93.id'
					),
					'joins' => array(
						$Thematiquefp93->join( 'Categoriefp93', array( 'type' => 'INNER' ) ),
						$Thematiquefp93->Categoriefp93->join( 'Filierefp93', array( 'type' => 'INNER' ) ),
					),
					'contain' => false
				);

				$unions = array();

				foreach( array_keys( $categories ) as $thematiqueName ) {
					foreach( array_keys( $categories[$thematiqueName] ) as $categorieName ) {
						$conditions = $categories[$thematiqueName][$categorieName];
						$query = $base;

						$query['conditions'] = $conditions;

						// Ajout de condition si nécessaire
						if( $typethematiquefp93_id !== null ) {
							$query['conditions']['Thematiquefp93.type'] = $typethematiquefp93_id;
						}

						// Ajout de condition si nécessaire
						if( $yearthematiquefp93_id !== null ) {
							$query['conditions']['Thematiquefp93.yearthema'] = $yearthematiquefp93_id;
						}

						$sql = $Thematiquefp93->sq( $query );
						$thematique = Sanitize::clean( $thematiqueName, array( 'encode' => false ) );
						$categorie = Sanitize::clean( $categorieName, array( 'encode' => false ) );

						$sql = "SELECT '{$thematique}' AS \"{$modelName}__thematique\", '{$categorie}' AS \"{$modelName}__categorie\", EXISTS( {$sql} ) AS \"{$modelName}__exists\"";
						$unions[] = $sql;
					}
				}
				$results = $Dbo->query( implode( ' UNION ', $unions ) );
				foreach( $results as $result ) {
					if( empty( $result[$modelName]['exists'] ) ) {
						unset( $categories[$result[$modelName]['thematique']][$result[$modelName]['categorie']] );
						if( empty( $categories[$result[$modelName]['thematique']] ) ) {
							unset( $categories[$result[$modelName]['thematique']] );
						}
					}
				}
				$this->{$attribute} = $categories;
			}

			return $this->{$attribute};
		}

		/**
		 * Tableau 1-B-5: prescription sur les actions à caractère socio-professionnel
		 * et professionnel.
		 *
		 * Partie "Totaux" (les deux tableaux périphériques).
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _tableau1b5Results( array $search ) {
			$Ficheprescription93 = ClassRegistry::init( 'Ficheprescription93' );
			$base = $this->_qdTableau1b41b5( $search, 'tableau1b5' );
			// Ajout des libellés des catégories et des thématiques
			$Dbo = $this->Tableausuivipdv93->getDataSource();

			$categories = $this->_tableau1b41b5Categories( 'tableau1b5', $search );

			$vFields = array(
				// A. nombre total de fiches quel que soit le statut renseigné
				'COUNT( Ficheprescription93.id ) AS "nombre"',
				// B. Cadre "Effectivité de la prescription": Nombre de fiches pour lesquelles l'allocataire s'est présenté ="oui" et date de signature du partenaire est renseignée
				'COALESCE( SUM( CASE WHEN "Ficheprescription93"."benef_retour_presente" = \'oui\' AND "Ficheprescription93"."date_signature_partenaire" IS NOT NULL THEN 1 ELSE 0 END ), 0 ) AS "nombre_effectives"',
				// C. Cadre "Suivi de l'action" : "La personne souhaite intégrer l'action=non"
				//'COALESCE( SUM( CASE WHEN "Ficheprescription93"."personne_souhaite_integrer" = \'0\' THEN 1 ELSE 0 END ), 0 ) AS "nombre_refus_beneficiaire"',
				// D. Cadre "Suivi de l'action" : "La personne a été retenue par la structure=non"
				'COALESCE( SUM( CASE WHEN "Ficheprescription93"."personne_retenue" = \'0\' THEN 1 ELSE 0 END ), 0 ) AS "nombre_refus_organisme"',
				// E. Cadre "Suivi de l'action" : La personne a été reçue en entretien=oui La personne a été retenue par la structure:oui La personne souhaite intégrer l'action:oui L'allocataire a intégré l'action= vide Avec la date du jour antérieure à la date de début de l'action si elle existe
				'COALESCE( SUM(
					CASE
						WHEN (
							"Ficheprescription93"."personne_retenue" = \'1\'
							AND "Ficheprescription93"."personne_a_integre" IS NULL
							AND (
								"Ficheprescription93"."dd_action" IS NULL
								OR "Ficheprescription93"."dd_action" > NOW()
							)
						) THEN 1
						ELSE 0
					END
				), 0 ) AS "nombre_en_attente"',
				// F. Cadre "Suivi de l'action": L'allocataire a intégré l'action=oui
				'COUNT( DISTINCT ( CASE WHEN "Ficheprescription93"."personne_a_integre" = \'1\' THEN "Ficheprescription93"."id" ELSE NULL END ) ) AS "nombre_participations"'
			);

			$conditionsTotal = array( 'OR' => array() );
			$sqls = array();
			$counter = 0;
			foreach( $categories as $categorieName => $thematiques ) {
				$conditionsSousTotal = array( 'OR' => array() );

				foreach( $thematiques as $thematiqueName => $conditions ) {
					$categorieName = Sanitize::clean( $categorieName, array( 'encode' => false ) );
					$thematiqueName = Sanitize::clean( $thematiqueName, array( 'encode' => false ) );

					$conditionsSousTotal['OR'][] = $conditions;
					$conditionsTotal['OR'][] = $conditions;
					$conditions = $Dbo->conditions( $conditions, true, false );

					// requête par ligne
					$query = $base;
					$query['fields'] = array_merge(
						array(
							"'{$categorieName}' AS \"categorie\"",
							"'{$thematiqueName}' AS \"thematique\"",
							"{$counter} AS \"counter\""
						),
						$vFields
					);
					$query['conditions'][] = $conditions;

					$sqls[] = $Ficheprescription93->sq( $query );
					$counter++;
				}

				// requête pour le sous-total
				$query = $base;
				$query['fields'] = array_merge(
					array(
						"'{$categorieName}' AS \"categorie\"",
						"'Sous-total' AS \"thematique\"",
						"{$counter} AS \"counter\"",
					),
					$vFields
				);
				$query['conditions'][] = $conditionsSousTotal;

				$sqls[] = $Ficheprescription93->sq( $query );
				$counter++;
			}
			// requête pour le total
			$query = $base;
			$query['fields'] = array_merge(
				array(
					"'Total' AS \"categorie\"",
					"NULL AS \"thematique\"",
					"{$counter} AS \"counter\"",
				),
				$vFields
			);
			$query['conditions'][] = $conditionsTotal;

			$sqls[] = $Ficheprescription93->sq( $query );
			$counter++;

			// Requête complète
			$results = $Ficheprescription93->query( '( '.implode( $sqls, ' UNION ' ).' ) ORDER BY "counter" ASC;' );
			$results = Hash::remove( $results, '{n}.0.counter' );

			return $results;
		}

		/**
		 * Tableau 1-B-5: prescription sur les actions à caractère socio-professionnel
		 * et professionnel.
		 *
		 * @param array $search
		 * @return array
		 */
		public function tableau1b5( array $search ) {
			return array(
				'results' => $this->_tableau1b5Results( $search ),
				'totaux' => $this->_tableau1b5Totaux( $search )
			);
		}

		/**
		 * Retourne le querydata nécessaire à l'export CSV du corpus pris en
		 * compte dans un historique de tableau 1B5.
		 *
		 * @param integer $id La clé primaire du tableau de suivi 1B5 historisé
		 * @return array
		 */
		public function qdExportcsvCorpus1b5( $id ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->_qdExportcsvCorpus1b41b5( 'tableau1b5' );

				Cache::write( $cacheKey, $query );
			}

			// Ajout de la condition sur l'id en-dehors de la partie mise en cache
			$query['conditions']['Tableausuivipdv93.id'] = $id;

			// Ajout de champs se trouvant dans les tableaux de résultats
			$query['fields']['Ficheprescription93.personne_a_integre'] = '( CASE WHEN "Ficheprescription93"."personne_a_integre" = \'1\' THEN \'Oui\' ELSE NULL END ) AS "Ficheprescription93__personne_a_integre"';
			$query['fields']['Ficheprescription93.personne_pas_deplace'] = '( CASE WHEN "Ficheprescription93"."benef_retour_presente" IN ( \'non\', \'excuse\' ) THEN \'Oui\' ELSE NULL END ) AS "Ficheprescription93__personne_pas_deplace"';
			$query['fields']['Ficheprescription93.en_attente'] = '( CASE WHEN ( "Ficheprescription93"."date_signature_partenaire" IS NULL ) AND ( "Ficheprescription93"."benef_retour_presente" IS NULL OR "Ficheprescription93"."benef_retour_presente" = \'oui\' ) THEN \'Oui\' ELSE NULL END ) AS "Ficheprescription93__en_attente"';

			return $query;
		}

		/**
		 * Complète un querydata d'export du corpus avec les champs et les
		 * jointures (à partir du modèle Personne) pour les modèles Foyer,
		 * Prestation, Dossier, Adressefoyer et Adresse.
		 *
		 * @param array $query Le querydata à compléter
		 * @return array
		 */
		protected function _completeQueryDonneesCaf( array $query ) {
			// Ajout des champs
			$query['fields'] += ConfigurableQueryFields::getModelsFields(
				array(
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne->Foyer,
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne->Prestation,
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne->Foyer->Dossier,
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne->Foyer->Adressefoyer,
					$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne->Foyer->Adressefoyer->Adresse
				)
			);

			// Ajout des jointures
			$query['joins'][] = $this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne->join( 'Foyer', array( 'type' => 'INNER' ) );
			$query['joins'][] = $this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne->join( 'Prestation', array( 'type' => 'INNER' ) );
			$query['joins'][] = $this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne->Foyer->join(
				'Adressefoyer',
				array(
					'type' => 'INNER',
					'conditions' => array(
						'Adressefoyer.id IN( '.$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
					)
				)
			);
			$query['joins'][] = $this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) );
			$query['joins'][] = $this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) );

			return $query;
		}

		/**
		 * Retourne le querydata nécessaire à l'export CSV du corpus pris en
		 * compte dans un historique de tableau 1B6.
		 *
		 * @param integer $id La clé primaire du tableau de suivi 1B6 historisé
		 * @return array
		 */
		public function qdExportcsvCorpus1b6( $id ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = array(
					'fields' => ConfigurableQueryFields::getModelsFields(
						array(
							$this->Tableausuivipdv93->Populationb6pdv93,
							$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous,
							$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->Personne,
							$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->Referent,
							$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->RendezvousThematiquerdv,
							$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->RendezvousThematiquerdv->Thematiquerdv,
							$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->Structurereferente,
							$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->Typerdv,
							$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->Statutrdv
						)
					),
					'conditions' => array(
						'Tableausuivipdv93.name' => 'tableau1b6'
					),
					'joins' => array(
						$this->Tableausuivipdv93->join( 'Populationb6pdv93', array( 'type' => 'INNER' ) ),
						$this->Tableausuivipdv93->Populationb6pdv93->join( 'Rendezvous', array( 'type' => 'INNER' ) ),
						$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->join( 'RendezvousThematiquerdv', array( 'type' => 'INNER' ) ),
						$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->RendezvousThematiquerdv->join( 'Thematiquerdv', array( 'type' => 'INNER' ) ),
						$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->join( 'Typerdv', array( 'type' => 'INNER' ) ),
						$this->Tableausuivipdv93->Populationb6pdv93->Rendezvous->join( 'Statutrdv', array( 'type' => 'INNER' ) ),
					),
					'order' => array(
						'Personne.nom' => 'ASC',
						'Personne.prenom' => 'ASC'
					)
				);

				$query = $this->_completeQueryDonneesCaf( $query );

				Cache::write( $cacheKey, $query );
			}

			$query['conditions']['Tableausuivipdv93.id'] = $id;

			return $query;
		}

		/**
		 * Retourne les enregistrements des thématiques de rendez-vous à prendre
		 * en compte dans le tableau 1B6, le modèle est aliasé par Tableau1b6.
		 *
		 * @return array
		 */
		protected function _tableau1b6Thematiquesrdvs() {
			$Thematiquerdv = ClassRegistry::init( array( 'class' => 'Thematiquerdv', 'alias' => 'Tableau1b6' ) );

			$cases = array();
			foreach( (array)Configure::read( 'Tableausuivipdv93.Tableau1b6.map_thematiques_themes' ) as $thematique_id => $theme ) {
				$cases[] = "WHEN id = {$thematique_id} THEN '{$theme}'";
			}

			$results = $Thematiquerdv->find(
				'all',
				array(
					'fields' => array(
						'Tableau1b6.id',
						'Tableau1b6.name',
						'( CASE WHEN false THEN NULL '.implode( '', $cases ).' ELSE NULL END ) AS "Tableau1b6__theme"'
					),
					'contain' => false,
					'conditions' => array(
						'Tableau1b6.typerdv_id' => (array)Configure::read( 'Tableausuivipdv93.Tableau1b6.typerdv_id' )
					),
					'order' => array( 'Tableau1b6.name ASC' )
				)
			);

			return $results;
		}

		/**
		 * Tableau 1-B-6: Actions collectives
		 *
		 * @param array $search
		 * @return array
		 */
		public function tableau1b6( array $search ) {
			$Thematiquerdv = ClassRegistry::init( array( 'class' => 'Thematiquerdv', 'alias' => 'Tableau1b6' ) );
			$Dbo = $this->Tableausuivipdv93->getDataSource();

			$results = $this->_tableau1b6Thematiquesrdvs();

			// Filtre sur l'année
			$annee = Sanitize::clean( Hash::get( $search, 'Search.annee' ), array( 'encode' => false ) );

			$conditionpdv = $this->_conditionpdv(
				$search,
				array(
					'structurereferente_id' => 'rendezvous.structurereferente_id',
					'referent_id' => 'rendezvous.referent_id'
				),
				true
			);

			// S'assure-ton qu'il existe au moins un RDV individuel ?
			$conditionrdv = null;
			$rdv_structurereferente = Hash::get( $search, 'Search.rdv_structurereferente' );
			if( $rdv_structurereferente ) {
				$conditionrdv = "AND rendezvous.personne_id IN (
					SELECT DISTINCT rdvindividuelhonore.personne_id
						FROM rendezvous AS rdvindividuelhonore
					WHERE
						-- avec un RDV honoré durant l'année N
						EXTRACT('YEAR' FROM rdvindividuelhonore.daterdv) = '{$annee}'
						-- Dont le type de RDV est individuel
						AND rdvindividuelhonore.typerdv_id IN ( ".implode( ',', (array)Configure::read( 'Tableausuivipdv93.typerdv_id' ) )." )
						AND rdvindividuelhonore.".$this->_conditionStatutRdv()."
						-- dont la SR du rendez-vous collectif est la même que celle du RDV individuel
						AND rendezvous.structurereferente_id = rdvindividuelhonore.structurereferente_id
						{$conditionpdv}
				)";
			}
			// Fin TODO factoriser

			// Liste des thématiques collectives
			$thematiquesrdvs_ids = (array)Hash::extract( $results, '{n}.Tableau1b6.id' );
			if( empty( $thematiquesrdvs_ids ) ) {
				$thematiquesrdvs_ids = array( 0 );
			}

			// --1-- Nbre de personnes invitées ou positionnées : honoré ou prévu
			$sql = "SELECT
							thematiquesrdvs.name AS \"Tableau1b6__name\",
							COUNT(DISTINCT rendezvous.personne_id) AS \"Tableau1b6__count_personnes_prevues\",
							COUNT(DISTINCT rendezvous.id) AS \"Tableau1b6__count_invitations\"
						FROM rendezvous
							INNER JOIN typesrdv ON ( typesrdv.id = rendezvous.typerdv_id )
							INNER JOIN rendezvous_thematiquesrdvs ON ( rendezvous.id = rendezvous_thematiquesrdvs.rendezvous_id )
							INNER JOIN thematiquesrdvs ON ( thematiquesrdvs.id = rendezvous_thematiquesrdvs.thematiquerdv_id )
						WHERE
							rendezvous_thematiquesrdvs.thematiquerdv_id IN ( ".implode( ',', $thematiquesrdvs_ids )." )
							AND EXTRACT( 'YEAR' FROM rendezvous.daterdv ) = '{$annee}'
							{$conditionpdv}
							{$conditionrdv}
						GROUP BY
							thematiquesrdvs.name,
							rendezvous_thematiquesrdvs.thematiquerdv_id";
			$results1 = $Thematiquerdv->query( $sql );

			$sql = "SELECT
							thematiquesrdvs.name AS \"Tableau1b6__name\",
							COUNT(DISTINCT rendezvous.daterdv||' '||rendezvous.heurerdv) AS \"Tableau1b6__count_seances\",
                            COUNT(DISTINCT rendezvous.personne_id) AS \"Tableau1b6__count_personnes\",
							COUNT(DISTINCT rendezvous.id) AS \"Tableau1b6__count_participations\"
						FROM rendezvous
							INNER JOIN typesrdv ON ( typesrdv.id = rendezvous.typerdv_id )
							INNER JOIN rendezvous_thematiquesrdvs ON ( rendezvous.id = rendezvous_thematiquesrdvs.rendezvous_id )
							INNER JOIN thematiquesrdvs ON ( thematiquesrdvs.id = rendezvous_thematiquesrdvs.thematiquerdv_id )
						WHERE
							rendezvous_thematiquesrdvs.thematiquerdv_id IN ( ".implode( ',', $thematiquesrdvs_ids )." )
							AND EXTRACT('YEAR' FROM rendezvous.daterdv) = '{$annee}'
							AND ".$this->_conditionStatutRdv( 'rendezvous.statutrdv_id' )."
							{$conditionpdv}
							{$conditionrdv}
						GROUP BY
							thematiquesrdvs.name,
							rendezvous_thematiquesrdvs.thematiquerdv_id";
			$results2 = $Thematiquerdv->query( $sql );

			// Formattage des résultats
			foreach( $results as $key => $result ) {
				$name = $result['Tableau1b6']['name'];
				foreach( $results1 as $result1 ) {
					foreach( array( 'count_personnes_prevues', 'count_invitations' ) as $field ) {
						if( $result1['Tableau1b6']['name'] == $name ) {
							$value = (int) Hash::get( $result1, "Tableau1b6.{$field}" );
							if( !isset( $results[$key]['Tableau1b6'][$field] ) ) {
								$results[$key]['Tableau1b6'][$field] = 0;
							}
							$results[$key]['Tableau1b6'][$field] += $value;
						}
						else {
							if( !isset( $results[$key]['Tableau1b6'][$field] ) ) {
								$results[$key]['Tableau1b6'][$field] = 0;
							}
						}
					}
				}
				foreach( $results2 as $result2 ) {
					foreach( array( 'count_seances', 'count_personnes', 'count_participations' ) as $field ) {
						if( $result2['Tableau1b6']['name'] == $name ) {
							$value = (int)Hash::get( $result2, "Tableau1b6.{$field}" );
							if( !isset( $results[$key]['Tableau1b6'][$field] ) ) {
								$results[$key]['Tableau1b6'][$field] = 0;
							}
							$results[$key]['Tableau1b6'][$field] += $value;
						}
						else {
							if( !isset( $results[$key]['Tableau1b6'][$field] ) ) {
								$results[$key]['Tableau1b6'][$field] = 0;
							}
						}
					}
				}
			}

			return $results;
		}

		/**
		 * Querydata permettant de récupérer les rendez-vous du tableau 1B6 pour
		 * les enregistrer dans la table de la population 1B6.
		 *
		 * @param array $search
		 * @return array
		 */
		public function qdTableau1b6( array $search ) {
			$Rendezvous = ClassRegistry::init( 'Rendezvous' );
			$Dbo = $this->Tableausuivipdv93->getDataSource();

			// Filtre sur l'année
			$annee = Sanitize::clean( Hash::get( $search, 'Search.annee' ), array( 'encode' => false ) );
			$conditionpdv = $this->_conditionpdv(
				$search,
				array(
					'structurereferente_id' => 'Rendezvous.structurereferente_id',
					'referent_id' => 'Rendezvous.referent_id'
				)
			);

			// S'assure-t-on qu'il existe au moins un RDV individuel ?
			$conditionrdv = null;
			$rdv_structurereferente = Hash::get( $search, 'Search.rdv_structurereferente' );
			if( $rdv_structurereferente ) {
				$conditionrdv = "Rendezvous.personne_id IN (
					SELECT DISTINCT rdvindividuelhonore.personne_id
						FROM rendezvous AS rdvindividuelhonore
					WHERE
						-- avec un RDV honoré durant l'année N
						EXTRACT('YEAR' FROM rdvindividuelhonore.daterdv) = '{$annee}'
						-- Dont le type de RDV est individuel
						AND rdvindividuelhonore.typerdv_id IN ( ".implode( ',', (array)Configure::read( 'Tableausuivipdv93.typerdv_id' ) )." )
						AND rdvindividuelhonore.".$this->_conditionStatutRdv()."
						-- dont la SR du rendez-vous collectif est la même que celle du RDV individuel
						AND \"Rendezvous\".\"structurereferente_id\" = rdvindividuelhonore.structurereferente_id
						".( empty( $conditionpdv ) ? null : "AND {$conditionpdv}" )."
				)";
			}

			$thematiquesrdvs_ids = Hash::extract( $this->_tableau1b6Thematiquesrdvs(), '{n}.Tableau1b6.id' );

			$query = array(
				'fields' => array_merge(
					$Rendezvous->fields(),
					$Rendezvous->Personne->fields(),
					$Rendezvous->Referent->fields(),
					$Rendezvous->RendezvousThematiquerdv->fields(),
					$Rendezvous->Structurereferente->fields(),
					$Rendezvous->Typerdv->fields(),
					$Rendezvous->RendezvousThematiquerdv->Thematiquerdv->fields(),
					array( $Rendezvous->Referent->sqVirtualField( 'nom_complet' ) )
				),
				'joins' => array(
					$Rendezvous->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Rendezvous->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$Rendezvous->join( 'RendezvousThematiquerdv', array( 'type' => 'INNER' ) ),
					$Rendezvous->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
					$Rendezvous->join( 'Typerdv', array( 'type' => 'INNER' ) ),
					$Rendezvous->RendezvousThematiquerdv->join( 'Thematiquerdv', array( 'type' => 'INNER' ) ),
				),
				'conditions' => array(
					'RendezvousThematiquerdv.thematiquerdv_id' => $thematiquesrdvs_ids,
					"EXTRACT( 'YEAR' FROM \"Rendezvous\".\"daterdv\" )" => $annee,
					$conditionpdv,
					$conditionrdv
				),
				'contain' => false
			);

			return $query;
		}

		/**
		 * Retourne une liste ordonnée et traduite.
		 *
		 * @param string $type
		 * @param string $tableauName
		 * @return array
		 */
		protected function _listes( $type, $tableauName ) {
			$options = array();
			$domain = Inflector::tableize( $this->Tableausuivipdv93->name );

			foreach( $this->{$type} as $intitule ) {
				$options[$intitule] = __d( $domain, "{$tableauName}.{$intitule}" );
			}

			return $options;
		}

		/**
		 * Retourne la liste des problématiques, ordonnées et traduites.
		 *
		 * @return array
		 */
		public function problematiques() {
			return $this->_listes( 'problematiques', 'Tableau1b3' );
		}

		/**
		 * Retourne la liste des types d'acteurs, ordonnées et traduites.
		 *
		 * @return array
		 */
		public function acteurs() {
			return $this->_listes( 'acteurs', 'Tableau1b4' );
		}

		/**
		 * Historisation de critères de recherches et de leurs résultats.
		 *
		 * @param string $action
		 * @param array $search
		 * @param integer $user_id
		 * @return boolean
		 */
		public function historiser( $action, $search, $user_id = null ) {
			$results = $this->{$action}( $search );

			$tableausuivipdv93 = array(
				'Tableausuivipdv93' => array(
					'name' => $action,
					'annee' => Hash::get( $search, 'Search.annee' ),
					'type' => Hash::get( $search, 'Search.type' ),
					'communautesr_id' => Hash::get( $search, 'Search.communautesr_id' ),
					'structurereferente_id' => Hash::get( $search, 'Search.structurereferente_id' ),
					'referent_id' => suffix( Hash::get( $search, 'Search.referent_id' ) ),
					'version' => app_version(),
					'search' => serialize( $search ),
					'results' => serialize( $results ),
					'user_id' => $user_id
				)
			);

			if( $tableausuivipdv93['Tableausuivipdv93']['type'] === 'interne' ) {
				$tableausuivipdv93 = Hash::insert(
					$tableausuivipdv93,
					'Structurereferente.Structurereferente',
					(array)Hash::get( $search, 'Search.Structurereferente.Structurereferente' )
				);
			}

			// On sauvegarde au maximum une fois par jour les mêmes requêtes et résultats
			$conditions = Hash::flatten(
				array( 'Tableausuivipdv93' => $tableausuivipdv93['Tableausuivipdv93'] )
			);
			$conditions["DATE_TRUNC( 'day', \"Tableausuivipdv93\".\"modified\" )"] = date( 'Y-m-d' );

			// A-t'on déjà sauvegardé exactement ce résultat ?
			$found = $this->Tableausuivipdv93->find( 'first', array( 'conditions' => $conditions ) );

			$success = true;

			// Si c'est le cas, on se contente de le réenregistrer pour que la date de modifcation soit mise à jour
			if( !empty( $found ) ) {
				$tableausuivipdv93 = Hash::merge( $tableausuivipdv93, $found );
				unset(
					$tableausuivipdv93['Tableausuivipdv93']['created'],
					$tableausuivipdv93['Tableausuivipdv93']['modified']
				);
			}

			$this->Tableausuivipdv93->create( $tableausuivipdv93 );
			$success = $this->Tableausuivipdv93->save( null, array( 'atomic' => false ) ) && $success;

			if( $success && empty( $found ) ) {
				// Sauvegarde complète du corpus
				if( $success ) {
					$query = $this->queryCorpus($action, $search);
					$modelClass = $this->modelsCorpus[$action];

					$models = array( $modelClass => null ) + Hash::normalize(Hash::extract( $query, 'joins.{n}.alias' ));
					foreach( array_keys( $models ) as $model ) {
						$models[$model] = ClassRegistry::init( $model )->schema();
					}

					// Pour les tableaux D1 et D2 sur Plaine Commune, on ajoute un champ virtuel (Demenagement.interne)
					// s'il existe un D1 précédent le D1 comptabilisé, la même année, sur une autre structure de Plaine Co ?
					if( in_array( $action, array( 'tableaud1', 'tableaud2' ) ) && 'communaute' === Hash::get( $search, 'Search.type' ) ) {
						$communautesr_id = Hash::get( $search, 'Search.communautesr_id' );
						$sqlCommunautesr = $this->Tableausuivipdv93->Communautesr->sqStructuresreferentes( $communautesr_id );

						$Rendezvous = ClassRegistry::init( 'Rendezvous' );
						$sqRendezvous = array(
							'alias' => 'rendezvouspcd',
							'fields' => array( 'rendezvouspcd.structurereferente_id' ),
							'contain' => false,
							'joins' => array(
								array_words_replace(
									$Rendezvous->join( 'Questionnaired1pdv93', array( 'type' => 'INNER' ) ),
									array( 'Questionnaired1pdv93' => 'questionnairesd1pdvs93pcd', 'Rendezvous' => 'rendezvouspcd' )
								)
							),
							'conditions' => array(
								'rendezvouspcd.personne_id = Rendezvous.personne_id',
								'EXTRACT( \'YEAR\' FROM questionnairesd1pdvs93pcd.date_validation ) = EXTRACT( \'YEAR\' FROM Questionnaired1pdv93.date_validation )',
								'questionnairesd1pdvs93pcd.date_validation < Questionnaired1pdv93.date_validation'
							),
							'order' => array( 'questionnairesd1pdvs93pcd.date_validation DESC' ),
							'limit' => 1
						);
						$sqlRendezvous =  $Rendezvous->sq( $sqRendezvous );

						$query['fields']['Demenagement.interne'] = "( CASE WHEN ( {$sqlRendezvous} ) IN ( {$sqlCommunautesr} ) = TRUE THEN TRUE ELSE FALSE END ) AS \"Demenagement__interne\"";
					}

					$modelClass = ClassRegistry::init( $modelClass );
					$modelClass->forceVirtualFields = true;

					// TODO: le faire dans le modèle beforeSave / afterFind ?
					$record = array(
						'Corpuspdv93' => array(
							'tableausuivipdv93_id' => $this->Tableausuivipdv93->id,
							'fields' => json_encode( $models ),
							'results' => json_encode( $modelClass->find( 'all', $query ) ),
							'options' => json_encode( $this->getOptions( $action ) )
						)
					);

					$this->Tableausuivipdv93->Corpuspdv93->create( $record );
					$success = $this->Tableausuivipdv93->Corpuspdv93->save( null, array( 'atomic' => false ) );
				}
			}

			return $success;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application.
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les méthodes qui ne font rien.
		 */
		public function prechargement() {
			$success = true;

			$methods = array(
				'tableau1b3' => 'qdExportcsvCorpus1b3',
				'tableau1b4' => 'qdExportcsvCorpus1b4',
				'tableau1b5' => 'qdExportcsvCorpus1b5',
				'tableau1b6' => 'qdExportcsvCorpus1b6'
			);

			foreach( $methods as $tableau => $method ) {
				$query = $this->{$method}( null );
				$success = !empty( $query ) && $success;

				$fileName = TMP.DS.'logs'.DS.__CLASS__."__{$tableau}.csv";
				ConfigurableQueryFields::exportQueryFields( $query, 'tableauxsuivispdvs93', $fileName );
			}

			return $success;
		}

		/**
		 * Retourne les options à utiliser dans les exports CSV, en fonction du
		 * nom du tableau.
		 *
		 * @param string $tableau Le nom du tableau
		 * @return array
		 */
		public function getOptions( $tableau ) {
			$options = array();

			if( in_array( $tableau, array( 'tableaud1', 'tableaud2' ) ) ) {
				$options = Hash::merge(
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->enums(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired2pdv93->enums(),
					$this->Tableausuivipdv93->Populationd1d2pdv93->Questionnaired1pdv93->Situationallocataire->enums()
				);
				// INFO: pour que l'intitulé des 1207 ne soit pas "Non scolarisé" mais "Niveau VI (6e à 4e ou formation préprofessionnelle de 1 an et non scolarisé)"
				$options['Questionnaired1pdv93']['nivetu'][1207] = $options['Questionnaired1pdv93']['nivetu'][1206];
			}
			// Options pour le tableau 1B3, on n'a pas besoin de ce qu'il y a au-dessus
			else {
				$Allocataire = ClassRegistry::init( 'Allocataire' );
				$options = $Allocataire->options();

				if( $tableau === 'tableau1b3' ) {
					$options['Difficulte'] = array();
					foreach( array_keys( $this->_categories1b3 ) as $categorie ) {
						$options['Difficulte'][$categorie] = array( '1' => 'Oui' );
					}
				}
				else if( $tableau === 'tableau1b4' ) {
					$options = Hash::merge(
						$options,
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->enums(),
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Actionfp93->enums(),
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Filierefp93->enums(),
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Filierefp93->Categoriefp93->enums(),
						$this->Tableausuivipdv93->Populationb4b5pdv93->Ficheprescription93->Filierefp93->Categoriefp93->Thematiquefp93->enums()
					);
				}
			}

			return $options;
		}

		/**
		 *
		 * @todo modifier l'autre fonction / s'en passer ?
		 *
		 * @param array $query
		 * @return array
		 */
		protected function _completeQueryCorpus( array $query ) {
			$Personne = ClassRegistry::init( 'Personne' );

			// Ajout des champs
			$query['fields'] += ConfigurableQueryFields::getModelsFields(
				array(
					$Personne->Foyer,
					$Personne->Prestation,
					$Personne->Foyer->Dossier,
					$Personne->Foyer->Adressefoyer,
					$Personne->Foyer->Adressefoyer->Adresse
				)
			);

			// Ajout des jointures
			$query['joins'][] = $Personne->join( 'Foyer', array( 'type' => 'INNER' ) );
			$query['joins'][] = $Personne->join( 'Prestation', array( 'type' => 'LEFT OUTER' ) );
			$query['joins'][] = $Personne->Foyer->join(
				'Adressefoyer',
				array(
					'type' => 'LEFT OUTER',
					'conditions' => array(
						'Adressefoyer.id IN( '.$Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
					)
				)
			);
			$query['joins'][] = $Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) );
			$query['joins'][] = $Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) );

			return $query;
		}

		/**
		 * @todo permet de déprécier qdExportcsvCorpusd1() et qdExportcsvCorpusd2()
		 *
		 * @param string $tableau
		 * @param array $search
		 * @return array
		 */
		protected function _queryCorpusD1D2( $tableau, array $search ) {
			$Personne = ClassRegistry::init( 'Personne' );

			if( $tableau === 'tableaud1' ) {
				$query = $this->qdTableaud1( $search );
				$query['joins'][] = $Personne->Rendezvous->join( 'Personne' );
			}
			else if( $tableau === 'tableaud2' ) {
				$query = $this->qdTableaud2( $search );
			}
			else {
				$msg = sprintf( 'Valeur du paramètre $tableau non valide (%s)', $tableau );
				throw new RuntimeException( $msg, 500 );
			}

			$query['fields'] = array();
			$query['contain'] = false;

			$query = $this->_completeQueryCorpus( $query );

			$query['joins'][] = $Personne->Rendezvous->join( 'Structurereferente', array( 'type' => 'INNER' ) );
			$query['joins'][] = $Personne->Rendezvous->join( 'Referent', array( 'type' => 'LEFT OUTER' ) );

			if( $tableau === 'tableaud1' ) {
				$query['joins'][] = $Personne->Questionnaired1pdv93->join( 'Questionnaired2pdv93', array( 'type' => 'LEFT OUTER' ) );
				$query['joins'][] = $Personne->Questionnaired1pdv93->Questionnaired2pdv93->join( 'Sortieaccompagnementd2pdv93', array( 'type' => 'LEFT OUTER' ) );
			}

			$query['fields'] += ConfigurableQueryFields::getModelsFields(
				array(
					$Personne,
					$Personne->Rendezvous,
					$Personne->Rendezvous->Structurereferente,
					$Personne->Rendezvous->Referent,
					$Personne->Questionnaired1pdv93,
					$Personne->Questionnaired1pdv93->Situationallocataire
				)
			);

			if( $tableau === 'tableaud2' ) {
				$query['fields'] += ConfigurableQueryFields::getModelsFields(
					array(
						$Personne->Questionnaired1pdv93->Questionnaired2pdv93,
						$Personne->Questionnaired1pdv93->Questionnaired2pdv93->Sortieaccompagnementd2pdv93
					)
				);
			}

			unset( $query['group'] );

			$query['order'] = array(
				'Rendezvous.daterdv ASC',
				'Questionnaired2pdv93.date_validation ASC'
			);

			return $query;
		}

		/**
		 *
		 * @param string $tableau
		 * @param array $search
		 * @return array
		 */
		protected function _queryCorpus1B3( $tableau, array $search ) {
			$Personne = ClassRegistry::init( 'Personne' );

			$query = array(
				'fields' => ConfigurableQueryFields::getModelsFields(
					array(
						$Personne,
						$Personne->Dsp,
						$Personne->DspRev,
						$Personne->Rendezvous,
						$Personne->Rendezvous->Structurereferente,
						$Personne->Rendezvous->Referent
					)
				),
				'conditions' => array(),
				'contain' => false,
				'joins' => array(
					$Personne->Rendezvous->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Dsp', array( 'type' => 'INNER' ) ),
					$Personne->Rendezvous->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
					$Personne->Rendezvous->join( 'Referent', array( 'type' => 'LEFT OUTER' ) )
				),
				'order' => array(
					'Personne.nom' => 'ASC',
					'Personne.prenom' => 'ASC'
				)
			);

			$query = $this->_completeQueryCorpus( $query );

			foreach( $this->_categories1b3 as $categorie => $params ) {
				if( $params['table'] === 'dsps' ) {
					$a = "( \"Dsp\".{$params['column']} IS NOT NULL AND \"Dsp\".{$params['column']} IN ( '".implode( "', '", $params['values'] )."' ) )
						OR
						( \"DspRev\".{$params['column']} IS NOT NULL AND \"DspRev\".{$params['column']} IN ( '".implode( "', '", $params['values'] )."' ) )";
					$query['fields']["Difficulte.{$categorie}"] = "( {$a} ) AS \"Difficulte__{$categorie}\"";
				}
				else {
					$a = "EXISTS(
						SELECT *
						FROM {$params['table']} AS {$params['alias']}
						WHERE {$params['alias']}.dsp_id = \"Dsp\".\"id\"
						AND ( {$params['alias']}.{$params['column']} IS NOT NULL AND {$params['alias']}.{$params['column']} IN ( '".implode( "', '", $params['values'] )."' ) )
					)
					OR EXISTS(
						SELECT *
						FROM {$params['table']}_revs AS {$params['alias']}_revs
						WHERE {$params['alias']}_revs.dsp_rev_id = \"DspRev\".\"id\"
						AND ( {$params['alias']}_revs.{$params['column']}  IS NOT NULL AND {$params['alias']}_revs.{$params['column']} IN ( '".implode( "', '", $params['values'] )."' ) )
					)";
					$query['fields']["Difficulte.{$categorie}"] = "( {$a} ) AS \"Difficulte__{$categorie}\"";
				}
			}

			// -----------------------------------------------------------------

			$conditions = array_words_replace(
				$this->_tableau1b3Conditions( $search ),
				array( 'rendezvous' => 'Rendezvous', 'dsps_revs' => 'DspRev' )
			);

			$subQuery = array(
				'fields' => array(
					'Rendezvous.id AS "rdv"',
					'Dsp.id AS "dsp"',
					'DspRev.id AS "dsp_rev"',
				),
				'contain' => false,
				'joins' => array(
					$Personne->join( 'Rendezvous', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Dsp', array( 'type' => 'INNER' ) ),
					$Personne->join(
						'DspRev',
						array(
							'type' => 'LEFT OUTER',
							'DspRev.modified <=' => "{$conditions['annee']}-12-31"
						)
					)
				),
				'conditions' => array(
					'Personne.id = Foo.id'
				),
				'order' => array(
					'Rendezvous.daterdv DESC',
					'Rendezvous.heurerdv DESC',
					'Rendezvous.id DESC',
					'Dsp.id DESC',
					'DspRev.modified DESC',
					'DspRev.id DESC'
				),
				'limit' => 1
			);

			// 2. Sous-requête
			// Dont le type de RDV est individuel
			$subQuery['conditions']['Rendezvous.typerdv_id'] = (array)Configure::read( 'Tableausuivipdv93.typerdv_id' );

			// Avec un RDV honore durant l'annee N
			$subQuery['conditions']['EXTRACT(\'YEAR\' FROM "Rendezvous"."daterdv")'] = $conditions['annee'];
			$subQuery['conditions'][] = $this->_conditionStatutRdv( 'Rendezvous.statutrdv_id' );

			// pour la structure referente X (eventuellement)
			foreach( array( 'conditionpdv', 'conditionmaj' ) as $key ) {
				$conditions[$key] = preg_replace( '/^AND /', '', $conditions[$key] );
				if( $conditions[$key] !== '' ) {
					$subQuery['conditions'][] = $conditions[$key];
				}
			}

			// De plus, on restreint les structures referentes a celles qui apparaissent dans le select
			$subQuery['conditions'][] = $this->_conditionStructurereferenteIsPdv( 'Rendezvous.structurereferente_id' );

			$sql = $Personne->sq( $subQuery );

			$replacements = array(
				'Personne' => 'personnes',
				'Dsp' => 'dsps',
				'DspRev' => 'dsps_revs',
				'Rendezvous' => 'rendezvous',
				'Foo' => 'Personne'
			);
			$sql = Hash::get( array_words_replace( array( $sql ), $replacements ), '0' );

			$query['conditions'][] = array(
				'OR' => array(
					array(
						'DspRev.id IS NULL',
						"( Rendezvous.id, Dsp.id ) IN ( SELECT \"tmp\".\"rdv\", \"tmp\".\"dsp\" FROM ( {$sql} ) AS \"tmp\" )"
					),
					array(
						'DspRev.id IS NOT NULL',
						"( Rendezvous.id, Dsp.id, DspRev.id ) IN ( SELECT \"tmp\".\"rdv\", \"tmp\".\"dsp\" , \"tmp\".\"dsp_rev\" FROM ( {$sql} ) AS \"tmp\" )"
					)
				)
			);

			$query['joins'][] = $Personne->join( 'DspRev', array( 'type' => 'LEFT OUTER' ));

			return $query;
		}

		/**
		 * TODO: permet de déprécier qdExportcsvCorpus1b4() et qdExportcsvCorpus1b5()
		 *
		 * @param string $tableau
		 * @param array $search
		 * @return string
		 */
		protected function _queryCorpus1B41B5( $tableau, array $search ) {
			$Personne = ClassRegistry::init( 'Personne' );

			if( $tableau === 'tableau1b4' ) {
				$query = $this->qdTableau1b4( $search );
			}
			else if( $tableau === 'tableau1b5' ) {
				$query = $this->qdTableau1b5( $search );
			}
			else {
				$msg = sprintf( 'Valeur du paramètre $tableau non valide (%s)', $tableau );
				throw new RuntimeException( $msg, 500 );
			}

			// INFO: il manque la jointure sur Personne
			array_unshift( $query['joins'], $Personne->Ficheprescription93->join( 'Personne' ) );

			$categories = $this->_tableau1b41b5Categories( $tableau, $search );
			$query['conditions'][] = array( 'OR' => Hash::extract($categories, '{s}.{s}') );

			$query['fields'] = array();
			$query['contain'] = false;
			$query = $this->_completeQueryCorpus( $query );
			$query['fields'] += ConfigurableQueryFields::getModelsFields(
				array(
					$Personne,
					$Personne->Ficheprescription93,
					$Personne->Ficheprescription93->Actionfp93,
					$Personne->Ficheprescription93->Adresseprestatairefp93,
					$Personne->Ficheprescription93->Filierefp93,
					$Personne->Ficheprescription93->Personne,
					$Personne->Ficheprescription93->Referent,
					$Personne->Ficheprescription93->Adresseprestatairefp93->Prestatairefp93,
					$Personne->Ficheprescription93->Filierefp93->Categoriefp93,
					$Personne->Ficheprescription93->Filierefp93->Categoriefp93->Thematiquefp93,
					$Personne->Ficheprescription93->Referent->Structurereferente
				)
			);
			unset( $query['group'] );

			$query['joins'][] = $Personne->Ficheprescription93->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) );

			// Ajout de champs se trouvant dans les tableaux de résultats
			if( $tableau === 'tableau1b5' ) {
				$query['fields']['Ficheprescription93.personne_a_integre'] = '( CASE WHEN "Ficheprescription93"."personne_a_integre" = \'1\' THEN \'Oui\' ELSE NULL END ) AS "Ficheprescription93__personne_a_integre"';
				$query['fields']['Ficheprescription93.personne_pas_deplace'] = '( CASE WHEN "Ficheprescription93"."benef_retour_presente" IN ( \'non\', \'excuse\' ) THEN \'Oui\' ELSE NULL END ) AS "Ficheprescription93__personne_pas_deplace"';
				$query['fields']['Ficheprescription93.en_attente'] = '( CASE WHEN ( "Ficheprescription93"."date_signature_partenaire" IS NULL ) AND ( "Ficheprescription93"."benef_retour_presente" IS NULL OR "Ficheprescription93"."benef_retour_presente" = \'oui\' ) THEN \'Oui\' ELSE NULL END ) AS "Ficheprescription93__en_attente"';
			}

			return $query;
		}

		protected function _queryCorpus1B6( $tableau, array $search ) {
			$Personne = ClassRegistry::init( 'Personne' );

			$query = $this->qdTableau1b6( $search );

			$query['fields'] = array();
			$query = $this->_completeQueryCorpus( $query );
			$query['fields'] += ConfigurableQueryFields::getModelsFields(
				array(
					$Personne,
					$Personne->Rendezvous,
					$Personne->Rendezvous->RendezvousThematiquerdv,
					$Personne->Rendezvous->RendezvousThematiquerdv->Thematiquerdv,
					$Personne->Rendezvous->Referent,
					$Personne->Rendezvous->Structurereferente,
					$Personne->Rendezvous->Statutrdv,
					$Personne->Rendezvous->Typerdv
				)
			);

			unset( $query['group'] );
			$query['joins'][] = $Personne->Rendezvous->join( 'Statutrdv', array( 'type' => 'INNER' ) );

			return $query;
		}

		/**
		 * Retourne le querydata à utiliser pour réaliser l'export du corpus d'un
		 * tableau de suivi.
		 * Utilisé dans la méthode historiser.
		 *
		 * @param string $tableau Le nom du tableau de suivi
		 * @param array $search Les filtes renvoyés par le moteur de recherche
		 * @return array
		 * @throws RuntimeException Lorsque le nom du tableau n'est pas reconnu
		 */
		public function queryCorpus( $tableau, array $search ) {
			if( in_array( $tableau, array( 'tableaud1', 'tableaud2' ) ) ) {
				$query = $this->_queryCorpusD1D2( $tableau, $search );
			}
			else if( $tableau === 'tableau1b3' ) {
				$query = $this->_queryCorpus1B3( $tableau, $search );
			}
			// tableau1b4
			else if( in_array( $tableau, array( 'tableau1b4', 'tableau1b5' ) ) ) {
				$query = $this->_queryCorpus1B41B5( $tableau, $search );
			}
			else if( $tableau === 'tableau1b6' ) {
				$query = $this->_queryCorpus1B6( $tableau, $search );
			}
			else {
				$msg = sprintf( 'Valeur du paramètre $tableau non valide (%s)', $tableau );
				throw new RuntimeException( $msg, 500 );
			}

			return $query;
		}

		//----------------------------------------------------------------------

		/**
		 * Retourne le querydata utilisé dans la recherche de tableaux de suivi.
		 *
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Communautesr' => 'LEFT OUTER',
				'Pdv' => 'LEFT OUTER',
				'Photographe' => 'LEFT OUTER',
				'Referent' => 'LEFT OUTER'
			);

			$vfNomcomplet = $this->Tableausuivipdv93->Photographe->sqVirtualfield( 'nom_complet', false );

			// Jointure spéciale pour le PDV: soit via Pdv, soit via la structure du référent
			$joins = array(
				'Structurereferente' => array_words_replace(
					$this->Tableausuivipdv93->Referent->join( 'Structurereferente', array( 'type' => $types['Pdv'] ) ),
					array( 'Structurereferente' => 'Pdv' )
				),
				'Pdv' => $this->Tableausuivipdv93->join( 'Pdv', array( 'type' => $types['Pdv'] ) )
			);
			$joinPdv = $joins['Pdv'];
			$joinPdv['conditions'] = array(
				'OR' => array(
					$joins['Pdv']['conditions'],
					$joins['Structurereferente']['conditions']
				)
			);

			$query = array(
				'fields' => array(
					'Tableausuivipdv93.id',
					'Tableausuivipdv93.annee',
					'Tableausuivipdv93.type',
					'Communautesr.name',
					'Pdv.lib_struc',
					$this->Tableausuivipdv93->Referent->sqVirtualField( 'nom_complet' ),
					'Tableausuivipdv93.name',
					'Tableausuivipdv93.version',
					"( CASE WHEN \"Photographe\".\"id\" IS NOT NULL THEN {$vfNomcomplet} ELSE 'Photographie automatique' END ) AS \"Photographe__nom_complet\"",
					'Tableausuivipdv93.created',
					'Tableausuivipdv93.modified',
				),
				'contain' => false,
				'joins' => array(
					$this->Tableausuivipdv93->join( 'Communautesr', array( 'type' => $types['Communautesr'] ) ),
					$this->Tableausuivipdv93->join( 'Photographe', array( 'type' => $types['Photographe'] ) ),
					$this->Tableausuivipdv93->join( 'Referent', array( 'type' => $types['Referent'] ) ),
					$joinPdv
				),
				'order' => array(
					'Tableausuivipdv93.annee DESC',
					'Pdv.lib_struc ASC',
					'Referent.nom_complet ASC',
					'Tableausuivipdv93.name ASC',
					'Tableausuivipdv93.modified DESC'
				)
			);

			return $query;
		}

		/**
		 * Complète le querydata avec des conditions issues des filtres du moteur
		 * de recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search = array() ) {
			// 1. Valeurs simples
			$fields = array( 'annee' => 'annee', 'tableau' => 'name', 'type' => 'type' );
			foreach( $fields as $searchField => $tableField ) {
				$value = Hash::get( $search, "Search.{$searchField}" );
				if( !empty( $value ) ) {
					$query['conditions']["Tableausuivipdv93.{$tableField}"] = $value;
				}
			}

			// 2. Valeurs particulières avec potentiellement la chaîne de caractères NULL
			$fields = array( 'communautesr_id', 'user_id' );
			foreach( $fields as $field ) {
				$value = suffix( Hash::get( $search, "Search.{$field}" ) );
				if( !empty( $value ) ) {
					if( $value == 'NULL' ) {
						$query['conditions'][] = "Tableausuivipdv93.{$field} IS NULL";
					}
					else {
						$query['conditions']["Tableausuivipdv93.{$field}"] = $value;
					}
				}
			}

			$referent_id = suffix( Hash::get( $search, 'Search.referent_id' ) );
			if( !empty( $referent_id ) ) {
				$query['conditions']['Referent.id'] = $referent_id;
			}
			else {
				$structurereferente_id = suffix( Hash::get( $search, 'Search.structurereferente_id' ) );
				if( !empty( $structurereferente_id ) ) {
					$query['conditions'][] = array( 'Pdv.id' => $structurereferente_id );
				}
			}

			return $query;
		}

		/**
		 * Retourne la liste des référents des PDV pour lesquels les tableaux de
		 * PDV doivent être calculés.
		 *
		 * @see Tableausuivipdv93.conditionsPdv dans le webrsa.inc
		 *
		 * @param integer $structurereferente_id L'id du PDV pour filtrage éventuel
		 * @return array
		 */
		public function listeReferentsPdvs( $structurereferente_id = null ) {
			$query = array(
				'fields' => array(
					'( "Structurereferente"."id" || \'_\' || "Referent"."id" ) AS "Referent__id"',
					'Referent.nom_complet'
				),
				'contain' => false,
				'joins' => array(
					$this->Tableausuivipdv93->Pdv->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
					$this->Tableausuivipdv93->Pdv->Referent->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) ),
				),
				'conditions' => array_words_replace(
					(array)Configure::read( 'Tableausuivipdv93.conditionsPdv' ),
					array( 'Pdv' => 'Structurereferente' )
				),
				'order' => array( 'Referent.nom_complet_court' )
			);

			if( !empty( $structurereferente_id ) ) {
				$query['conditions']['Referent.structurereferente_id'] = $structurereferente_id;
			}

			$results = $this->Tableausuivipdv93->Pdv->Referent->find( 'all', $query );
			$results = Hash::combine( $results, '{n}.Referent.id', '{n}.Referent.nom_complet' );

			return $results;
		}

		/**
		 * Retourne la liste des PDV pour lesquels les tableaux de PDV doivent
		 * être calculés.
		 *
		 * @see Tableausuivipdv93.conditionsPdv dans le webrsa.inc
		 *
		 * @return array
		 */
		public function listePdvs() {
			return $this->Tableausuivipdv93->Pdv->find(
				'list',
				array(
					'contain' => false,
					'joins' => array(
						$this->Tableausuivipdv93->Pdv->join( 'Typeorient', array( 'type' => 'INNER' ) ),
					),
					'conditions' => (array)Configure::read( 'Tableausuivipdv93.conditionsPdv' ),
					'order' => array( 'Pdv.lib_struc' )
				)
			);
		}

		/**
		 * @todo Tableausuivipdv93::getOptions()
		 *
		 * @param array $params
		 * @return array
		 */
		public function options( array $params = array() ) {
			$params += array(
				'tableau' => null,
				'structuresreferentes' => null,
				'referents' => null
			);

			$years = array_reverse( range( 2009, date( 'Y' ) ) );

			$options = array(
				'Search' => array(
					'annee' => array_combine( $years, $years ),
					'communautesr_id' => $this->Tableausuivipdv93->Communautesr->find( 'list' ),
					'structurereferente_id' => $params['structuresreferentes'],
					'referent_id' => $params['referents'],
					'tableau' => $this->tableaux,
					'typethematiquefp93_id' => ClassRegistry::init( 'Thematiquefp93' )->enum( 'type' ),
					'mode' => array( 'fse' => 'FSE', 'statistiques' => 'Statistiques' )
				),
				'problematiques' => $this->problematiques(),
				'acteurs' => $this->acteurs(),
				'Tableausuivipdv93' => array( 'name' => $this->tableaux )
			);

			// Get liste des années
			$query = array(
				'fields' => array(
					' DISTINCT "Thematiquefp93"."yearthema" ' ,
				),
				'conditions' => array(
				),
				'order' => array(
				)
			);
			$results = $this->Thematiquefp93->find( 'all', $query );
			$options['Search']['yearthematiquefp93_id'] = Hash::combine( $results, '{n}.Thematiquefp93.yearthema', '{n}.Thematiquefp93.yearthema' );

			return $options;
		}
	}
?>