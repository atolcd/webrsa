<?php
	/**
	 * Code source de la classe RecoursController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe RecoursController ...
	 *
	 * @package app.Controller
	 */
	class RecoursController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Recours';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(

		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Infofinanciere',
			'Avispcgdroitrsa',
			'Option',
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
			'contentieux' => 'update',
			'gracieux' => 'update',
		);

		/**
		 *
		 */
		public function beforeFilter() {
			parent::beforeFilter();
			$this->set( 'commission', ClassRegistry::init('Recours')->enum('typecommission') );
			$this->set( 'decisionrecours', $this->Option->decisionrecours() );
			$this->set( 'motifrecours', $this->Option->motifrecours() );
		}

		/**
		 *
		 * @param integer $dossier_id
		 */
		public function gracieux( $dossier_id = null ) {
			$this->assert( valid_int( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$gracieux = $this->Avispcgdroitrsa->find(
					'first', array(
				'conditions' => array(
					'Avispcgdroitrsa.dossier_id' => $dossier_id
				),
				'recursive' => -1
					)
			);

			$qd_avispcg = array(
				'conditions' => array(
					'Avispcgdroitrsa.dossier_id' => $dossier_id
				)
			);
			$avispcg = $this->Avispcgdroitrsa->find( 'first', $qd_avispcg );
debug($avispcg);

			$this->set( 'dossier_id', $dossier_id );
			$this->set( 'urlmenu', '/indus/index/'.$dossier_id );
			$this->set( 'avispcg', $avispcg );
			$this->set( 'gracieux', $gracieux );
		}

		/**
		 *
		 * @param integer $dossier_id
		 */
		public function contentieux( $dossier_id = null ) {
			$this->assert( valid_int( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$contentieux = $this->Avispcgdroitrsa->find(
					'first', array(
				'conditions' => array(
					'Avispcgdroitrsa.dossier_id' => $dossier_id
				),
				'recursive' => -1
					)
			);

			$this->set( 'dossier_id', $dossier_id );
			$this->set( 'urlmenu', '/indus/index/'.$dossier_id );
			$this->set( 'contentieux', $contentieux );
		}

	}
?>