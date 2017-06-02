<?php
	/**
	 * Code source de la classe UserFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'cake_app_test_fixture.php' );

	/**
	 * Classe UserFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class UserFixture extends CakeAppTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'User',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'group_id' => 1,
				'serviceinstructeur_id' => 1,
				'username' => 'jdupont',
				'password' => '83a98ed2a57ad9734eb0a1694293d03c74ae8a57',
				'nom' => 'Dupont',
				'prenom' => 'Jean',
				'date_naissance' => '1980-06-15',
				'date_deb_hab' => '2009-06-01',
				'date_fin_hab' => '2300-05-31',
				'numtel' => '04 05 06 07 08',
				'filtre_zone_geo' => false,
				'numvoie' => null,
				'typevoie' => null,
				'nomvoie' => null,
				'compladr' => null,
				'codepos' => null,
				'ville' => null,
				'isgestionnaire' => 'O',
				'sensibilite' => 'O',
				'structurereferente_id' => 1,
				'referent_id' => null,
				'type' => 'cg',
				'email' => null,
			),
		);
	}
?>