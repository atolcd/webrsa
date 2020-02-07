<?php
	/**
	 * Code source de la classe.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohortePlanpauvreterendezvous', 'Model' );

	/**
	 * La classe...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvreterendezvousInfocolImprimeSecondRdvStock extends WebrsaCohortePlanpauvreterendezvous
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
			// Champs supplémentaire
			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Rendezvous.id',
					'Rendezvous.daterdv',
					'Rendezvous.heurerdv'
				)
			);

			// Conditions
			// Gestion du type de RDV
			$query['conditions'][] = "Rendezvous.typerdv_id = " . $this->getTypeRdvId ('cohorte_infocol_imprime_second_rdv_stock');
			$query['conditions'][] = "Rendezvous.statutrdv_id = " . $this->getStatutId('cohorte_infocol_imprime_second_rdv_stock');

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

			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Rendezvous.daterdv' );
			$query['conditions'] = $this->conditionsHeures( $query['conditions'], $search, 'Rendezvous.heurerdv' );

			return $query;
		}
	}
?>