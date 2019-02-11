<?php
	/**
	 * Code source de la classe Soussujetcer93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe Soussujetcer93Fixture.
	 *
	 * @package app.Test.Fixture
	 */
	class Soussujetcer93Fixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Soussujetcer93',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'name' => 'Sous-sujet 1',
				'sujetcer93_id' => 1
			),
			array(
				'name' => 'Sous-sujet 2',
				'sujetcer93_id' => 1
			),
			array(
				'name' => 'Sous-sujet 3',
				'sujetcer93_id' => 2
			),
			array(
				'name' => 'Sous-sujet 4',
				'sujetcer93_id' => 2
			),
		);

	}
?>