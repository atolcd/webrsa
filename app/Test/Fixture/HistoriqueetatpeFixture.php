<?php
	/**
	 * Code source de la classe HistoriqueetatpeFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * Classe HistoriqueetatpeFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class HistoriqueetatpeFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Historiqueetatpe',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'informationpe_id' => 1,
				'identifiantpe' => '0609065370Y',
				'date' => '2010-12-01',
				'etat' => 'inscription',
				'code' => 1,
				'motif' => null,
				'codeinsee' => null,
				'localite' => null,
				'adresse' => null,
				'ale' => null,
			)
		);
	}
?>