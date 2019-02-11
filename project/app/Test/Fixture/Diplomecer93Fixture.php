<?php
	/**
	 * Code source de la classe Diplomecer93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe Diplomecer93Fixture.
	 *
	 * @package app.Test.Fixture
	 */
	class Diplomecer93Fixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Diplomecer93',
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
				'name' => 'Diplôme de soudeur',
				'annee' => '2005',
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
			array(
				'cer93_id' => 1,
				'name' => 'Diplôme de manutentionnaire',
				'annee' => '2003',
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
			array(
				'cer93_id' => 2,
				'name' => 'BAC',
				'annee' => '2001',
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
			array(
				'cer93_id' => 2,
				'name' => 'Diplôme d\'informatique',
				'annee' => '2005',
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
		);

	}
?>