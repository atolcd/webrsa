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
		public function searchQuery( array $types = array(),  $nouvelentrant = true ) {
			$query = parent::searchQuery($types, $nouvelentrant);

			// SDD & DOV
			$query = $this->sdddovHistorique($query);

			//Sans orientation
			$query = $this->sansOrientation($query, true);
			//Sans RDV
			$query = $this->sansRendezvous($query, true);
			//Sans CER
			$query = $this->sansCER($query, true);
			//Inscrit PE
			$query = $this->inscritPE($query);

			if(Configure::read('PlanPauvrete.Nouveauxentrants.PPAE')){
				//Uniquement les personnes qui ont un PPAE
				$query = $this->avecPPAE($query);
			}


			//Uniquement les personne qui sont SDDOV pour la première fois.
			if( Configure::read('PlanPauvrete.Cohorte.Primoaccedant') ) {
				$query = $this->uniqueHistoriqueSdddov($query);
			}

			//Uniquement les personnes dont l'état PE est mis a jour ce mois ci
			$query = $this->dateInscritPESupPeriode($query);

			//Dans le mois précédent :
			$query = $this->nouveauxEntrants($query);

			return $query;
		}
	}