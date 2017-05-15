<?php
	/**
	 * Code source de la classe Adresseprestatairefp93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	/**
	 * La classe Adresseprestatairefp93Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Adresseprestatairefp93Fixture extends CakeTestFixture
	{

		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Adresseprestatairefp93',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'prestatairefp93_id' => 1,
				'adresse' => 'Av. de la république',
				'codepos' => '93000',
				'localite' => 'Bobigny',
				'tel' => null,
				'fax' => null,
				'email' => null,
				'created' => null,
				'modified' => null
			)
		);
	}
?>