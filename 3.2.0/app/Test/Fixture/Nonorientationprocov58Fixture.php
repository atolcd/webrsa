<?php
	/**
	 * Code source de la classe Nonorientationprocov58Fixture.
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 app::uses('PgsqlConstraintsFixture', 'Test/Fixture');
	 
	/**
	 * La classe Nonorientationprocov58Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Nonorientationprocov58Fixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Nonorientationprocov58',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
		);

	}
?>