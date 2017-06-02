<?php
	/**
	 * Code source de la classe TyperdvFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe TyperdvFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class TyperdvFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Typerdv',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'libelle' => 'Evaluation pour orientation',
				'modelenotifrdv' => 'Evaluation',
				'nbabsencesavpassageep' => 0,
				'nbabsaveplaudition' => 0,
				'motifpassageep' => null
			),
			array(
				'libelle' => 'Elaboration du CER',
				'modelenotifrdv' => 'Elaboration_CER',
				'nbabsencesavpassageep' => 0,
				'nbabsaveplaudition' => 0,
				'motifpassageep' => null
			),
		);
	}
?>