<?php
	/**
	 * Code source de la classe TestBakeDossierSuperFixture.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	App::uses('SuperFixtureInterface', 'SuperFixture.Interface');

	/**
	 * TestBakeDossierSuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class TestBakeDossierSuperFixture implements SuperFixtureInterface {
		/**
		 * Fixtures à charger en plus pour un bon fonctionnement
		 * 
		 * @var array
		 */
		public static $fixtures = array(
			'app.Dossier',
			'app.Foyer',
			'app.User',
			'app.Typeorient',
			'app.Structurereferente',
			'app.Referent',
			'app.Adresse',
			'app.Adressefoyer',
			'app.Situationdossierrsa',
			'app.Personne',
			'app.Prestation',
			'app.Detaildroitrsa',
			'app.Detailcalculdroitrsa',
			'app.Calculdroitrsa',
			'app.Orientstruct',
			'app.Cui',
			'app.Serviceinstructeur',
			'app.Group',
			'app.AdresseCanton',
			'app.Canton',
			'app.Nonoriente66',
		);
		
		/**
		 * Permet d'obtenir les informations nécéssaire pour charger la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData() {
			return array();
		}
	}
