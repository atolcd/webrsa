<?php
	/**
	 * Code source de la classe WebrsaCohortePlanpauvreteorientationsIsemploistock.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'WebrsaCohortePlanpauvreteorientations', 'Model' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohortePlanpauvreteorientationsIsemploistock ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvreteorientationsIsemploistock extends WebrsaCohortePlanpauvreteorientations
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohortePlanpauvreteorientationsIsemploistock';

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$query = parent::searchQuery($types);

			// Stock
			$query = $this->stock($query);

			return $query;
		}
	}
?>