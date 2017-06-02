<?php
	/**
	 * Fichier source de la classe DescriptionspdosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe DescriptionspdosController s'occupe du paramétrage des
	 * descriptions des traitements PDO.
	 *
	 * @package app.Controller
	 */
	class DescriptionspdosController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Descriptionspdos';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Descriptionspdos:edit'
		);
	}
?>