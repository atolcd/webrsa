<?php
	/**
	 * Code source de la classe WebrsaRecherchesNonorientes66Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );
	App::uses( 'WebrsaCohortesNonorientes66', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesNonorientes66Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesNonorientes66Component extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Cohortes',
			'WebrsaRecherches',
			'WebrsaCohortesNonorientes66',
		);
		
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			return $this->WebrsaCohortesNonorientes66->{__FUNCTION__}($params);
		}
		
		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsRecords( array $params = array() ) {
			return $this->WebrsaCohortesNonorientes66->{__FUNCTION__}($params);
		}

		/**
		 * Retourne les noms des modèles dont des enregistrements seront mis en
		 * cache après l'appel à la méthode _optionsRecords() afin que la clé de
		 * cache générée par la méthode _options() se trouve associée dans
		 * ModelCache.
		 *
		 * @see _optionsRecords(), _options()
		 *
		 * @return array
		 */
		protected function _optionsRecordsModels( array $params ) {
			return $this->WebrsaCohortesNonorientes66->{__FUNCTION__}($params);
		}
	}
?>