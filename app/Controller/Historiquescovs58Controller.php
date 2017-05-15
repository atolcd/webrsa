<?php
	/**
	 * Code source de la classe Historiquescovs58Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe Historiquescovs58Controller ...
	 *
	 * @package app.Controller
	 */
	class Historiquescovs58Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Historiquescovs58';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dossiercov58',
			'WebrsaDossiercov58',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
			'view' => 'read',
		);

		/**
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$params = array( 'themes' => false, 'decisions' => false );

			$query = $this->WebrsaDossiercov58->getQuery( $params );
			$query['fields'] = array(
				'Cov58.datecommission',
				'Dossiercov58.themecov58',
				'Passagecov58.etatdossiercov',
				'Sitecov58.name',
				'Dossiercov58.created',
				'Passagecov58.id',
				'Cov58.id',
				'Cov58.etatcov'
			);
			$query['order'] = array(
				'Cov58.datecommission IS NOT NULL',
				'Cov58.datecommission DESC',
				'Dossiercov58.created'
			);
			$query['conditions']['Dossiercov58.personne_id'] = $personne_id;
			$results = $this->Dossiercov58->find( 'all', $query );

			$options = $this->WebrsaDossiercov58->options( $params );

			$this->set( compact( 'results', 'options' ) );
		}

		/**
		 * Visualisation d'un passage en COV donné et de la décision associée.
		 *
		 * @param integer $passagecov58_id
		 */
		public function view( $passagecov58_id ) {
			$personne_id = $this->Dossiercov58->Passagecov58->personneId( $passagecov58_id );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$path = "ConfigurableQuery.{$this->name}.{$this->action}";
			$keys = array( "{$path}.common" );
			$themes = array_keys( (array)Hash::get( $this->Dossiercov58->enums(), 'Dossiercov58.themecov58' ) );
			foreach( $themes as $theme ) {
				$keys[] = "{$path}.{$theme}";
				$keys[] = "{$path}.decisions{$theme}";
			}
			$query = $this->WebrsaDossiercov58->getQuery();
			$query = ConfigurableQueryFields::getFieldsByKeys( $keys, $query );

			$query['conditions']['Passagecov58.id'] = $passagecov58_id;
			$this->Dossiercov58->forceVirtualFields = true;
			$record = $this->Dossiercov58->find( 'first', $query );

			$options = $this->WebrsaDossiercov58->options();
			$fields = (array)Configure::read( $path );

			$this->set( compact( 'record', 'options', 'fields' ) );
			$this->set( 'urlmenu', "/historiquescovs58/index/{$personne_id}" );
		}
	}
?>
