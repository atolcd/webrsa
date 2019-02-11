<?php
	/**
	 * Code source de la classe Raisonssocialespartenairescuis66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
    App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Raisonssocialespartenairescuis66Controller s'occupe du paramétrage
	 * des motifs de raisons sociales des partenaires du CUI du CD 66.
	 * @package app.Controller
	 */
	class Raisonssocialespartenairescuis66Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Raisonssocialespartenairescuis66';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Raisonsocialepartenairecui66' );

	}
?>
