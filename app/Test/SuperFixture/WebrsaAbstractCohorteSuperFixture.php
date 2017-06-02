<?php
	/**
	 * Code source de la classe WebrsaAbstractCohorteSuperFixture.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	App::uses('SuperFixtureInterface', 'SuperFixture.Interface');

	/**
	 * WebrsaAbstractCohorteSuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class WebrsaAbstractCohorteSuperFixture implements SuperFixtureInterface {
		/**
		 * Fixtures à charger en plus pour un bon fonctionnement
		 * 
		 * @var array
		 */
		public static $fixtures = array(
			'Personne',
			'Typeorient',
			'Structurereferente',
			'Referent',
			'User',
			'Nonoriente66',
			'Prestation',
			'PersonneReferent',
		);
		
		/**
		 * Permet d'obtenir les informations nécéssaire pour charger la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData() {
			return array(
				'Orientstruct' => array(
					1 => array(
						'personne_id' => 1,
						'typeorient_id' => 1,
						'structurereferente_id' => 1,
						'propo_algo' => null,
						'valid_cg' => null,
						'date_propo' => null,
						'date_valid' => '2009-06-24',
						'statut_orient' => 'Orienté',
						'date_impression' => null,
						'daterelance' => null,
						'statutrelance' => null,
						'date_impression_relance' => null,
						'referent_id' => null,
						'etatorient' => null,
						'rgorient' => 1,
						'structureorientante_id' => null,
						'referentorientant_id' => null,
						'user_id' => null,
						'haspiecejointe' => '0',
						'origine' => 'manuelle',
						'typenotification' => null,
					)
				),
				'Serviceinstructeur' => array(
					1 => array(
						'lib_service' => 'test',
						'type_voie' => 'test',
						'code_insee' => '12345',
						'numdepins' => '123',
						'typeserins' => 'S',
						'numcomins' => '123',
						'numagrins' => '12',
					)
				),
				'Structurereferente' => array(
					1 => array(
						'lib_struc' => 'test',
						'typeorient_id' => 1,
					)
				),
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
			);
		}
	}

