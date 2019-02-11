<?php
	/**
	 * Code source de la classe RegroupementsepsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe RegroupementsepsController s'occupe du paramétrage des
	 * regroupements des EP.
	 *
	 * @package app.Controller
	 */
	class RegroupementsepsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Regroupementseps';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Regroupementseps:edit'
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array( 'compositionsregroupementseps' );

		/**
		 * Liste des regroupements d'EP.
		 */
		public function index() {
			$this->WebrsaParametrages->index( array(), array( 'blacklist' => $this->blacklist ) );

			$options = $this->viewVars['options'];
			$options['Regroupementep']['themes'] = $this->Regroupementep->themes();
			$this->set( compact( 'options' ) );
		}
	}
?>