<?php
	/**
	 * Code source de la classe WebrsaCohortePlanpauvreteorientationsIsemploi.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohortePlanpauvreteorientations', 'Model' );

	/**
	 * La classe WebrsaCohortePlanpauvreteorientationsIsemploi ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvreteorientationsIsemploi extends WebrsaCohortePlanpauvreteorientations
	{
		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$query = parent::searchQuery($types);

			//Sans orientation
			$query = $this->sansOrientation($query);
			//Sans RDV
			$query = $this->sansRendezvous($query);
			//Sans CER
			$query = $this->sansCER($query);
			// Inscrit PE
			$query = $this->inscritPE($query);

			//Dans le mois précédent :
			$query = $this->nouveauxEntrants($query);

			return $query;
		}
	}