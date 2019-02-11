<?php
	/**
	 * Fichier source de la classe TraitementstypespdosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe TraitementstypespdosController s'occupe du paramétrage des types de
	 * traitements PDO.
	 *
	 * @package app.Controller
	 */
	class TraitementstypespdosController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Traitementstypespdos';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Traitementstypespdos:edit'
		);
	}
?>