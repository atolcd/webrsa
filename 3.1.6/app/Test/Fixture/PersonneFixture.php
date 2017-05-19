<?php
	/**
	 * Code source de la classe PersonneFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'cake_app_test_fixture.php' );

	/**
	 * Classe PersonneFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class PersonneFixture extends CakeAppTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Personne',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'foyer_id' => 1,
				'qual' => 'MR',
				'nom' => 'BUFFIN',
				'prenom' => 'CHRISTIAN',
				'nomnai' => 'BUFFIN',
				'prenom2' => null,
				'prenom3' => null,
				'nomcomnai' => null,
				'dtnai' => '1979-01-24',
				'rgnai' => null,
				'typedtnai' => 'N',
				'nir' => null,
				'topvalec' => true,
				'sexe' => '1',
				'nati' => null,
				'dtnati' => null,
				'pieecpres' => null,
				'idassedic' => null,
				'numagenpoleemploi' => null,
				'dtinscpoleemploi' => null,
				'numfixe' => null,
				'numport' => null,
				'haspiecejointe' => null,
				'email' => null,
			),
			array(
				'foyer_id' => 2,
				'qual' => 'MME',
				'nom' => 'DURAND',
				'prenom' => 'JEANNE',
				'nomnai' => 'DUPUIS',
				'prenom2' => null,
				'prenom3' => null,
				'nomcomnai' => null,
				'dtnai' => '1956-12-05',
				'rgnai' => null,
				'typedtnai' => 'N',
				'nir' => null,
				'topvalec' => true,
				'sexe' => '2',
				'nati' => null,
				'dtnati' => null,
				'pieecpres' => null,
				'idassedic' => null,
				'numagenpoleemploi' => null,
				'dtinscpoleemploi' => null,
				'numfixe' => null,
				'numport' => null,
				'haspiecejointe' => null,
				'email' => null,
			),
			array(
				'foyer_id' => 2,
				'qual' => 'MR',
				'nom' => 'DURAND',
				'prenom' => 'RAOUL',
				'nomnai' => 'DURAND',
				'prenom2' => null,
				'prenom3' => null,
				'nomcomnai' => null,
				'dtnai' => '1950-05-07',
				'rgnai' => null,
				'typedtnai' => 'N',
				'nir' => null,
				'topvalec' => true,
				'sexe' => '1',
				'nati' => null,
				'dtnati' => null,
				'pieecpres' => null,
				'idassedic' => null,
				'numagenpoleemploi' => null,
				'dtinscpoleemploi' => null,
				'numfixe' => null,
				'numport' => null,
				'haspiecejointe' => null,
				'email' => null,
			),
			
			/**
			 * @see Model/CorrespondancepersonneTest.php
			 * @note En cas d'ajout ou retrait sur fixture, corriger les ids dans Correspondancepersonne
			 */
			array(
				'foyer_id' => 1,
				'qual' => 'MR',
				'nom' => 'FOO',
				'prenom' => 'BAR',
				'nomnai' => 'FOO',
				'prenom2' => null,
				'prenom3' => null,
				'nomcomnai' => null,
				'dtnai' => '1950-01-01',
				'rgnai' => null,
				'typedtnai' => 'N',
				'nir' => '150017505600118',
				'topvalec' => true,
				'sexe' => '1',
				'nati' => null,
				'dtnati' => null,
				'pieecpres' => null,
				'idassedic' => null,
				'numagenpoleemploi' => null,
				'dtinscpoleemploi' => null,
				'numfixe' => null,
				'numport' => null,
				'haspiecejointe' => null,
				'email' => null,
			),
			array(
				'foyer_id' => 2,
				'qual' => 'MR',
				'nom' => 'FOO',
				'prenom' => 'BAR',
				'nomnai' => 'FOO',
				'prenom2' => null,
				'prenom3' => null,
				'nomcomnai' => null,
				'dtnai' => '1950-01-01',
				'rgnai' => null,
				'typedtnai' => 'N',
				'nir' => '150017505600118',
				'topvalec' => true,
				'sexe' => '1',
				'nati' => null,
				'dtnati' => null,
				'pieecpres' => null,
				'idassedic' => null,
				'numagenpoleemploi' => null,
				'dtinscpoleemploi' => null,
				'numfixe' => null,
				'numport' => null,
				'haspiecejointe' => null,
				'email' => null,
			),
			array(
				'foyer_id' => 1,
				'qual' => 'MR',
				'nom' => 'FOO',
				'prenom' => 'BAZ',
				'nomnai' => 'FOO',
				'prenom2' => null,
				'prenom3' => null,
				'nomcomnai' => null,
				'dtnai' => '1951-01-01',
				'rgnai' => null,
				'typedtnai' => 'N',
				'nir' => '250017505600118',
				'topvalec' => true,
				'sexe' => '2',
				'nati' => null,
				'dtnati' => null,
				'pieecpres' => null,
				'idassedic' => null,
				'numagenpoleemploi' => null,
				'dtinscpoleemploi' => null,
				'numfixe' => null,
				'numport' => null,
				'haspiecejointe' => null,
				'email' => null,
			),
			array(
				'foyer_id' => 2,
				'qual' => 'MR',
				'nom' => 'FOO',
				'prenom' => 'BAZ',
				'nomnai' => 'FOO',
				'prenom2' => null,
				'prenom3' => null,
				'nomcomnai' => null,
				'dtnai' => '1952-01-01',
				'rgnai' => null,
				'typedtnai' => 'N',
				'nir' => '250017505600118',
				'topvalec' => true,
				'sexe' => '2',
				'nati' => null,
				'dtnati' => null,
				'pieecpres' => null,
				'idassedic' => null,
				'numagenpoleemploi' => null,
				'dtinscpoleemploi' => null,
				'numfixe' => null,
				'numport' => null,
				'haspiecejointe' => null,
				'email' => null,
			),
		);
	}
?>