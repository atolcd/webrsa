<?php
	/**
	 * Code source de la classe MotiffichedeliaisonsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AbstractParametragesController', 'Controller');
	App::import( 'Behaviors', 'Occurences' );

	/**
	 * La classe MotiffichedeliaisonsController ...
	 *
	 * @package app.Controller
	 */
	class MotiffichedeliaisonsController extends AbstractParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Motiffichedeliaisons';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Motiffichedeliaison'
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 * 
		 * @var array
		 */
		public $aucunDroit = array(
			
		);
	}