<?php
	/**
	 * Code source de la classe Expprocer93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe Expprocer93Fixture.
	 *
	 * @package app.Test.Fixture
	 */
	class Expprocer93Fixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Expprocer93',
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
				'metierexerce_id' => 1,
				'secteuracti_id' => 2,
				'anneedeb' => 2005,
				'duree' => '3 mois',
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
			array(
				'cer93_id' => 1,
				'metierexerce_id' => 2,
				'secteuracti_id' => 2,
				'anneedeb' => 2007,
				'duree' => '9 mois',
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
			array(
				'cer93_id' => 2,
				'metierexerce_id' => 2,
				'secteuracti_id' => 2,
				'anneedeb' => 2009,
				'duree' => '6 mois',
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
			array(
				'cer93_id' => 2,
				'metierexerce_id' => 1,
				'secteuracti_id' => 1,
				'anneedeb' => 2005,
				'duree' => '3 ans',
				'created' => '2012-10-01 15:36:00',
				'modified' => '2012-10-01 15:36:00',
			),
		);

	}
?>