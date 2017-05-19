<?php
	/**
	 * Contrôleur de développement permettant de tester PgDbSnapshotComponent
	 */
	class PgDbSnapshotTnemesController extends AppController
	{
		/**
		 * @access public
		 */
		public $name = 'PgDbSnapshotTnemes';

		/**
		 * @access public
		 */
		public $helpers = array( );

		/**
		 * @access public
		 */
		public $uses = array( 'User' );

		/**
		 * @access public
		 */
		public $components = array( 'PgDbSnapshot.PgDbSnapshot' );

		/**
		 * Suppression de la vérification des droits d'accès à ce contrôleur.
		 *
		 * @return void
		 */
		public function beforeFilter() {
			$this->Auth->allow( '*' );
		}

		/**
		 * @url /pg_db_snapshot/pg_db_snapshot_tnemes/index
		 * @return void
		 */
		public function index() {
			$this->PgDbSnapshot->makeXmlDump( $this->User );
		}

	}
?>
