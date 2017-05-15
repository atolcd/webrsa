<?php
	/**
	 * Code source de la classe RendezvousFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'cake_app_test_fixture.php' );

	/**
	 * Classe RendezvousFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class RendezvousFixture extends CakeAppTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Rendezvous',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'personne_id' => 1,
				'structurereferente_id' => 1,
				'daterdv' => '2012-01-01',
				'objetrdv' => null,
				'commentairerdv' => null,
				'typerdv_id' => 1,
				'heurerdv' => '08:00:00',
				'referent_id' => null,
				'permanence_id' => null,
				'statutrdv_id' => 1,
				'haspiecejointe' => '0',
				'created' => '2012-01-01 00:00:00',
				'modified' => '2012-01-01 00:00:00',
				'isadomicile' => '0',
			)
		);
	}
?>