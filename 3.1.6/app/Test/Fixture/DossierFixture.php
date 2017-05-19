<?php
	/**
	 * Code source de la classe DossierFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * Classe DossierFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class DossierFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Dossier',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'numdemrsa' => '66666666693',
				'dtdemrsa' => '2009-09-01',
				'dtdemrmi' => null,
				'numdepinsrmi' => null,
				'typeinsrmi' => null,
				'numcominsrmi' => null,
				'numagrinsrmi' => null,
				'numdosinsrmi' => null,
				'numcli' => null,
				'numorg' => '931',
				'fonorg' => 'CAF',
				'matricule' => '123456700000000',
				'statudemrsa' => null,
				'typeparte' => 'CG',
				'ideparte' => '093',
				'fonorgcedmut' => null,
				'numorgcedmut' => null,
				'matriculeorgcedmut' => null,
				'ddarrmut' => null,
				'codeposanchab' => null,
				'fonorgprenmut' => null,
				'numorgprenmut' => null,
				'dddepamut' => null,
				'detaildroitrsa_id' => null,
				'avispcgdroitrsa_id' => null,
				'organisme_id' => null,
			),
			array(
				'numdemrsa' => '77777777793',
				'dtdemrsa' => '2010-07-12',
				'dtdemrmi' => null,
				'numdepinsrmi' => null,
				'typeinsrmi' => null,
				'numcominsrmi' => null,
				'numagrinsrmi' => null,
				'numdosinsrmi' => null,
				'numcli' => null,
				'numorg' => '931',
				'fonorg' => 'CAF',
				'matricule' => '987654321000000',
				'statudemrsa' => null,
				'typeparte' => 'CG',
				'ideparte' => '093',
				'fonorgcedmut' => null,
				'numorgcedmut' => null,
				'matriculeorgcedmut' => null,
				'ddarrmut' => null,
				'codeposanchab' => null,
				'fonorgprenmut' => null,
				'numorgprenmut' => null,
				'dddepamut' => null,
				'detaildroitrsa_id' => null,
				'avispcgdroitrsa_id' => null,
				'organisme_id' => null,
			),
		);

		/**
		 * Création de la séquence dossiers_numdemrsatemp_seq.
		 *
		 * @param Object $Db
		 * @return boolean
		 */
		public function create( $Db ) {
			$return = parent::create( $Db );
			$sql = 'CREATE SEQUENCE dossiers_numdemrsatemp_seq START 1;';
			return $return && ( $this->_query( $Db, $sql, true ) !== false );
		}

		/**
		 * Suppression de la séquence dossiers_numdemrsatemp_seq.
		 *
		 * @param Object $Db
		 * @return boolean
		 */
		public function drop( $Db ) {
			$return = parent::drop( $Db );
			$sql = 'DROP SEQUENCE IF EXISTS dossiers_numdemrsatemp_seq;';
			return $return && ( $this->_query( $Db, $sql, true ) !== false );
		}
	}
?>