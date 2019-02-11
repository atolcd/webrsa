<?php
	/**
	 * Code source de la classe StructurereferenteFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * Classe StructurereferenteFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class StructurereferenteFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Structurereferente',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'typeorient_id' => 1,
				'lib_struc' => '« Projet de Ville RSA d\'Aubervilliers»',
				'num_voie' => '117',
				'type_voie' => 'R',
				'nom_voie' => '	Andre Karman',
				'code_postal' => '93300',
				'ville' => 'Aubervilliers',
				'code_insee' => '93001',
				'filtre_zone_geo' => true,
				'contratengagement' => 	'O',
				'apre' => 'O',
				'orientation' => 'O',
				'pdo' => 'O',
				'numtel' => '0153560510',
				'actif' => 'O',
				'typestructure' => 'msp',
			)
		);

	}
?>