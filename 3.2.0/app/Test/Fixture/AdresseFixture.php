<?php
	/**
	 * Code source de la classe AdresseFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe AdresseFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class AdresseFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Adresse',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'numvoie' => 66,
				'libtypevoie' => 'AVENUE',
				'nomvoie' => 'DE LA REPUBLIQUE',
				'complideadr' => null,
				'compladr' => null,
				'lieudist' => null,
				'numcom' => '93001',
				'codepos' => '93300',
				'nomcom' => 'AUBERVILLIERS',
				'pays' => 'FRA',
			),
			array(
				'numvoie' => 120,
				'libtypevoie' => 'RUE',
				'nomvoie' => 'DU MARECHAL BROUILLON',
				'complideadr' => null,
				'compladr' => null,
				'lieudist' => null,
				'numcom' => '93063',
				'codepos' => '93230',
				'nomcom' => 'ROMAINVILLE',
				'pays' => 'FRA',
			),
			array(
				'numvoie' => 10,
				'libtypevoie' => 'RUE',
				'nomvoie' => 'HECTOR BERLIOZ',
				'complideadr' => null,
				'compladr' => null,
				'lieudist' => null,
				'numcom' => '93008',
				'codepos' => '93000',
				'nomcom' => 'BOBIGNY',
				'pays' => 'FRA',
			),
		);
	}
?>