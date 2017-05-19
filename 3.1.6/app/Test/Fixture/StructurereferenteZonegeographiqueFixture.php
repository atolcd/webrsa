<?php
	/**
	 * Code source de la classe StructurereferenteZonegeographiqueFixture.
	 *
	 * @package app.Test.Fixture
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/Test/CakePHP Fixture.php.
	 */

	/**
	 * La classe StructurereferenteZonegeographiqueFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class StructurereferenteZonegeographiqueFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'StructurereferenteZonegeographique',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'structurereferente_id' => 1,
				'zonegeographique_id' => 1,
			)
		);

	}
?>