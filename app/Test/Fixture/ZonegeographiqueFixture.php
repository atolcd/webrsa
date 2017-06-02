<?php
	/**
	 * Code source de la classe ZonegeographiqueFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe ZonegeographiqueFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class ZonegeographiqueFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Zonegeographique',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'codeinsee' => '93001',
				'libelle' => 'AUBERVILLIERS',
			),
			array(
				'codeinsee' => '93008',
				'libelle' => 'BOBIGNY',
			),
			array(
				'codeinsee' => '93010',
				'libelle' => 'BONDY',
			),
		);

	}
?>