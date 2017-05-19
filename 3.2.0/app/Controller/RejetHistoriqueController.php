<?php
	// Fait par le CG93
	// Auteur : Harry ZARKA <hzarka@cg93.fr>, 2010.
	App::uses( 'AppController', 'Controller' );

	class RejetHistoriqueController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'RejetHistorique';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(

		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(

		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(

		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(

		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'affrej' => 'read',
			'affxml' => 'read',
		);

		public $scaffold;

		public function affrej( $fichier = null ) {
			$rejetHistorique = $this->RejetHistorique->findByFic( $fichier );
			$this->paginate = array( 'conditions' => array( 'RejetHistorique.fic' => $fichier ),
				'fields' => array( 'RejetHistorique.numdemrsa', 'RejetHistorique.matricule', 'RejetHistorique.log' ) );
			$this->set( 'rejetHistoriques', $this->paginate() );
			$this->set( 'fichier', $fichier );
		}

		public function affxml( $fichier = null, $nrsa = null ) {
			$rejet = $this->RejetHistorique->findByNumdemrsa( $nrsa );

			$xml = ($rejet['RejetHistorique']['balisededonnee']);

			$this->set( 'rejet', $xml );
			$this->set( 'nrsa', $nrsa );
		}
	}
?>