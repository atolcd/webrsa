<?php
	/**
	 * Code source de la classe Prestatairefp93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	/**
	 * La classe Prestatairefp93Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Prestatairefp93Fixture extends CakeTestFixture
	{

		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Prestatairefp93',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'name' => 'Association LE PRISME',
				'created' => null,
				'modified' => null
			),
			array(
				'name' => 'Sol en Si',
				'created' => null,
				'modified' => null
			),
		);
	}
?>