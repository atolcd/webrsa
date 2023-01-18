<?php
	/**
	 * Code source de la classe WebrsaCohortePlanpauvreteorientationsIsemploistock.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohortePlanpauvreteorientations', 'Model' );

	/**
	 * La classe WebrsaCohortePlanpauvreteorientationsIsemploistock ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvreteorientationsIsemploistock extends WebrsaCohortePlanpauvreteorientations
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

			//Sans orientation
			$query = $this->sansOrientation($query);
			//Sans RDV
			$query = $this->sansRendezvous($query);
			//Sans CER
			$query = $this->sansCER($query);
			// Inscrit PE
			$query = $this->inscritPE($query);

			if(Configure::read('PlanPauvrete.Fileactive.PPAE')){
				//Uniquement les personnes qui ont un PPAE
				$query = $this->avecPPAE($query);
			}

			// Stock
			$query = $this->stock($query);

			return $query;
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

				if ( $search['Historiquedroit']['created'] ){
					//Modification du lien à Historiquedroit
					$query = $this->joinHistoriqueInDates($query, $search);

					// SDD & DOV sur historique
					$query = $this->sdddovHistorique($query);

					//Gestion de l'inscription Pole emploi
					$query['conditions'][] = '(
					"Historiqueetatpe"."date_modification" IS NULL
					OR date_trunc(\'day\', "Historiqueetatpe"."date_modification") > date_trunc(\'day\', "Historiquedroit"."created"))';
				}
			}

			return $query;
		}
	}