<?php
	/**
	 * Code source de la classe ReferentFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * Classe ReferentFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class ReferentFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Referent',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'structurereferente_id' => 1,
				'nom' => 'Dupont',
				'prenom' => 'Martin',
				'numero_poste' => '0123455678',
				'email' => 'martin.dupont@moncg.fr',
				'qual' => 'MR',
				'fonction' => 'Référent',
				'actif' => 'O',
				'datecloture' => null,
			),
			array(
				'structurereferente_id' => 1,
				'nom' => 'Claude',
				'prenom' => 'Dupont',
				'numero_poste' => '0123455678',
				'email' => 'claude.martin@moncg.fr',
				'qual' => 'MR',
				'fonction' => 'Référent',
				'actif' => 'N',
				'datecloture' => null,
			),
		);

	}
?>