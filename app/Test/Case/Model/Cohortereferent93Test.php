<?php
	/**
	 * Code source de la classe Cohortereferent93Test.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Cohortereferent93Test réalise les tests unitaires du modèle Cohortereferent93.
	 *
	 * @package app.Test.Case.Model
	 */
	class Cohortereferent93Test extends CakeTestCase
	{
		/**
		 * Fixtures associated with this test case
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Adresse',
			'app.Adressefoyer',
			'app.Calculdroitrsa',
			'app.Cer93',
			'app.Cer93Sujetcer93',
			'app.Contratinsertion',
			'app.Dossier',
			'app.Foyer',
			'app.Orientstruct',
			'app.Personne',
			'app.PersonneReferent',
			'app.Prestation',
			'app.Referent',
			'app.Situationdossierrsa',
			'app.Structurereferente',
			'app.Sujetcer93',
		);

		protected $_querydatas = array(
			'affecter' => array(
				'fields' =>
				array(
					'Personne.id',
					'Personne.foyer_id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.nomnai',
					'Personne.prenom2',
					'Personne.prenom3',
					'Personne.nomcomnai',
					'Personne.dtnai',
					'Personne.rgnai',
					'Personne.typedtnai',
					'Personne.nir',
					'Personne.topvalec',
					'Personne.sexe',
					'Personne.nati',
					'Personne.dtnati',
					'Personne.pieecpres',
					'Personne.idassedic',
					'Personne.numagenpoleemploi',
					'Personne.dtinscpoleemploi',
					'Personne.numfixe',
					'Personne.numport',
					'Personne.haspiecejointe',
					'Personne.email',
					'PersonneReferent.id',
					'PersonneReferent.personne_id',
					'PersonneReferent.referent_id',
					'PersonneReferent.dddesignation',
					'PersonneReferent.dfdesignation',
					'PersonneReferent.structurereferente_id',
					'PersonneReferent.haspiecejointe',
					'Calculdroitrsa.id',
					'Calculdroitrsa.personne_id',
					'Calculdroitrsa.mtpersressmenrsa',
					'Calculdroitrsa.mtpersabaneursa',
					'Calculdroitrsa.toppersdrodevorsa',
					'Calculdroitrsa.toppersentdrodevorsa',
					'Contratinsertion.id',
					'Contratinsertion.personne_id',
					'Contratinsertion.structurereferente_id',
					'Contratinsertion.typocontrat_id',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Contratinsertion.diplomes',
					'Contratinsertion.form_compl',
					'Contratinsertion.expr_prof',
					'Contratinsertion.aut_expr_prof',
					'Contratinsertion.rg_ci',
					'Contratinsertion.actions_prev',
					'Contratinsertion.obsta_renc',
					'Contratinsertion.service_soutien',
					'Contratinsertion.pers_charg_suivi',
					'Contratinsertion.objectifs_fixes',
					'Contratinsertion.engag_object',
					'Contratinsertion.sect_acti_emp',
					'Contratinsertion.emp_occupe',
					'Contratinsertion.duree_hebdo_emp',
					'Contratinsertion.nat_cont_trav',
					'Contratinsertion.duree_cdd',
					'Contratinsertion.duree_engag',
					'Contratinsertion.nature_projet',
					'Contratinsertion.observ_ci',
					'Contratinsertion.decision_ci',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.date_saisi_ci',
					'Contratinsertion.lieu_saisi_ci',
					'Contratinsertion.emp_trouv',
					'Contratinsertion.forme_ci',
					'Contratinsertion.commentaire_action',
					'Contratinsertion.raison_ci',
					'Contratinsertion.aviseqpluri',
					'Contratinsertion.sitfam_ci',
					'Contratinsertion.sitpro_ci',
					'Contratinsertion.observ_benef',
					'Contratinsertion.referent_id',
					'Contratinsertion.avisraison_ci',
					'Contratinsertion.type_demande',
					'Contratinsertion.num_contrat',
					'Contratinsertion.typeinsertion',
					'Contratinsertion.bilancontrat',
					'Contratinsertion.engag_object_referent',
					'Contratinsertion.outilsmobilises',
					'Contratinsertion.outilsamobiliser',
					'Contratinsertion.niveausalaire',
					'Contratinsertion.zonegeographique_id',
					'Contratinsertion.autreavisradiation',
					'Contratinsertion.autreavissuspension',
					'Contratinsertion.datesuspensionparticulier',
					'Contratinsertion.dateradiationparticulier',
					'Contratinsertion.faitsuitea',
					'Contratinsertion.positioncer',
					'Contratinsertion.created',
					'Contratinsertion.modified',
					'Contratinsertion.current_action',
					'Contratinsertion.haspiecejointe',
					'Contratinsertion.avenant_id',
					'Contratinsertion.sitfam',
					'Contratinsertion.typeocclog',
					'Contratinsertion.persacharge',
					'Contratinsertion.objetcerprecautre',
					'Contratinsertion.motifannulation',
					'Contratinsertion.datedecision',
					'Contratinsertion.datenotification',
					'Contratinsertion.actioncandidat_id',
					'Contratinsertion.datetacitereconduction',
					'Cer93.id',
					'Cer93.contratinsertion_id',
					'Cer93.user_id',
					'Cer93.matricule',
					'Cer93.dtdemrsa',
					'Cer93.qual',
					'Cer93.nom',
					'Cer93.nomnai',
					'Cer93.prenom',
					'Cer93.dtnai',
					'Cer93.adresse',
					'Cer93.codepos',
					'Cer93.nomcom',
					'Cer93.sitfam',
					'Cer93.natlog',
					'Cer93.incoherencesetatcivil',
					'Cer93.inscritpe',
					'Cer93.cmu',
					'Cer93.cmuc',
					'Cer93.nivetu',
					'Cer93.numdemrsa',
					'Cer93.rolepers',
					'Cer93.identifiantpe',
					'Cer93.positioncer',
					'Cer93.formeci',
					'Cer93.datesignature',
					'Cer93.autresexps',
					'Cer93.isemploitrouv',
					'Cer93.metierexerce_id',
					'Cer93.secteuracti_id',
					'Cer93.naturecontrat_id',
					'Cer93.dureehebdo',
					'Cer93.dureecdd',
					'Cer93.prevu',
					'Cer93.bilancerpcd',
					'Cer93.duree',
					'Cer93.pointparcours',
					'Cer93.datepointparcours',
					'Cer93.pourlecomptede',
					'Cer93.observpro',
					'Cer93.observbenef',
					'Cer93.structureutilisateur',
					'Cer93.nomutilisateur',
					'Cer93.prevupcd',
					'Cer93.sujetpcd',
					'Cer93.created',
					'Cer93.modified',
					'Cer93.dateimpressiondecision',
					'Cer93.observationdecision',
					'Orientstruct.id',
					'Orientstruct.personne_id',
					'Orientstruct.typeorient_id',
					'Orientstruct.structurereferente_id',
					'Orientstruct.propo_algo',
					'Orientstruct.valid_cg',
					'Orientstruct.date_propo',
					'Orientstruct.date_valid',
					'Orientstruct.statut_orient',
					'Orientstruct.date_impression',
					'Orientstruct.daterelance',
					'Orientstruct.statutrelance',
					'Orientstruct.date_impression_relance',
					'Orientstruct.referent_id',
					'Orientstruct.etatorient',
					'Orientstruct.rgorient',
					'Orientstruct.structureorientante_id',
					'Orientstruct.referentorientant_id',
					'Orientstruct.user_id',
					'Orientstruct.haspiecejointe',
					'Orientstruct.origine',
					'Orientstruct.typenotification',
					'Structurereferente.id',
					'Structurereferente.typeorient_id',
					'Structurereferente.lib_struc',
					'Structurereferente.num_voie',
					'Structurereferente.type_voie',
					'Structurereferente.nom_voie',
					'Structurereferente.code_postal',
					'Structurereferente.ville',
					'Structurereferente.code_insee',
					'Structurereferente.filtre_zone_geo',
					'Structurereferente.contratengagement',
					'Structurereferente.apre',
					'Structurereferente.orientation',
					'Structurereferente.pdo',
					'Structurereferente.numtel',
					'Structurereferente.actif',
					'Structurereferente.typestructure',
					'Structurereferente.cui',
					'Prestation.personne_id',
					'Prestation.natprest',
					'Prestation.rolepers',
					'Prestation.topchapers',
					'Prestation.id',
					'Dossier.id',
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Dossier.dtdemrmi',
					'Dossier.numdepinsrmi',
					'Dossier.typeinsrmi',
					'Dossier.numcominsrmi',
					'Dossier.numagrinsrmi',
					'Dossier.numdosinsrmi',
					'Dossier.numcli',
					'Dossier.numorg',
					'Dossier.fonorg',
					'Dossier.matricule',
					'Dossier.statudemrsa',
					'Dossier.typeparte',
					'Dossier.ideparte',
					'Dossier.fonorgcedmut',
					'Dossier.numorgcedmut',
					'Dossier.matriculeorgcedmut',
					'Dossier.ddarrmut',
					'Dossier.codeposanchab',
					'Dossier.fonorgprenmut',
					'Dossier.numorgprenmut',
					'Dossier.dddepamut',
					'Dossier.detaildroitrsa_id',
					'Dossier.avispcgdroitrsa_id',
					'Dossier.organisme_id',
					'Adresse.id',
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.lieudist',
					'Adresse.numcom',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Adresse.pays',
					'Adresse.canton',
					'Adresse.typeres',
					'Adresse.topresetr',
					'Adresse.foyerid',
					'Situationdossierrsa.id',
					'Situationdossierrsa.dossier_id',
					'Situationdossierrsa.etatdosrsa',
					'Situationdossierrsa.dtrefursa',
					'Situationdossierrsa.moticlorsa',
					'Situationdossierrsa.dtclorsa',
					'Situationdossierrsa.motirefursa',
					'( ( "Personne"."nom" || \' \' || "Personne"."prenom" ) ) AS "Personne__nom_complet_court"',
					'( ( SELECT dsps.id FROM dsps WHERE dsps.personne_id = "Personne"."id" LIMIT 1 ) IS NOT NULL ) AS "Dsp__exists"',
					'( "Contratinsertion"."structurereferente_id" = affecter ) AS "Contratinsertion__interne"',
					'( ( CASE WHEN ( "PersonneReferent"."id" IS NULL AND "Contratinsertion"."id" IS NULL ) THEN 1 WHEN ( "PersonneReferent"."id" IS NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'E\' AND "Cer93"."positioncer" = \'00enregistre\' ) THEN 2 WHEN ( "PersonneReferent"."id" IS NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'E\' AND "Cer93"."positioncer" = \'01signe\' ) THEN 3 WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NULL ) THEN 4 WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'E\' AND "Cer93"."positioncer" = \'00enregistre\' ) THEN 5 WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'E\' AND "Cer93"."positioncer" = \'01signe\' ) THEN 6 WHEN ( "PersonneReferent"."id" IS NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'V\' AND "Contratinsertion"."df_ci" <= NOW() ) THEN 7 WHEN ( "PersonneReferent"."id" IS NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'V\' AND "Contratinsertion"."df_ci" > NOW() ) THEN 8 WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'V\' AND "Contratinsertion"."df_ci" <= NOW() ) THEN 9 WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'V\' AND "Contratinsertion"."df_ci" > NOW() ) THEN 10 WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'R\' AND "Cer93"."positioncer" = \'99rejete\' ) THEN 11 ELSE 12 END ) ) AS "Personne__situation"',
				),
				'contain' => '',
				'joins' =>
				array(
					array(
						'table' => '"calculsdroitsrsa"',
						'alias' => 'Calculdroitrsa',
						'type' => 'LEFT OUTER',
						'conditions' => '"Calculdroitrsa"."personne_id" = "Personne"."id"',
					),
					array(
						'table' => '"contratsinsertion"',
						'alias' => 'Contratinsertion',
						'type' => 'LEFT OUTER',
						'conditions' => '"Contratinsertion"."personne_id" = "Personne"."id"',
					),
					array(
						'table' => '"foyers"',
						'alias' => 'Foyer',
						'type' => 'INNER',
						'conditions' => '"Personne"."foyer_id" = "Foyer"."id"',
					),
					array(
						'table' => '"orientsstructs"',
						'alias' => 'Orientstruct',
						'type' => 'INNER',
						'conditions' => '"Orientstruct"."personne_id" = "Personne"."id"',
					),
					array(
						'table' => '"personnes_referents"',
						'alias' => 'PersonneReferent',
						'type' => 'LEFT OUTER',
						'conditions' => '"PersonneReferent"."personne_id" = "Personne"."id" AND "PersonneReferent"."id" IN ( SELECT "personnes_referents"."id" FROM personnes_referents WHERE "personnes_referents"."personne_id" = "Personne"."id" AND "personnes_referents"."dfdesignation" IS NULL ORDER BY "personnes_referents"."dddesignation" DESC LIMIT 1 )',
					),
					array(
						'table' => '"referents"',
						'alias' => 'Referent',
						'type' => 'LEFT OUTER',
						'conditions' => '"PersonneReferent"."referent_id" = "Referent"."id"'
					),
					array(
						'table' => '"prestations"',
						'alias' => 'Prestation',
						'type' => 'INNER',
						'conditions' => '"Prestation"."personne_id" = "Personne"."id" AND "Prestation"."natprest" = \'RSA\'',
					),
					array(
						'table' => '"cers93"',
						'alias' => 'Cer93',
						'type' => 'LEFT OUTER',
						'conditions' => '"Cer93"."contratinsertion_id" = "Contratinsertion"."id"',
					),
					array(
						'table' => '"adressesfoyers"',
						'alias' => 'Adressefoyer',
						'type' => 'INNER',
						'conditions' => '"Adressefoyer"."foyer_id" = "Foyer"."id"',
					),
					array(
						'table' => '"dossiers"',
						'alias' => 'Dossier',
						'type' => 'INNER',
						'conditions' => '"Foyer"."dossier_id" = "Dossier"."id"',
					),
					array(
						'table' => '"adresses"',
						'alias' => 'Adresse',
						'type' => 'INNER',
						'conditions' => '"Adressefoyer"."adresse_id" = "Adresse"."id"',
					),
					array(
						'table' => '"situationsdossiersrsa"',
						'alias' => 'Situationdossierrsa',
						'type' => 'INNER',
						'conditions' => '"Situationdossierrsa"."dossier_id" = "Dossier"."id"',
					),
					array(
						'table' => '"orientsstructs"',
						'alias' => 'Orientstructpcd',
						'type' => 'LEFT OUTER',
						'conditions' => '"Orientstructpcd"."personne_id" = "Personne"."id" AND (("Orientstructpcd"."id" IS NULL) OR ("Orientstructpcd"."id" IN ( SELECT "orientsstructs"."id" AS orientsstructs__id FROM orientsstructs AS orientsstructs WHERE "orientsstructs"."personne_id" = "Personne"."id" AND "orientsstructs"."statut_orient" = \'Orienté\' AND "orientsstructs"."id" NOT IN ( SELECT "orientsstructs"."id" AS orientsstructs__id FROM orientsstructs AS orientsstructs WHERE "orientsstructs"."personne_id" = "Personne"."id" AND "orientsstructs"."statut_orient" = \'Orienté\' AND "orientsstructs"."date_valid" IS NOT NULL ORDER BY "orientsstructs"."date_valid" DESC LIMIT 1 ) ORDER BY "orientsstructs"."date_valid" DESC LIMIT 1 )))',
					),
					array(
						'table' => '"structuresreferentes"',
						'alias' => 'Structurereferente',
						'type' => 'LEFT',
						'conditions' => '"Orientstructpcd"."structurereferente_id" = "Structurereferente"."id"',
					),
				),
				'conditions' =>
				array(
					'Prestation.rolepers' =>
					array(
						0 => 'DEM',
						1 => 'CJT',
					),
					0 => 'Adressefoyer.id IN ( SELECT "adressesfoyers"."id" AS "adressesfoyers__id" FROM "adressesfoyers" AS "adressesfoyers" WHERE "adressesfoyers"."foyer_id" = "Foyer"."id" AND "adressesfoyers"."rgadr" = \'01\' ORDER BY "adressesfoyers"."dtemm" DESC LIMIT 1 )',
					1 => 'Orientstruct.id IN ( SELECT "orientsstructs"."id" AS "orientsstructs__id" FROM "orientsstructs" AS "orientsstructs" WHERE "orientsstructs"."personne_id" = "Personne"."id" AND "orientsstructs"."statut_orient" = \'Orienté\' AND "orientsstructs"."date_valid" IS NOT NULL ORDER BY "orientsstructs"."date_valid" DESC LIMIT 1 )',
					'Orientstruct.structurereferente_id' => 'affecter',
					2 => '( "Contratinsertion"."id" IS NULL OR "Contratinsertion"."id" IN ( SELECT "contratsinsertion"."id" AS "contratsinsertion__id" FROM "contratsinsertion" AS "contratsinsertion" WHERE "contratsinsertion"."personne_id" = "Personne"."id" ORDER BY "contratsinsertion"."created" DESC LIMIT 1 ) )',
					3 =>
					array(
						'OR' =>
						array(
							0 => 'Contratinsertion.id IS NULL',
							1 =>
							array(
								0 => 'Contratinsertion.id IS NOT NULL',
								1 => 'Orientstructpcd.id IS NOT NULL',
							),
							2 =>
							array(
								0 => 'Contratinsertion.id IS NOT NULL',
								'Contratinsertion.decision_ci' => 'V',
								'Contratinsertion.df_ci <=' => '2013-08-07',
							),
							3 =>
							array(
								0 => 'Contratinsertion.id IS NOT NULL',
								'Contratinsertion.decision_ci' => 'E',
								'Cer93.positioncer' =>
								array(
									0 => '00enregistre',
									1 => '01signe',
								),
							),
							4 =>
							array(
								0 => 'Contratinsertion.id IS NOT NULL',
								'Contratinsertion.decision_ci' => 'R',
								'Cer93.positioncer' => '99rejete',
							),
						),
					),
				),
				'order' =>
				array(
					'Personne.situation' => 'ASC',
					0 => 'Orientstruct.date_valid ASC',
					1 => 'Personne.nom ASC',
					2 => 'Personne.prenom ASC',
				),
				'limit' => '10',
			) );

		/**
		 * Méthode exécutée avant chaque test.
		 */
		public function setUp() {
			parent::setUp();
			Configure::write( 'Cg.departement', 93 );
			$this->Cohortereferent93 = ClassRegistry::init( 'Cohortereferent93' );
		}

		/**
		 * Méthode exécutée après chaque test.
		 */
		public function tearDown() {
			unset( $this->Cohortereferent93 );
		}

		/**
		 * Test de la méthode Cohortereferent93::search().
		 *
		 * @group medium
		 * @return void
		 */
		public function testSearch() {
			$result = $this->Cohortereferent93->search(
				'affecter',
				array(),
				false,
				array(),
				false
			);

			$regexes = array(
				'/[[:space:]]+/' => ' '
			);

			$result = recursive_key_value_preg_replace( $result, $regexes );
			$expected = recursive_key_value_preg_replace( $this->_querydatas['affecter'], $regexes );
			$expected['conditions'][3]['OR'][2]['Contratinsertion.df_ci <='] = date( 'Y-m-d', strtotime( Configure::read( 'Cohortescers93.saisie.periodeRenouvellement' ) ) );

			unset( $result['fields'], $expected['fields'] );

			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>