<?php
	/**
	 * Code source de la classe OriginespdosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe OriginespdosController s'occupe du paramétrage des
	 * fonctions des origines de PDO.
	 *
	 * @package app.Controller
	 */
	class OriginespdosController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Originespdos';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Originepdo' );
	}
?>