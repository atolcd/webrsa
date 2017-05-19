<?php
	/**
	 * Code source de la classe OriginepdoFixture.
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 app::uses('PgsqlConstraintsFixture', 'Test/Fixture');

	/**
	 * La classe OriginepdoFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class OriginepdoFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Originepdo',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'libelle' => 'OriginePdo test',
				'originepcg' => 'N',
				'cerparticulier' => 'N',
			)
		);

	}
?>