<?php
	/**
	 * Code source de la classe PrestationFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe PrestationFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class PrestationFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Prestation',
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
				'natprest' => 'RSA',
				'rolepers' => 'DEM',
				'topchapers' => null,
			),
			array(
				'personne_id' => 2,
				'natprest' => 'RSA',
				'rolepers' => 'DEM',
				'topchapers' => null,
			),
			array(
				'personne_id' => 3,
				'natprest' => 'RSA',
				'rolepers' => 'CJT',
				'topchapers' => null,
			),
		);

	}
?>