<?php
	/**
	 * Code source de la classe WebrsaCohortePlanpauvreterendezvousInfocolStock.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohortePlanpauvreterendezvous', 'Model' );

	/**
	 * La classe ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvreterendezvousInfocolStock extends WebrsaCohortePlanpauvreterendezvous
	{
		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array(), $nouvelentrant = false ) {
			$query = parent::searchQuery($types, $nouvelentrant);

			// Conditions
			// SDD & DOV sur historique
			$query = $this->sdddovHistorique($query);
			// Sans RDV
			$query = $this->sansRendezvous($query);

			// Sans Orientation
			$query = $this->sansOrientation($query);

			// Sans CER
			$query = $this->sansCER($query);

			if(Configure::read('PlanPauvrete.Fileactive.PPAE')){
				// Non inscrit PE ou inscrit PE sans PPAE
				$query = $this->nonInscritPEouInscritPEsansPPAE($query);
			} else {
				// Non inscrit PE
				$query = $this->nonInscritPE($query);
			}

			//Dans le mois précédent :
			$query = $this->stock($query);

			return $query;
		}

		/**
		 * Logique de sauvegarde de la cohorte
		 *
		 * @param type $data
		 * @param type $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$params['nom_cohorte'] = 'cohorte_infocol_stock';
			$success = parent::saveCohorte($data, $params, $user_id);

			return $success;
		}

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			$query = parent::searchConditions($query, $search);

			if ( isset($search['Historiquedroit']['created']) ){
				$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Historiquedroit.created' );

				if ( $search ['Historiquedroit']['created'] ){
					//Modification du lien à Historiquedroit
					$query = $this->joinHistoriqueInDates($query, $search);

					// SDD & DOV sur historique
					$query = $this->sdddovHistorique($query);

				}
			}

			return $query;
		}
	}