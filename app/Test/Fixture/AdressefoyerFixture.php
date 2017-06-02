<?php
	/**
	 * Code source de la classe AdressefoyerFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe AdressefoyerFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class AdressefoyerFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Adressefoyer',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'adresse_id' => 1,
				'foyer_id' => 1,
				'rgadr' => '01',
				'dtemm' => '2010-05-25',
				'typeadr' => 'D'
			),
			array(
				'adresse_id' => 2,
				'foyer_id' => 2,
				'rgadr' => '01',
				'dtemm' => '2012-06-08',
				'typeadr' => 'D'
			),
			array(
				'adresse_id' => 3,
				'foyer_id' => 2,
				'rgadr' => '02',
				'dtemm' => '2010-09-20',
				'typeadr' => 'D'
			),
		);
	}
?>