<?php
    /**
     * Code source de la classe Progsfichescandidatures66Controller.
     *
     * PHP 5.3
     *
     * @package app.Controller
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe MotifssortieController s'occupe du paramétrage programmes région
	 * du CD 66.
	 *
	 * @package app.Controller
	 */
    class Progsfichescandidatures66Controller extends AbstractWebrsaParametragesController
    {
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Progsfichescandidatures66';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Progfichecandidature66' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Progsfichescandidatures66:edit'
		);
    }
?>