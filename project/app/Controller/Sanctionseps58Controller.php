<?php
	/**
	 * Fichier source de la classe Sanctionseps58Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaAccessContratsinsertion', 'Utility' );

	/**
	* Gestion des dossiers d'EP pour "Non respect et sanctions" (CG 58).
	 *
	 * @package app.Controller
	 */
	class Sanctionseps58Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Sanctionseps58';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Cohortes',
			'DossiersMenus',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'cohorte_radiespe' => array('filter' => 'Search'),
					'cohorte_noninscritspe' => array('filter' => 'Search')
				),
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Allocataires',
			'Csv',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Sanctionep58',
			'Contratinsertion',
			'WebrsaContratinsertion',
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
			'cohorte_noninscritspe' => 'create',
			'cohorte_radiespe' => 'create',
			'deleteNonrespectcer' => 'delete',
			'exportcsv_noninscritspe' => 'read',
			'exportcsv_radiespe' => 'read',
			'nonrespectcer' => 'create',
			'deleteNonrespectppae' => 'delete',
		);

		/**
		 *
		 * @param integer $contratinsertion_id
		 */
		public function nonrespectcer( $contratinsertion_id ) {
			$this->_checkAccess($contratinsertion_id);

			$contratinsertion = $this->Sanctionep58->Dossierep->Personne->Contratinsertion->find(
				'first',
				array(
					'fields' => array(
						'Contratinsertion.id',
						'Contratinsertion.personne_id'
					),
					'conditions' => array(
						'Contratinsertion.id' => $contratinsertion_id
					),
					'contain' => false
				)
			);

			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $contratinsertion['Contratinsertion']['personne_id'] ) );

			$success = true;
			$this->Sanctionep58->begin();

			$dossierep = array(
				'Dossierep' => array(
					'themeep' => 'sanctionseps58',
					'personne_id' => $contratinsertion['Contratinsertion']['personne_id']
				)
			);
			$this->Sanctionep58->Dossierep->create( $dossierep );
			$success = $this->Sanctionep58->Dossierep->save( null, array( 'atomic' => false ) ) && $success;

			$sanctionep58 = array(
				'Sanctionep58' => array(
					'dossierep_id' => $this->Sanctionep58->Dossierep->id,
					'origine' => 'nonrespectcer',
					'contratinsertion_id' => $contratinsertion['Contratinsertion']['id']
				)
			);

			$this->Sanctionep58->create( $sanctionep58 );
			$success = $this->Sanctionep58->save( null, array( 'atomic' => false ) ) && $success;

			if( $success ) {
				$this->Sanctionep58->commit();
				$this->Flash->success( __( 'Save->success' ) );
			}
			else {
				$this->Sanctionep58->rollback();
				$this->Flash->error( __( 'Save->error' ) );
			}

			$this->redirect( array( 'controller' => 'contratsinsertion', 'action' => 'index', $contratinsertion['Contratinsertion']['personne_id'] ) );
		}

		/**
		 *
		 * @param integer $sanctionep58_id
		 */
		public function deleteNonrespectcer( $sanctionep58_id ) {
			$this->_deleteSanctionNonrespect($sanctionep58_id);
		}

		/**
		 *
		 * @param integer $sanctionep58_id
		 */
		public function deleteNonrespectppae( $sanctionep58_id ) {
			$this->_deleteSanctionNonrespect($sanctionep58_id, 'orientsstructs');
		}

		/**
		 * Supprime une sanction (CER ou PPAE)
		 * @param int $sanctionep58_id
		 * @param string $controller
		 */
		protected function _deleteSanctionNonrespect($sanctionep58_id, $controller = 'contratsinsertion') {
			$dossierep = $this->Sanctionep58->find(
				'first',
				array(
					'conditions' => array(
						'Sanctionep58.id' => $sanctionep58_id
					),
					'contain' => array(
						'Dossierep'
					)
				)
			);

			if ($controller == 'contratsinsertion') {
				$this->_checkAccess(Hash::get($dossierep, 'Sanctionep58.contratinsertion_id'));
			}

			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Sanctionep58->Dossierep->personneId( $dossierep['Sanctionep58']['dossierep_id'] ) ) );

			$success = true;
			$this->Sanctionep58->begin();

			$success = $this->Sanctionep58->delete( $dossierep['Sanctionep58']['id'] ) && $success;
			$success = $this->Sanctionep58->Dossierep->delete( $dossierep['Dossierep']['id'] ) && $success;

			if( $success ) {
				$this->Sanctionep58->commit();
				$this->Flash->success( __( 'Save->success' ) );
			}
			else {
				$this->Sanctionep58->rollback();
				$this->Flash->error( __( 'Save->error' ) );
			}

			$this->redirect( array( 'controller' => $controller, 'action' => 'index', $dossierep['Dossierep']['personne_id'] ) );
		}

		/**
		 * Cohorte de sélection des allocataires radiés de Pôle Emploi.
		 */
		public function cohorte_radiespe() {
			$this->loadModel('Personne');

			$Cohortes = $this->Components->load( 'WebrsaCohortesSanctionseps58' );

			$Cohortes->cohorte(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohorteSanctionep58Radiepe'
				)
			);
		}

		/**
		 * Export CSV desc résultats de la cohorte de sélection des allocataires
		 * radiés de Pôle Emploi.
		 */
		public function exportcsv_radiespe() {
			$this->loadModel('Personne');

			$Cohortes = $this->Components->load( 'WebrsaCohortesSanctionseps58' );

			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohorteSanctionep58Radiepe'
				)
			);
		}

		/**
		 * Cohorte de sélection des allocataires non inscrits à Pôle Emploi
		 *.
		 */
		public function cohorte_noninscritspe() {
			$this->loadModel('Personne');

			$Cohortes = $this->Components->load( 'WebrsaCohortesSanctionseps58' );

			$Cohortes->cohorte(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohorteSanctionep58Noninscritpe',
					'auto' => true
				)
			);
		}

		/**
		 * Export CSV desc résultats de la cohorte de sélection des allocataires
		 * non inscrits à Pôle Emploi.
		 */
		public function exportcsv_noninscritspe() {
			$this->loadModel('Personne');

			$Cohortes = $this->Components->load( 'WebrsaCohortesSanctionseps58' );

			$Cohortes->exportcsv(
				array(
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohorteSanctionep58Noninscritpe'
				)
			);
		}

		/**
		 * Fait appel à WebrsaAccessContratsinsertion pour vérifier les droits d'accès
		 * à une action en fonction d'un enregistrement
		 *
		 * @see ContratsinsertionController::_checkAccess
		 * @param integer $contratinsertion_id
		 */
		protected function _checkAccess($contratinsertion_id) {
			$personne_id = $this->Contratinsertion->personneId($contratinsertion_id);
			$querydata = $this->WebrsaContratinsertion->qdThematiqueEp('Sanctionep58', $personne_id);
            $querydata['fields'] = 'Sanctionep58.id';
            $sanctionseps58 = $this->Sanctionep58->Dossierep->find('first', $querydata);

			$params = $this->WebrsaContratinsertion->haveNeededDatas($personne_id);
			$params['erreursCandidatePassage'] = $this->Sanctionep58->Dossierep->getErreursCandidatePassage($personne_id);
			$params['haveSanctionep'] = !empty($sanctionseps58);

			$records = $this->WebrsaContratinsertion->getDataForAccess(array('Contratinsertion.id' => $contratinsertion_id));
			$record = end($records);

			$redirectUrl = array('controller' => 'Contratsinsertion', 'action' => 'index', $personne_id);
			$msgstr = 'Impossible d\'effectuer cette action.';

			if (!WebrsaAccessContratsinsertion::check($this->name, $this->action, $record, $params)) {
				$this->Flash->error($msgstr);
				$this->redirect($redirectUrl);
			}
		}
	}
?>