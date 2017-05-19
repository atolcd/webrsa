<?php
	/**
	 * Code source de la classe Coderomemetierdsp66Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Coderomemetierdsp66Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Coderomemetierdsp66Fixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Coderomemetierdsp66',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'code' => '11111',
				'name' => 'EMPLOYE DE MENAGE A DOMICILE',
				'coderomesecteurdsp66_id' => 1
			)
		);

	}
?>