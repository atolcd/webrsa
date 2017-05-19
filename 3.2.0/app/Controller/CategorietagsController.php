<?php
	/**
	 * Code source de la classe CategorietagsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe CategorietagsController s'occupe du paramétrage des catégories
	 * de tags.
	 *
	 * @package app.Controller
	 */
	class CategorietagsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Categorietags';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Categorietag' );
	}
?>
