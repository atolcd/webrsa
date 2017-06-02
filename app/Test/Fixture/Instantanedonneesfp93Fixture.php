<?php
	/**
	 * Code source de la classe Instantanedonneesfp93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * La classe Instantanedonneesfp93Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Instantanedonneesfp93Fixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Instantanedonneesfp93',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'ficheprescription93_id' => 1,
				'referent_fonction' => 'Référent',
				'structure_name' => '« Projet de Ville RSA d\'Aubervilliers»',
				'structure_num_voie' => '117',
				'structure_type_voie' => 'R',
				'structure_nom_voie' => 'Andre Karman',
				'structure_code_postal' => '93300',
				'structure_ville' => 'Aubervilliers'
			)
		);

	}
?>