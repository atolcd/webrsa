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
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Personne'
		);

		/**
		 * Ajoute la condition dans la query pour ne pas avoir de CER
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function sansCER($query) {
			$query['conditions'][] = 'Contratinsertion.id IS NULL';
			return $query;
		}

		/**
		 * Base de la requete des orientations
		 */
		public function requeteOrientation($isNouveauxEntrant = false) {
			$conditions = 'SELECT "orientsstructs"."id" AS "orientsstructs__id"
				FROM orientsstructs AS orientsstructs
				WHERE "orientsstructs"."statut_orient" = \'Orienté\'
				AND "orientsstructs"."personne_id" = "Personne"."id"';

			if($isNouveauxEntrant) {
				$dates = $this->datePeriodeCohorte ();
				$conditions .= " AND Historiquedroit.created BETWEEN '".$dates['deb']."' AND '".$dates['fin']."'";
			}
			return $conditions;
		}

		/**
		 * Ajoute la condition dans la query pour ne pas avoir d'orientation
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function sansOrientation($query) {
			$query['conditions'][] = 'Orientstruct.id IS NULL';
			return $query;
		}

		/**
		 * Ajoute la condition dans la query pour avoir une d'orientation
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function avecOrientation($query) {
			//Recupération des type d'orientation voulues
			$limitation = Configure::read ('PlanPauvrete.Cohorte.Orientations.Limite');

			if ( isset( $limitation['structureorientante_id'] ) ){
				$query['conditions'][] = 'Orientstruct.structureorientante_id = '. $limitation['structureorientante_id'];
			}
			if ( isset( $limitation['typeorient_id'] ) ){
				$query['conditions'][] = 'Orientstruct.typeorient_id = '. $limitation['typeorient_id'];
			}
			if ( isset( $limitation['typenotification'] ) ){
				$query['conditions'][] = 'Orientstruct.typenotification = '. $limitation['typenotification'];
			}

			$query['conditions'][] = 'Orientstruct.id IS NOT NULL';
			return $query;
		}

		/**
		 * Ajoute la condition dans la query pour ne pas avoir de rendez vous
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function sansRendezvous($query) {
			$query['conditions'][] = 'Rendezvous.id IS NULL';
			return $query;
		}

		/**
		 * Récupère la liste des excuses recevables en variable de configuration
		 */
		public function excuses_recevables_rdv(){
			$arrayStatutRdvExcusesRecevables = Configure::read('Statistiqueplanpauvrete.orientationRdv.excuses_recevables');
			if (is_array ($arrayStatutRdvExcusesRecevables) && !is_null ($arrayStatutRdvExcusesRecevables) ) {
				return implode(',', $arrayStatutRdvExcusesRecevables);
			}
			return "";
		}

		/**
		 * Calcule la vraie date de début du droit en cours
		 */
		public function date_debut_droit(){
			return '
				LEFT JOIN (
					WITH calcul_date_debut_droit AS (
					SELECT
						personne_id
						, etatdosrsa
						, created
						, CASE
							WHEN
								lag(etatdosrsa) OVER (PARTITION BY personne_id ORDER BY created) != etatdosrsa
								OR lag(etatdosrsa) OVER (PARTITION BY personne_id ORDER BY created) IS NULL
							THEN true
							ELSE false
						END AS first_date
					FROM historiquesdroits h
					)
					SELECT
						distinct ON (personne_id) personne_id,
						created
					FROM calcul_date_debut_droit
					WHERE etatdosrsa = \'2\' AND first_date
					ORDER BY personne_id, created desc
				) AS date_debut_droit ON date_debut_droit.personne_id = "Personne"."id"
			';
		}

		/**
		 * Regroupe une partie des jointures nécessaires au plan pauvreté
		 */
		public function jointures(){
			//Excuses recevables pour les rdv
			$listStatutRdvExcusesRecevables = $this->excuses_recevables_rdv();
			return
			[
				$this->date_debut_droit(),
				$this->Personne->join(
					'Orientstruct',
					[
						'type' => 'LEFT',
						'conditions' => [
							'Orientstruct.personne_id = Personne.id',
							'Orientstruct.statut_orient = \'Orienté\'',
							'date_debut_droit.created <= Orientstruct.date_valid'
						]
					]
				),
				$this->Personne->join(
					'Rendezvous',
					[
						'type' => 'LEFT',
						'conditions' => [
							'Rendezvous.personne_id = Personne.id',
							'date_debut_droit.created <= Rendezvous.daterdv',
							'Rendezvous.statutrdv_id not in ('.$listStatutRdvExcusesRecevables.')',
							'Rendezvous.statutrdv_id is not NULL'
						]
					]
				),
				$this->Personne->join(
					'Contratinsertion',
					[
						'type' => 'LEFT',
						'conditions' => [
							'Contratinsertion.personne_id = Personne.id',
							'date_debut_droit.created <= Contratinsertion.datevalidation_ci',
							'Contratinsertion.decision_ci = \'V\''
						]
					]
				),
			];
		}

		/** Ajoute le join et la condition pour gérer l'état précédent dans l'historique de la personne */
		protected function _etat_precedent($query) {
			$dates = $this->datePeriodeCohorte ();
			$query['joins'][] = 'LEFT JOIN (
				WITH calcul_date_debut_droit AS (
					SELECT
						personne_id
						, etatdosrsa
						, created
						, CASE
							WHEN
								lag(etatdosrsa) OVER (PARTITION BY personne_id ORDER BY created) != etatdosrsa
								OR lag(etatdosrsa) OVER (PARTITION BY personne_id ORDER BY created) IS NULL
							THEN true
							ELSE false
						END AS first_date
					FROM historiquesdroits h
				),etat_prec AS (
				select
					cddd.personne_id,
					cddd.etatdosrsa,
					cddd.created,
					rank() over(PARTITION BY cddd.personne_id, cddd.etatdosrsa ORDER BY cddd.created desc) rank_created
				FROM calcul_date_debut_droit cddd
				JOIN personnes p ON cddd.personne_id = p.id
				JOIN foyers f ON f.id = p.foyer_id
				JOIN dossiers d ON d.id = f.dossier_id
				JOIN situationsdossiersrsa s ON s.dossier_id = d.id
				where
					cddd.created < \'' . $dates['fin'] . '\'
					AND s.etatdosrsa <> cddd.etatdosrsa
					AND first_date
				order by
					personne_id,
					created DESC
				)
				SELECT
					distinct ON (personne_id) personne_id
					, etatdosrsa
					, created
				FROM etat_prec
				WHERE rank_created = 1
					) etat_precedent on
				etat_precedent.personne_id = "Personne"."id" and etat_precedent.etatdosrsa <> "Situationdossierrsa"."etatdosrsa"';

			$query['conditions'][] = "(etat_precedent.etatdosrsa is null OR etat_precedent.etatdosrsa in ('5','6') or (etat_precedent.etatdosrsa in ('3', '4') and etat_precedent.created < date_debut_droit.created - interval '1 year'))";
			return $query;
		}

		/**
		 * Ajoute la condition pour n'avoir que les nouveaux entrants
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function nouveauxEntrants($query) {
			$dates = $this->datePeriodeCohorte ();
			$query = $this->_etat_precedent($query);
			$query['conditions'][] = 'Historiquedroit.created BETWEEN \''.$dates['deb'].'\' AND \''.$dates['fin'].'\'';
			$query['conditions'][] = 'date_debut_droit.created BETWEEN \''.$dates['deb'].'\' AND \''.$dates['fin'].'\'';
			return $query;
		}

		/**
		 * Ajoute la condition dans la query pour n'avoir que les personnes dont le droit passe a SDD-DOV pour la première fois.
		 * SDD-DOV = etatdosrsa 2 et toppersdrodevorsa 1
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function uniqueHistoriqueSdddov($query) {
			$query['joins'][] = "JOIN LATERAL (
			    SELECT
			      COUNT(historiquesdroits.id) AS cnt,
			      historiquesdroits.personne_id
			    FROM
			      historiquesdroits
			    WHERE
			      historiquesdroits.toppersdrodevorsa = '1'
			      AND historiquesdroits.etatdosrsa = '2'
			      AND historiquesdroits.personne_id = \"Personne\".\"id\"
			    GROUP BY historiquesdroits.personne_id ) AS \"HistoCount\" ON (\"HistoCount\".\"personne_id\" = \"Personne\".\"id\" )  ";
			$query['conditions']['HistoCount.cnt'] = '1';

			return $query;
		}

		/**
		 * Ajoute la condition pour n'avoir que les nouveaux entrants
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function stock($query) {
			$dates = $this->datePeriodeCohorte ();

			//Recherche selon Stock
			$query['conditions'][] = 'date_trunc(\'day\', Historiquedroit.created) < \''.$dates['deb'].'\'';
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
			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Historiqueetatpe.identifiantpe',
					'Historiqueetatpe.date',
				)
			);

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
			$this->loadModel('Informationpe');

			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Historiqueetatpe.identifiantpe',
					'Historiqueetatpe.date',
				)
			);

			$query['joins'] = array_merge(
				$query['joins'],
				array(
					$this->Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', 'LEFT OUTER' ),
					$this->Informationpe->Historiqueetatpe->joinInformationpeHistoriqueetatpe( true, 'Informationpe', 'Historiqueetatpe', 'LEFT OUTER' )
				)
			);

			$sqDerniereInformationpe = $this->Informationpe->sqDerniere( 'Personne' );
			$query['conditions'] =  array_merge(
				$query['conditions'],
				array(
					array(
						'OR' => array(
							"Informationpe.id IS NULL",
							"Informationpe.id IN ( {$sqDerniereInformationpe} )"
						),
					),
					array(
						'OR' => array(
							"Historiqueetatpe.etat <> 'inscription'",
							'Historiqueetatpe.etat IS NULL'
						)
					)
				)
			);

			return $query;
		}

		/**
		 * Ajoute la condition pour avoir les personne dont l'état PE as été mis à jour depuis la date de recherche.
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function dateInscritPESupPeriode($query) {
			$dates = $this->datePeriodeCohorte ();
			$query['conditions'][] = '(
				"Historiqueetatpe"."date_modification" IS NULL
				OR
				"Historiqueetatpe"."date_modification" > \''.$dates['deb'].'\'
			)';

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
		 * Ajoute la condition pour n'avoir que les Soumis à droit et devoir & Droit ouvert et versable de l'historique
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function sdddovHistorique($query) {
			//Soumis à droit et devoir
			$query['conditions']['Historiquedroit.toppersdrodevorsa'] = '1';
			//Droit ouvert et versable :
			$query['conditions']['Historiquedroit.etatdosrsa'] = '2';
			return $query;
		}

		/**
		 * Change les conditions de join d'historiquedroit pour prendre l'état de l'historique au moment de la recherche et non le dernier historique
		 *
		 * @param array $query
		 * @param array $search
		 * @return array $query
		 */
		public function joinHistoriqueInDates($query, $search) {
				//Modification du lien à Historiquedroit
				foreach ($query['joins'] as $key => $value) {
					if ( $value['alias'] == 'Historiquedroit' ){
						$created_from =
							$search ['Historiquedroit']['created_from']['year'].'-'.
							$search ['Historiquedroit']['created_from']['month'].'-';
						if (isset($search ['Historiquedroit']['created_from']['day'])){
							$created_from .= $search ['Historiquedroit']['created_from']['day'];
						}else{
							$created_from .= '01';
						}
						$created_to =
							$search ['Historiquedroit']['created_to']['year'].'-'.
							$search ['Historiquedroit']['created_to']['month'].'-';
						if (isset($search ['Historiquedroit']['created_to']['day'])){
							$created_to .= $search ['Historiquedroit']['created_to']['day'];
						}else{
							$created_to .= '01';
						}
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

			$query['conditions'] = $this->conditionsRendezvous ( $query['conditions'], $search);


			return $query;
		}

		/**
		 * Calcul de la période des nouveaux entrants par rapport à la date du jour.
		 *
		 * @return array
		 */
		public function datePeriodeCohorte () {
			$debutPeriode = Configure::read( 'PlanPauvrete.Cohorte.Moisprecedent.deb' );

			return $this->generationPeriodeMois ($debutPeriode);
		}

		/**
		 * Calcul de la période des nouveaux entrants par rapport à la date du jour pour les statistiques.
		 *
		 * @return array
		 */
		public function datePeriodeStatistique ($annee) {
			$debutPeriode = Configure::read( 'PlanPauvrete.Stats.Moisprecedent.deb' );

			return $this->generationPeriodeMois($debutPeriode, $annee);
		}

		/**
		 * Calcul de la période d'un mois enfonction d'un jour de début par rapport à la date du jour.
		 *
		 * @return array
		 */
		public function generationPeriodeMois ($debutPeriode, $annee = null) {
			if ( is_null($annee) || empty($annee)  ){
				$annee = date ('Y');
			}
			$dates = array ();
			if ( is_null($debutPeriode) || empty($debutPeriode) ){
				$debutPeriode = "01";
			}
			$dateDeb = new DateTime ($annee.date('-m-').$debutPeriode);
			$dateFin = new DateTime ($annee.date('-m-').$debutPeriode);

			if (date('j') >= $debutPeriode) {
				$dateDeb->sub (new DateInterval ('P1M'));
				$dateFin->sub (new DateInterval ('P1D'));
			} else {
				$dateDeb->sub (new DateInterval ('P2M'));
				$dateFin->sub (new DateInterval ('P1M'));
				$dateFin->sub (new DateInterval ('P1D'));
			}

			$dates['deb'] = $dateDeb->format ('Y-m-d');
			$dates['fin'] = $dateFin->format ('Y-m-d');

			return $dates;
		}

		/**
		 * Texte pour flux des nouveaux entrants
		 *
		 * @return string
		 */
		public function texteFluxNouveauxEntrants () {
			return $this->generationTexte('Planpauvrete.Nouveaux.Phrasesmois');
		}

		/**
		 * Texte pour flux du stock
		 *
		 * @return string
		 */
		public function texteFluxStock () {
			return $this->generationTexte('Planpauvrete.Stock.Phrasesmois', 'P1M');
		}

		/**
		 * Génération du texte
		 *
		 * @return string
		 */
		public function generationTexte ($locale, $interval = null ) {
			$dateNouveauxEntrants = $this->datePeriodeCohorte ();
			$date = new DateTime ($dateNouveauxEntrants['fin']);

			if (!is_null($interval)) {
				$date->sub (new DateInterval ($interval));
			}

			$mois = __d ('cake', $date->format ('F'));
			$texte = preg_replace ('#MOIS#', $mois, __d ('planpauvrete', $locale));

			return $texte;
		}

		/**
		 * Ajoute la jointure et les conditions des activités professionnelles à exclure si configuré
		 *
		 * @param array
		 *
		 * @return array
		 */
		public function activiteToSkip($query) {
			$this->loadModel('Personne');

			$activiteAExclure = Configure::read('PlanPauvrete.Cohorte.Activite.Skip');
			if(!empty($activiteAExclure)) {

				// Pour le check
				if (!isset($query['joins']) || !is_array ($query['joins'])) {
					$query['joins'] = array ();
				}

				// Ajout du join
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Personne->join('Activite', array('type' => 'LEFT OUTER') )
					)
				);
				// Ajout de la conditions
				$query['conditions'][] = array(
					'OR' => [
						'Activite.act NOT IN' => $activiteAExclure,
						'Activite.act IS NULL'
					]
				);
			}

			return $query;
		}

		/**
		 * Ajoute une condition pour avoir les personnes ayant un PPAE
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function avecPPAE($query){
			$query['conditions'][] = "Informationpe.ppae_date_signature IS NOT NULL";
			return $query;
		}

		/**
		 * Ajoute la condition pour avoir les non inscrit PE et les inscrits PE sans PPAE
		 *
		 * @param array $query
		 * @return array $query
		 */
		public function nonInscritPEouInscritPEsansPPAE($query) {
			$this->loadModel('Informationpe');
			$this->loadModel('Historiqueetatpe');

			$sqDerniereInformationpe = $this->Informationpe->sqDerniere( 'Personne' );
			$sqDerniereHistoriqueetatpe = $this->Historiqueetatpe->sqDernier( 'Informationpe' );

			$conditions = $query['conditions'];
			$query = $this->nonInscritPE($query);

			$query['conditions'] =  array_merge(
				$conditions,
				array(
					'OR' => array(
						array(
							array(
								'OR' => array(
									"Informationpe.id IS NULL",
									"Informationpe.id IN ( {$sqDerniereInformationpe} )"
								)
							),
							array(
								'OR' => array(
									"Historiqueetatpe.etat <> 'inscription'",
									'Historiqueetatpe.etat IS NULL'
								)
							)
						),
						array(
							"Informationpe.ppae_date_signature IS NULL",
							"Informationpe.id IN ( {$sqDerniereInformationpe} )",
							"Historiqueetatpe.id IN ( {$sqDerniereHistoriqueetatpe} )",
							"Historiqueetatpe.etat = 'inscription'"
						)
					)
				)
			);

			return $query;
		}

		/**
		 * Sépare les jointures pour sortir personnes et foyer (ou dossier et foyer)
		 */
		public function separeJointures($query){
			$join[0] = $query['joins'][0];
			$join[1] = $query['joins'][1];
			unset($query['joins'][0]);
			unset($query['joins'][1]);

			return [$join, $query];
		}

		/**
		 * Retourne le tableau des conditions pour la jointure avec Historiquedroit
		 */
		public function conditionsJointureHistoriquedroit($dates, $nouvelentrant = true){

			$cond = [
				'Personne.id = Historiquedroit.personne_id',
				'Historiquedroit.id = (SELECT id
				from historiquesdroits WHERE
				personne_id = "Personne"."id"
				ORDER BY created DESC LIMIT 1)'
			];

			if($nouvelentrant) {
				$cond[] = 'Historiquedroit.created BETWEEN \''.$dates['deb'].'\' AND \''.$dates['fin'].'\'';
			}

			return $cond;

		}

	}
