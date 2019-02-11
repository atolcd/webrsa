<?php
	/**
	 * Code source de la classe StructurereferenteZonegeographiqueFixture.
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
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