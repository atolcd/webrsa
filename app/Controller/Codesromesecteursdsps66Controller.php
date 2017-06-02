<?php
	/**
	 * Code source de la classe Codesromesecteursdsps66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Codesromesecteursdsps66Controller s'occupe du paramétrage des
	 * codes ROME pour les secteurs.
	 *
	 * @package app.Controller
	 */
	class Codesromesecteursdsps66Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Codesromesecteursdsps66';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Codesromesecteursdsps66:edit'
		);
	}
?>