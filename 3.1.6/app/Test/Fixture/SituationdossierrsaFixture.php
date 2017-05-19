<?php
	/**
	 * Code source de la classe SituationdossierrsaFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe SituationdossierrsaFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class SituationdossierrsaFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Situationdossierrsa',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'dossier_id' => 1,
				'etatdosrsa' => '2',
				'dtrefursa' => null,
				'moticlorsa' => null,
				'dtclorsa' => null,
				'motirefursa' => null
			)
		);

	}
?>