<?php
	/**
	 * Code source de la classe CategoriesutilisateursController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe CategoriesutilisateursController s'occupe du paramétrage des
	 * fonctions des membres des EP.
	 *
	 * @package app.Controller
	 */
	class CategoriesutilisateursController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Categoriesutilisateurs';

         /**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Categorieutilisateur' );

	}
?>