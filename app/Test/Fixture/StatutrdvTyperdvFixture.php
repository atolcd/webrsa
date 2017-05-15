<?php
	/**
	 * Code source de la classe StatutrdvTyperdvFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe StatutrdvTyperdvFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class StatutrdvTyperdvFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'StatutrdvTyperdv',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'statutrdv_id' => 2,
				'typerdv_id' => 1,
				'nbabsenceavantpassageep' => 2,
				'motifpassageep' => 'De votre fait ....'
			),
		);

	}
?>