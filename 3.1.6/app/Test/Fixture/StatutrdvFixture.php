<?php
	/**
	 * Code source de la classe StatutrdvFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe StatutrdvFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class StatutrdvFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Statutrdv',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array( 'libelle' => 'Présent', 'provoquepassageep' => '0', 'permetpassageepl' => '0' ),
			array( 'libelle' => 'Absent non excusé', 'provoquepassageep' => '1', 'permetpassageepl' => '0' ),
		);

	}
?>