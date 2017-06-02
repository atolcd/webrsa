<?php
	/**
	 * Code source de la classe MetiersexercesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe MetiersexercesController s'occupe du paramétrage des métiers
	 * exercés pour le CER du CD 93.
	 *
	 * @package app.Controller
	 */
	class MetiersexercesController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Metiersexerces';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Metierexerce' );
	}
?>
