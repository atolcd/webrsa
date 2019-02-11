<?php
	/**
	 * Code source de la classe Listesanctionseps58Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Listesanctionseps58Controller s'occupe du paramétrage des
	 * sanctions des EP.
	 *
	 * @package app.Controller
	 */
	class Listesanctionseps58Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Listesanctionseps58';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Listesanctionseps58:edit'
		);

		/**
		 * Liste des sanctions des EP.
		 */
		public function index() {
			$query = array(
				'order' => array( 'Listesanctionep58.rang ASC' )
			);
			$this->WebrsaParametrages->index( $query );

			$this->set( 'sanctionsValides', $this->Listesanctionep58->checkValideListe() );
		}
	}
?>