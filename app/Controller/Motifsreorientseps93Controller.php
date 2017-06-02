<?php
	/**
	 * Code source de la classe Motifsreorientseps93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Motifsreorientseps93Controller s'occupe du paramétrage des
	 * motifs des demandes de réorientation à passer en EP.
	 *
	 * @package app.Controller
	 */
	class Motifsreorientseps93Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Motifsreorientseps93';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Motifsreorientseps93:edit',
		);
	}
?>