<?php
	/**
	 * Code source de la classe Commentairesnormescers93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Commentairesnormescers93Controller s'occupe du paramétrage des
	 * commentaires normés pour les décisions du CER du CD 93.
	 *
	 * @package app.Controller
	 */
	class Commentairesnormescers93Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Commentairesnormescers93';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Commentairenormecer93' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Commentairesnormescers93:edit'
		);
	}
?>
