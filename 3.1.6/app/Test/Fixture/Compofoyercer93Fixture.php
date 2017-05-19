<?php
	/**
	 * Code source de la classe Compofoyercer93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe Compofoyercer93Fixture.
	 *
	 * @package app.Test.Fixture
	 */
	class Compofoyercer93Fixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Compofoyercer93',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'cer93_id' => 1,
				'qual' => 'MME',
				'nom' => 'DURAND',
				'prenom' => 'JEANNE',
				'dtnai' => '1956-12-05',
				'rolepers' => 'CJT',
			),
			array(
				'cer93_id' => 1,
				'qual' => 'MR',
				'nom' => 'DURAND',
				'prenom' => 'ROBERT',
				'dtnai' => '1966-11-17',
				'rolepers' => 'DEM',
			),
		);

	}
?>