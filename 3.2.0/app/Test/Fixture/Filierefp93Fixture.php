<?php
	/**
	 * Code source de la classe Filierefp93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Filierefp93Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Filierefp93Fixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Filierefp93',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'categoriefp93_id' => 1,
				'name' => 'Filière de test',
				'created' => null,
				'modified' => null
			)
		);
	}
?>