<?php
    /**
     * Code source de la classe Motifscersnonvalids66Controller.
     *
     * PHP 5.3
     *
     * @package app.Controller
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

    /**
     * La classe Motifscersnonvalids66Controller s'occupe du paramétrage des motifs
	 * de non validation du CER.
     *
     * @package app.Controller
     */
    class Motifscersnonvalids66Controller extends AbstractWebrsaParametragesController
    {
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Motifscersnonvalids66';

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array( 'add' => 'Motifscersnonvalids66:edit' );
    }
?>
