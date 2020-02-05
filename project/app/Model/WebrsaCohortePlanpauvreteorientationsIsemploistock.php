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

			//Dans le mois précédent : Nouvelle demande ou Réouverture de droit
			$dateDebRecherche = date('Y-m-',strtotime("-1 month")).Configure::read( 'PlanPauvrete.Cohorte.Moisprecedent.deb' );
			$dateFinRecherche = date('Y-m-').Configure::read( 'PlanPauvrete.Cohorte.Moisprecedent.fin' );
			//Recherche selon Stock
			$query['conditions'][] = 'date_trunc(\'day\', Historiquedroit.created) < \''.$dateDebRecherche.'\'';
			$query['conditions'][] = 'date_trunc(\'day\', Historiquedroit.modified) > \''.$dateFinRecherche.'\'';

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