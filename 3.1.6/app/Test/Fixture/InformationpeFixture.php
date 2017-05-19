<?php
	/**
	 * Code source de la classe InformationpeFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe InformationpeFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class InformationpeFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Informationpe',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'nir' => null,
				'nom' => 'BUFFIN',
				'prenom' => 'CHRISTIAN',
				'dtnai' => '1979-01-24'
			)
		);
	}
?>