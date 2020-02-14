<?php
	/**
	 * Code source de la classe WebrsaCohortePlanpauvrete.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohortePlanpauvrete ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvrete extends AbstractWebrsaCohorte
	{

		/**
		 * Ajoute la condition dans la query pour ne pas avoir de CER
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function sansCER($query) {
			$query['conditions'][] = 'NOT EXISTS(
				SELECT "contratsinsertion"."id" AS "contratsinsertion__id"
				FROM contratsinsertion AS contratsinsertion
				INNER JOIN historiquesdroits ON (historiquesdroits.personne_id = contratsinsertion.personne_id AND historiquesdroits.created <= contratsinsertion.datevalidation_ci )
				WHERE "contratsinsertion"."decision_ci" = \'V\'
				AND "contratsinsertion"."personne_id" = "Personne"."id"
				)';
			return $query;
		}

		/**
		 * Base de la requete des orientations
		 */
		public function requeteOrientation() {
			$conditions = 'SELECT "orientsstructs"."id" AS "orientsstructs__id"
				FROM orientsstructs AS orientsstructs
				WHERE "orientsstructs"."statut_orient" = \'Orienté\'
				AND "orientsstructs"."personne_id" = "Personne"."id"';
			return $conditions;
		}

		/**
		 * Ajoute la condition dans la query pour ne pas avoir d'orientation
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function sansOrientation($query) {
			$conditions = $this->requeteOrientation();
			$query['conditions'][] = ' NOT EXISTS('.$conditions.')';
			return $query;
		}

		/**
		 * Ajoute la condition dans la query pour avoir une d'orientation
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function avecOrientation($query) {
			$conditions = $this->requeteOrientation();

			//Recupération des type d'orientation voulues
			$limitation = Configure::read ('PlanPauvrete.Cohorte.Orientations.Limite');
			if ( isset( $limitation['structureorientante_id'] ) ){
				$conditions .= ' AND "orientsstructs"."structureorientante_id" = '. $limitation['structureorientante_id'];
			}
			if ( isset( $limitation['typeorient_id'] ) ){
				$conditions .= ' AND "orientsstructs"."typeorient_id" = '. $limitation['typeorient_id'];
			}
			if ( isset( $limitation['typenotification'] ) ){
				$conditions .= ' AND "orientsstructs"."typenotification" = '. $limitation['typenotification'];
			}

			$query['conditions'][] = ' EXISTS('.$conditions.')';
			return $query;
		}

		/**
		 * Ajoute la condition dans la query pour ne pas avoir de rendez vous
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function sansRendezvous($query) {
			$query['conditions'][] = 'NOT EXISTS(
				SELECT "rendezvous"."id" AS "rendezvous__id"
				FROM rendezvous AS rendezvous
				INNER JOIN historiquesdroits ON (historiquesdroits.personne_id = Rendezvous.personne_id AND historiquesdroits.created <= Rendezvous.daterdv)
				WHERE "rendezvous"."personne_id" = "Personne"."id"
				 )';
			return $query;
		}

		/**
		 * Ajoute la condition pour n'avoir que les nouveaux entrants
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function nouveauxEntrants($query) {
			$moisDeb = date('j') >= Configure::read( 'PlanPauvrete.Cohorte.Moisprecedent.deb' ) ? "-1 month" : "-2 month";
			$moisFin = $moisDeb === "-2 month" ? "-1 month" : "now";

			$dateDebRecherche = date('Y-m-',strtotime($moisDeb)).Configure::read( 'PlanPauvrete.Cohorte.Moisprecedent.deb' );
			$dateFinRecherche = date('Y-m-',strtotime($moisFin)).Configure::read( 'PlanPauvrete.Cohorte.Moisprecedent.fin' );

			$query['conditions'][] = 'Historiquedroit.created BETWEEN \''.$dateDebRecherche.'\' AND \''.$dateFinRecherche.'\'';
			return $query;
		}

		/**
		 * Ajoute la condition pour n'avoir que les nouveaux entrants
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function stock($query) {

			//Dans le mois précédent : Nouvelle demande ou Réouverture de droit
			$dateDebRecherche = date('Y-m-',strtotime("-1 month")).Configure::read( 'PlanPauvrete.Cohorte.Moisprecedent.deb' );
			//Recherche selon Stock
			$query['conditions'][] = 'date_trunc(\'day\', Historiquedroit.created) < \''.$dateDebRecherche.'\'';
			return $query;
		}

		/**
		 * Ajoute la condition pour avoir les inscrits PE
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function inscritPE($query) {
			$this->loadModel('Historiqueetatpe');
			$query['conditions'][] = array(
				'OR' => array(
					'Informationpe.id IS NULL',
					'Informationpe.id IN ( '
						.$this->Historiqueetatpe->Informationpe->sqDerniere('Personne')
					.' )'
				)
			);
			$query['conditions'][] =  array(
				'OR' => array(
					'Historiqueetatpe.id IS NULL',
					'Historiqueetatpe.id IN ( '.$this->Historiqueetatpe->sqDernier( 'Informationpe' ).' )'
				)
			);
			$query['conditions']['Historiqueetatpe.etat'] = 'inscription';
			return $query;
		}

		/**
		 * Ajoute la condition pour avoir les non inscrit PE
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function nonInscritPE($query) {
			$this->loadModel('Historiqueetatpe');
			$qdNonInscrits = $this->Historiqueetatpe->Informationpe->qdNonInscrits(false);
			$query['conditions'] = array_merge($query['conditions'], $qdNonInscrits['conditions']);
			return $query;
		}

		/**
		 * Ajoute la condition pour n'avoir que les Soumis à droit et devoir & Droit ouvert et versable
		 * @param array $query
		 * @return array $query
		 */
		public function sdddov($query) {
			//Soumis à droit et devoir
			$query['conditions']['Calculdroitrsa.toppersdrodevorsa'] = '1';
			//Droit ouvert et versable :
			$query['conditions']['Situationdossierrsa.etatdosrsa'] = '2';
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
			$query = $this->Allocataire->searchConditions( $query, $search );
			return $query;
		}
	}