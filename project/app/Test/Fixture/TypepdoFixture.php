<?php
	/**
	 * Code source de la classe TypepdoFixture.
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 app::uses('PgsqlConstraintsFixture', 'Test/Fixture');
	 
	/**
	 * La classe TypepdoFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class TypepdoFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Typepdo',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'libelle' => 'TypePdo test',
				'originepcg' => 'N',
				'cerparticulier' => 'N',
			)
		);

	}
?>