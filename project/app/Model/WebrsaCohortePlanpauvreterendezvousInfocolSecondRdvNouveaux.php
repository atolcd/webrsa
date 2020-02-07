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
	class WebrsaCohortePlanpauvreterendezvousInfocolSecondRdvNouveaux extends WebrsaCohortePlanpauvreterendezvous
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
			$query['conditions'][] = "Rendezvous.typerdv_id = " . $this->getTypeRdvId ('cohorte_infocol_second_rdv_nouveau');
			$statutRdv = $this->Rendezvous->Statutrdv->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'Statutrdv.code_statut' => 'NONVENU'
				)
            ) );
            $query['conditions'][] = "Rendezvous.statutrdv_id = " . $statutRdv['Statutrdv']['id'];


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

        /**
		 * Logique de sauvegarde de la cohorte
		 *
		 * @param type $data
		 * @param type $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$params['nom_cohorte'] = 'cohorte_infocol_second_rdv_nouveaux';
			$success = parent::saveCohorte($data, $params, $user_id);

			return $success;
		}
	}
?>