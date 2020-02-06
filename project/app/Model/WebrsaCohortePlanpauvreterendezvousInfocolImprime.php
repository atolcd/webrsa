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
	class WebrsaCohortePlanpauvreterendezvousInfocolImprime extends WebrsaCohortePlanpauvreterendezvous
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohortePlanpauvreterendezvousInfocolImprime';

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
			$this->loadModel('Rendezvous');
			$config = Configure::read('ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime');
			$typeRdv = $this->Rendezvous->Typerdv->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'Typerdv.code_type' => $config['cohorte']['config']['Typerdv.code_type']
				)
			) );
			$query['conditions'][] = "Rendezvous.typerdv_id = " .$typeRdv['Typerdv']['id'];

			$statutRdv = $this->Rendezvous->Statutrdv->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'Statutrdv.code_statut' => $config['cohorte']['config']['Statutrdv.code_statut']
				)
			) );
			$query['conditions'][] = "Rendezvous.statutrdv_id = " . $statutRdv['Statutrdv']['id'];

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