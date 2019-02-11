<?php
	/**
	 * Code source de la classe Sitescovs58Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Sitescovs58Controller s'occupe du paramétrage des sites d'actions
	 * médico-sociale  COV.
	 *
	 * @package app.Controller
	 */
	class Sitescovs58Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Sitescovs58';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Sitecov58' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Sitescovs58:edit'
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array( 'sitescovs58_zonesgeographiques' );

		/**
		 * Formulaire de modification d'un site COV.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$params = array(
				'query' => array(
					'contain' => array(
						'Zonegeographique'
					)
				),
				'view' => 'add_edit'
			);
			$this->WebrsaParametrages->edit( $id, $params );

			$options = $this->viewVars['options'];
			$options['Zonegeographique']['Zonegeographique'] = $this->Sitecov58->Zonegeographique->find( 'list' );
			$this->set( compact( 'options' ) );
		}
	}

?>