<?php
	/**
	 * Code source de la classe Cer93Sujetcer93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe Cer93Sujetcer93Fixture.
	 *
	 * @package app.Test.Fixture
	 */
	class Cer93Sujetcer93Fixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Cer93Sujetcer93',
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
				'sujetcer93_id' => 1,
				'soussujetcer93_id' => 1,
				'commentaireautre' => null,
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
			array(
				'cer93_id' => 1,
				'sujetcer93_id' => 3,
				'soussujetcer93_id' => null,
				'commentaireautre' => 'Commentaire autre',
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
			array(
				'cer93_id' => 2,
				'sujetcer93_id' => 2,
				'soussujetcer93_id' => 2,
				'commentaireautre' => null,
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
			array(
				'cer93_id' => 2,
				'sujetcer93_id' => 3,
				'soussujetcer93_id' => null,
				'commentaireautre' => 'Commentaire autre',
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
		);

	}
?>