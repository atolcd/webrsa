<?php
	/**
	 * Code source de la classe Questionnaired2pdv93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Questionnaired2pdv93Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Questionnaired2pdv93Fixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Questionnaired2pdv93',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'personne_id' => 1,
				'situationaccompagnement' => 'maintien',
				'questionnaired1pdv93_id' => 1,
				'sortieaccompagnementd2pdv93_id' => null,
				'date_validation' => '2013-10-22',
				'created' => '2013-10-22 14:57:00',
				'modified' => '2013-10-22 14:58:00',
			),
			array(
				'personne_id' => 2,
				'situationaccompagnement' => 'maintien',
				'questionnaired1pdv93_id' => 2,
				'sortieaccompagnementd2pdv93_id' => null,
				'date_validation' => '2013-10-22',
				'created' => '2013-10-22 14:57:00',
				'modified' => '2013-10-22 14:58:00',
			),
			array(
				'personne_id' => 3,
				'situationaccompagnement' => 'maintien',
				'questionnaired1pdv93_id' => 3,
				'sortieaccompagnementd2pdv93_id' => null,
				'date_validation' => '2013-10-22',
				'created' => '2013-10-22 14:57:00',
				'modified' => '2013-10-22 14:58:00',
			),
		);

	}
?>