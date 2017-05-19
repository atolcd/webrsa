<?php
	/**
	 * Code source de la classe AllocataireSuperFixture.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	App::uses('SuperFixtureInterface', 'SuperFixture.Interface');

	/**
	 * AllocataireSuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class AllocataireSuperFixture implements SuperFixtureInterface
	{
		/**
		 * Fixtures supplémentaire à charger à vide
		 *
		 * @var array
		 */
		public static $fixtures = array(
			'app.Activite',
			'app.Calculdroitrsa',
			'app.Commissionep',
			'app.Detaildroitrsa',
			'app.Dossierep',
			'app.Orientstruct',
			'app.Passagecommissionep',
			'app.PersonneReferent',
			'app.Referent',
			'app.Structurereferente',
			'app.Typeorient',
		);

		/**
		 * Permet d'obtenir les informations nécéssaire pour charger la SuperFixture
		 *
		 * @return array
		 */
		public static function getData() {
			return array(
				'Dossier' => array(
					1 => array(
						'numdemrsa' => '00000010976',
						'dtdemrsa' => '2009-06-01',
						'dtdemrmi' => null,
						'numdepinsrmi' => null,
						'typeinsrmi' => null,
						'numcominsrmi' => null,
						'numagrinsrmi' => null,
						'numdosinsrmi' => null,
						'numcli' => null,
						'numorg' => '976',
						'fonorg' => 'CAF',
						'matricule' => '060000100000000',
						'statudemrsa' => 'C',
						'typeparte' => 'CG',
						'ideparte' => '976',
						'fonorgcedmut' => null,
						'numorgcedmut' => null,
						'matriculeorgcedmut' => null,
						'ddarrmut' => null,
						'codeposanchab' => null,
						'fonorgprenmut' => null,
						'numorgprenmut' => null,
						'dddepamut' => null,
						'detaildroitrsa_id' => null,
						'avispcgdroitrsa_id' => null,
						'organisme_id' => null
					),
				),
				'Foyer' => array(
					1 => array(
						'dossier_id' => 1,
						'sitfam' => 'CEL',
						'ddsitfam' => '1979-01-24',
						'typeocclog' => null,
						'mtvallocterr' => null,
						'mtvalloclog' => null,
						'contefichliairsa' => null,
						'mtestrsa' => null,
						'raisoctieelectdom' => null,
						'regagrifam' => null,
						'haspiecejointe' => '0'
					)
				),
				'Adressefoyer' => array(
					1 => array(
						'rgadr' => '01',
						'adresse_id' => 1,
						'foyer_id' => 1,
						'dtemm' => null,
						'typeadr' => null,
					),
				),
				'Adresse' => array(
					1 => array(
						'numvoie' => '2',
						'codepos' => '84238',
						'pays' => 'FRA',
						'nomcom' => 'DenisVille',
						'nomvoie' => 'DE PEREZ',
						'libtypevoie' => 'AVENUE',
						'complideadr' => null,
						'compladr' => null,
						'lieudist' => null,
						'canton' => null,
						'typeres' => null,
						'topresetr' => null,
					),
				),
				'Situationdossierrsa' => array(
					1 => array(
						'dossier_id' => 1,
						'etatdosrsa' => '2',
						'dtrefursa' => null,
						'moticlorsa' => null,
						'dtclorsa' => null,
						'motirefursa' => null,
					),
				),
				'Personne' => array(
					1 => array(
						'foyer_id' => 1,
						'qual' => 'MR',
						'nom' => 'BUFFIN',
						'prenom' => 'CHRISTIAN',
						'nomnai' => 'BUFFIN',
						'prenom2' => 'MARIE',
						'prenom3' => 'JOSEPH',
						'nomcomnai' => 'BELGIQUE',
						'dtnai' => '1979-01-24',
						'rgnai' => null,
						'typedtnai' => 'N',
						'nir' => null,
						'topvalec' => null,
						'sexe' => '1',
						'nati' => 'A',
						'dtnati' => '1979-01-24',
						'pieecpres' => null,
						'idassedic' => null,
						'numagenpoleemploi' => null,
						'dtinscpoleemploi' => null,
						'numfixe' => null,
						'numport' => null,
						'haspiecejointe' => '0',
						'email' => null
					)
				),
				'Prestation' => array(
					1 => array(
						'personne_id' => 1,
						'natprest' => 'RSA',
						'rolepers' => 'DEM',
						'topchapers' => true
					)
				),
				'Dsp' => array(
					1 => array(
						'personne_id' => 1
					)
				),
				'DspRev' => array(
					1 => array(
						'personne_id' => 1,
						'dsp_id' => 1,
						'natlog' => '0902',
						'haspiecejointe' => '0'
					)
				),
				'Dossiercov58' => array(
					1 => array(
						'personne_id' => 1,
						'themecov58_id' => 1,
						'created' => '2015-08-14 16:40:13',
						'modified' => '2015-08-14 16:40:13',
						'themecov58' => 'proposorientationscovs58'
					)
				),
				'Cov58' => array(
					1 => array(
						'name' => null,
						'lieu' => null,
						'datecommission' => '2015-10-15 14:00:00',
						'observation' => null,
						'etatcov' => 'associe',
						'sitecov58_id' => 1
					)
				),
				'Passagecov58' => array(
					1 => array(
						'cov58_id' => 1,
						'dossiercov58_id' => 1,
						'user_id' => 1,
						'etatdossiercov' => 'associe',
						'impressiondecision' => null,
						'created' => '2015-08-14 16:40:13',
						'modified' => '2015-08-14 16:40:13',
					)
				),
				'Propoorientationcov58' => array(
					1 => array(
						'dossiercov58_id' => 1,
						'typeorient_id' => 1,
						'structurereferente_id' => 1,
						'referent_id' => 1,
						'datedemande' => '2011-01-26',
						'rgorient' => 1,
						'covtypeorient_id' => null,
						'covstructurereferente_id' => null,
						'datevalidation' => null,
						'commentaire' => null,
						'user_id' => 1,
						'decisioncov' => null,
						'covreferent_id' => null,
						'structureorientante_id' => 1,
						'referentorientant_id' => 1,
						'nvorientstruct_id' => null,
					)
				)
			);
		}
	}
