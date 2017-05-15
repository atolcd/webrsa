<?php
	/**
	 * Code source de la classe Coderomesecteurdsp66Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Coderomesecteurdsp66Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Coderomesecteurdsp66Fixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Coderomesecteurdsp66',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'code' => '111',
				'name' => 'Services aux personnes'
			)
		);

	}
?>