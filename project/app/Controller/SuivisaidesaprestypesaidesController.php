<?php
	/**
	 * Code source de la classe SuivisaidesaprestypesaidesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe SuivisaidesaprestypesaidesController s'occupe du paramétrage des
	 * types d'aides liées aux personnes chargées du suivi de l'APRE.
	 *
	 * @package app.Controller
	 */
	class SuivisaidesaprestypesaidesController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Suivisaidesaprestypesaides';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Suiviaideapretypeaide', 'Option', 'WebrsaApre' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array( 'add' => 'Suivisaidesaprestypesaides:edit' );

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array( 'delete' );

		/**
		 * Surcharge de la liste des types d'aides liées aux personnes chargées
		 * du suivi de l'APRE pour ajouter les informations de la personne
		 * chargée du suivi.
		 */
		public function index() {
			$query = array(
				'fields' => array_merge(
					$this->Suiviaideapretypeaide->fields(),
					array(
						'Suiviaideapre.nom_complet'
					)
				),
				'joins' => array(
					$this->Suiviaideapretypeaide->join( 'Suiviaideapre', array( 'type' => 'INNER' ) )
				)
			);
			$this->Suiviaideapretypeaide->forceVirtualFields = true;
			$this->WebrsaParametrages->index( $query, array( 'blacklist' => $this->blacklist ) );
		}

		/**
		 * Surcharge de la modification des types d'aides liées aux personnes
		 * chargées du suivi de l'APRE pour manipuler tous les types d'aide en
		 * une fois..
		 */
		public function edit( $id = null ) {
			$id = 'add' === $this->action
				? null
				: array_keys( $this->Suiviaideapretypeaide->find( 'list', array( 'contain' => false ) ) );
			$this->WebrsaParametrages->edit( $id, array( 'method' => 'saveAll', 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Suiviaideapretypeaide']['suiviaideapre_id'] = $this->Suiviaideapretypeaide->Suiviaideapre->find( 'list' );
			$this->set( compact( 'options' ) );
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