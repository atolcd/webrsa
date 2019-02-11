<?php
	/**
	 * Code source de la classe WebrsaCohortesContratsinsertionComponent.
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesContratsinsertionComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesContratsinsertionComponent extends WebrsaAbstractCohortesComponent
	{	
		/**
		 * Components utilisés par ce component
		 *
		 * @var array
		 */
		public $components = array( 'Allocataires', 'Gedooo.Gedooo', 'WebrsaRecherchesContratsinsertion' );
		
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params ) {
			return $this->WebrsaRecherchesContratsinsertion->{__FUNCTION__}($params);
		}

		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 *
		 * @return array
		 */
		protected function _optionsRecords( array $params ) {
			return $this->WebrsaRecherchesContratsinsertion->{__FUNCTION__}($params);
		}

		/**
		 * Retourne les noms des modèles dont des enregistrements seront mis en
		 * cache après l'appel à la méthode _optionsRecords() afin que la clé de
		 * cache générée par la méthode _options() se trouve associée dans
		 * ModelCache.
		 *
		 * @return array
		 */
		protected function _optionsRecordsModels( array $params ) {
			return $this->WebrsaRecherchesContratsinsertion->{__FUNCTION__}($params);
		}
	}
?>