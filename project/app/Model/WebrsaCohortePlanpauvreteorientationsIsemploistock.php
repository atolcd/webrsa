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
			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Historiquedroit.created' );

			if ( $search ['Historiquedroit']['created'] ){
				//Modification du lien à Historiquedroit
				foreach ($query['joins'] as $key => $value) {
					debug( $value ) ;
					if ( $value['alias'] == 'Historiquedroit' ){
						$created_from =
							$search ['Historiquedroit']['created_from']['year'].'-'.
							$search ['Historiquedroit']['created_from']['month'].'-'.
							$search ['Historiquedroit']['created_from']['day'];
						$created_to =
							$search ['Historiquedroit']['created_to']['year'].'-'.
							$search ['Historiquedroit']['created_to']['month'].'-'.
							$search ['Historiquedroit']['created_to']['day'];
						$query['joins'][$key] =
						$this->Personne->join('Historiquedroit', array(
							'type' => 'INNER',
							'conditions' => array(
								'Historiquedroit.personne_id = Personne.id AND Personne.id IN(
									SELECT "Historiquedroit"."personne_id" from historiquesdroits as Historiquedroit 
									WHERE "Historiquedroit"."personne_id" = "Personne"."id"'
									.' AND date_trunc(\'day\', "Historiquedroit"."created") BETWEEN \''.$created_from.'\' AND \''.$created_to.'\' '
									.' ORDER BY "Historiquedroit"."created" DESC LIMIT 1)'
							)
						));
						break;
					}
				}
				// SDD & DOV sur historique
				$query = $this->sdddovHistorique($query);

				//Gestion de l'inscription Pole emploi
				$query['conditions'][] = '(date_trunc(\'day\', "Historiqueetatpe"."date") > date_trunc(\'day\', "Historiquedroit"."created")';

			}

			return $query;
		}
	}