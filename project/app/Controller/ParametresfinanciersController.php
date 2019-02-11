<?php
	/**
	 * Code source de la classe ParametresfinanciersController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ParametresfinanciersController s'occupe des paramètres financiers
	 * pour la gestion de l'APRE.
	 *
	 * @package app.Controller
	 */
	class ParametresfinanciersController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Parametresfinanciers';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array( 'add' => 'Parametresfinanciers:edit' );

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array( 'delete' );

		/**
		 * Surcharge de la liste des paramètres financiers pour la gestion de
		 * l'APRE car il s'agit c'est la visualisation d'un seul enregistrement.
		 */
		public function index() {
			$query = array(
				'fields' => $this->Parametrefinancier->fields(),
				'limit' => 1
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Surcharge de la modification des paramètres financiers pour la gestion
		 * de l'APRE.
		 */
		public function edit( $id = null ) {
			$id = 'add' === $this->action
				? null
				: array_keys( $this->Parametrefinancier->find( 'list', array( 'contain' => false ) ) );
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );
		}

		/**
		 * Surcharge de la méthode de suppression car il ne doit pas être
		 * possible de supprimer ce paramétrage.
		 *
		 * @param integer $id
		 * @throws NotFoundException
		 */
		public function delete( $id ) {
			throw new NotFoundException();
		}
	}
?>