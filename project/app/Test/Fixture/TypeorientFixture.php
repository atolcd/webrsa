<?php
	/**
	 * Code source de la classe TypeorientFixture.
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * La classe TypeorientFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class TypeorientFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Typeorient',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'parentid' => null,
				'lib_type_orient' => 'Socioprofessionnelle',
				'modele_notif' => 'proposition_orientation_vers_SS_ou_PDV',
				'modele_notif_cohorte' => 'proposition_orientation_vers_SS_ou_PDV_cohorte',
				'actif' => 'O',
			),
			array(
				'parentid' => null,
				'lib_type_orient' => 'Social',
				'modele_notif' => 'proposition_orientation_vers_SS_ou_PDV',
				'modele_notif_cohorte' => 'proposition_orientation_vers_SS_ou_PDV_cohorte',
				'actif' => 'O',
			),
			array(
				'parentid' => null,
				'lib_type_orient' => 'Emploi',
				'modele_notif' => 'proposition_orientation_vers_pole_emploi',
				'modele_notif_cohorte' => 'proposition_orientation_vers_pole_emploi_cohorte',
				'actif' => 'O',
			),
			array(
				'parentid' => null,
				'lib_type_orient' => 'Foo',
				'modele_notif' => 'proposition_orientation_vers_foo',
				'modele_notif_cohorte' => 'proposition_orientation_vers_foo_cohorte',
				'actif' => 'N',
			),
		);
	}
?>