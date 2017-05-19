<?php
	/**
	 * Code source de la classe PropositionprimosController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AbstractParametragesController', 'Controller');
	App::import( 'Behaviors', 'Occurences' );

	/**
	 * La classe PropositionprimosController ...
	 *
	 * @package app.Controller
	 */
	class PropositionprimosController extends AbstractParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Propositionprimos';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Propositionprimo'
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 * 
		 * @var array
		 */
		public $aucunDroit = array(
			
		);
	}