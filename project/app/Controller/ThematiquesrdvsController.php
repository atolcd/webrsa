<?php
	/**
	 * Code source de la classe ThematiquesrdvsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ThematiquesrdvsController s'occupe du paramétrage des thématiques
	 * de rendez-vous.
	 *
	 * @package app.Controller
	 */
	class ThematiquesrdvsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Thematiquesrdvs';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Thematiquerdv' );

		/**
		 * Liste des thématiques de rendez-vous.
		 *
		 * @return void
		 */
		public function index() {
			$query = array(
				'contain' => array(
					'Statutrdv.libelle',
					'Typerdv.libelle'
				)
			);
			$this->WebrsaParametrages->index( $query, array( 'blacklist' => $this->blacklist ) );

			$options = $this->viewVars['options'];
			$options['Thematiquerdv']['linkedmodel'] = $this->Thematiquerdv->linkedModels();
			$this->set( compact( 'options' ) );
		}

		/**
		 * Formulaire de modification d'une thématique de rendez-vous.
		 *
		 * @throws NotFoundException
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Thematiquerdv']['statutrdv_id'] = $this->Thematiquerdv->Statutrdv->find( 'list', array( 'contain' => false, 'conditions' => array('actif' => 1), 'order' => array( 'libelle' ) ) );
			$options['Thematiquerdv']['typerdv_id'] = $this->Thematiquerdv->Typerdv->find( 'list', array( 'contain' => false, 'order' => array( 'libelle' ) ) );
			$options['Thematiquerdv']['linkedmodel'] = $this->Thematiquerdv->linkedModels();
			$this->set( compact( 'options' ) );
		}
	}
?>
