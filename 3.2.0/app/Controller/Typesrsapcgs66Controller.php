<?php
    /**
     * Code source de la classe Typesrsapcgs66Controller.
     *
     * PHP 5.3
     *
     * @package app.Controller
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Typesrsapcgs66Controller s'occupe du paramétrage des types de RSA.
     *
     * @package app.Controller
     */
    class Typesrsapcgs66Controller extends AbstractWebrsaParametragesController
    {
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Typesrsapcgs66';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Typesrsapcgs66:edit'
		);
    }
?>
