<?php
	/**
	 * Code source de la classe Dossierpcg66Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * Classe Dossierpcg66Fixture.
	 *
	 * @package app.Test.Fixture
	 */
	class Dossierpcg66Fixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Dossierpcg66',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'foyer_id' => 1,
				'poledossierpcg66_id' => 1,
				'dateaffectation' => '2012-12-11',
				'typepdo_id' => 1,
				'datereceptionpdo' => '2012-12-11',
				'originepdo_id' => 1,
				'haspiecejointe' => 0,
				'etatdossierpcg' => 'atttransmisop',
				'dateimpression' => '2012-12-11',
				'istransmis' => '0'
			)
		);

	}
?>