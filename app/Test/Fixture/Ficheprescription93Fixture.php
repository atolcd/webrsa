<?php
	/**
	 * Code source de la classe Ficheprescription93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * La classe Ficheprescription93Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Ficheprescription93Fixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Ficheprescription93',
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
				'referent_id' => 1,
				'filierefp93_id' => 1,
				'actionfp93_id' => 1,
				'adresseprestatairefp93_id' => 1,
				'statut' => '01renseignee'
			)
		);

	}
?>