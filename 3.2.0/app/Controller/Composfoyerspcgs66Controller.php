<?php
	/**
	 * Code source de la classe Composfoyerspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Composfoyerspcgs66Controller s'occupe du paramétrage des
	 * compositions des foyers PCG.
	 *
	 * @package app.Controller
	 */
	class Composfoyerspcgs66Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Composfoyerspcgs66';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Composfoyerspcgs66:edit'
		);
	}
?>