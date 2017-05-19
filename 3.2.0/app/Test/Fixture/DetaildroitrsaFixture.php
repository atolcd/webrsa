<?php
	/**
	 * Code source de la classe DetaildroitrsaFixture.
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe DetaildroitrsaFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class DetaildroitrsaFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Detaildroitrsa',
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
				'topsansdomfixe' => false,
				'nbenfautcha' => null,
				'oridemrsa' => null,
				'dtoridemrsa' => null,
				'topfoydrodevorsa' => null,
				'ddelecal' => null,
				'dfelecal' => null,
				'mtrevminigararsa' => null,
				'mtpentrsa' => null,
				'mtlocalrsa' => null,
				'mtrevgararsa' => null,
				'mtpfrsa' => null,
				'mtalrsa' => null,
				'mtressmenrsa' => null,
				'mtsanoblalimrsa' => null,
				'mtredhosrsa' => null,
				'mtredcgrsa' => null,
				'mtcumintegrsa' => null,
				'mtabaneursa' => null,
				'mttotdrorsa' => null,
				'surfagridom' => null,
				'ddsurfagridom' => null,
				'surfagridompla' => null,
				'nbtotaidefamsurfdom' => null,
				'nbtotpersmajosurfdom' => null,
			)
		);
	}
?>