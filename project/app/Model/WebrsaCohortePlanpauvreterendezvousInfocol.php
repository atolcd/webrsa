<?php
	/**
	 * Code source de la classe WebrsaCohortePlanpauvreterendezvous.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'WebrsaCohortePlanpauvreterendezvous', 'Model' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohortePlanpauvreterendezvous ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvreterendezvousInfocol extends WebrsaCohortePlanpauvreterendezvous
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohortePlanpauvreterendezvousInfocol';

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$query = parent::searchQuery($types);
			// Conditions
			// Sans RDV
			$query['conditions'][] = 'NOT EXISTS(
				SELECT "rendezvous"."id" AS "rendezvous__id"
				FROM rendezvous AS rendezvous
				WHERE "rendezvous"."personne_id" = "Personne"."id" )';

			// Sans Orientation
			$query['conditions'][] = 'NOT EXISTS(
				SELECT "orientsstructs"."id" AS "orientsstructs__id"
				FROM orientsstructs AS orientsstructs
				WHERE "orientsstructs"."statut_orient" = \'Orienté\'
				AND "orientsstructs"."personne_id" = "Personne"."id" )';

			// Sans CER
			$query['conditions'][] = 'NOT EXISTS(
				SELECT "contratsinsertion"."id" AS "contratsinsertion__id"
				FROM contratsinsertion AS contratsinsertion
				WHERE "contratsinsertion"."decision_ci" = \'V\'
				AND "contratsinsertion"."personne_id" = "Personne"."id" )';

			//Dans le mois précédent :
			$dateDebRecherche = date('Y-m-',strtotime("-2 month")).Configure::read( 'PlanPauvrete.Cohorte.Moisprecedent.deb' );
			$dateFinRecherche = date('Y-m-',strtotime("-1 month")).Configure::read( 'PlanPauvrete.Cohorte.Moisprecedent.fin' );
			$query['conditions'][] = 'Historiquedroit.created BETWEEN \''.$dateDebRecherche.'\' AND \''.$dateFinRecherche.'\'';

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
			$params['nom_cohorte'] = 'cohorte_infocol';
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
			return $query;
		}
	}
?>