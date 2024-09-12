<?php
	/**
	 * Code source de la classe Tableauxbords93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Tableauxbords93Controller ...
	 *
	 * @package app.Controller
	 */
	class Tableauxbords93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Tableauxbords93';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'colonnes_export_corpus_tdb2' => 'Tableauxbords93:exportcsv_tableau2_corpus',
			'requeteTableau2' => 'Tableauxbords93:tableau2',
			'sql_tab2_calculs' => 'Tableauxbords93:tableau2',
			'sql_tab2_corpus' => 'Tableauxbords93:tableau2',
			'sql_tab2_histo_base' => 'Tableauxbords93:tableau2',
			'sql_tab2_instant_base' => 'Tableauxbords93:tableau2',
			'calculCategories' => 'Tableauxbords93:tableau2',
			'getDateFromParameter' => 'Tableauxbords93:tableau2',
			'getDateFromTrimestre' => 'Tableauxbords93:tableau2',
		);

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Session',
			'InsertionsBeneficiaires',
			'Allocataires'
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default1' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Search.SearchForm',
			'Observer' => array(
				'className' => 'Prototype.PrototypeObserver',
				'useBuffer' => false
			),
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Default'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Zonegeographique',
			'Personne'
		);

		const LISTE_ENUMS =
		[
			'etatdroit' => ['situationdossierrsa', 'ENUM::ETATDOSRSA::'],
			'sdd' => ['calculdroitrsa', 'ENUM::TOPPERSDRODEVORSA::'],
			'statutpe' => ['historiqueetatpe', 'ENUM::ETAT::'],
			'nivetu' => ['dsp', 'ENUM::NIVETU::'],
			'sexe' => ['personne', 'ENUM::SEXE::'],
			'referent_actif' => ['referent', 'ENUM::ACTIF::'],
			'refe_appartient_struct' => ['tableauxbords93', 'ENUM::BOOL::'],
			'tag_diag' => ['tableauxbords93', 'ENUM::BOOL::'],
			'nveau_orient' => ['tableauxbords93', 'ENUM::BOOL::'],
			'do_tjs_orient' => ['tableauxbords93', 'ENUM::BOOL::'],
			'sdd_tjs_orient' => ['tableauxbords93', 'ENUM::BOOL::'],
			'dohd_origine' => ['orientstruct', 'ENUM::ORIGINE::'],
			'dod_origine' => ['orientstruct', 'ENUM::ORIGINE::'],
			'dsp_vide' => ['tableauxbords93', 'ENUM::BOOL::'],
			'rdv_sans_dsp' => ['tableauxbords93', 'ENUM::BOOL::'],
			'pas_rdv_30j' => ['tableauxbords93', 'ENUM::BOOL::'],
			'pas_rdv_60j' => ['tableauxbords93', 'ENUM::BOOL::'],
			'rdv_prevu_passe' => ['tableauxbords93', 'ENUM::BOOL::'],
			'rdv_coll_sans_indiv' => ['tableauxbords93', 'ENUM::BOOL::'],
			'rdv_sans_d1' => ['tableauxbords93', 'ENUM::BOOL::'],
			'd1_rempli' => ['tableauxbords93', 'ENUM::BOOL::'],
			'cer_struct_valide' => ['tableauxbords93', 'ENUM::BOOL::'],
			'dcerv_duree' => ['cer93', 'ENUM::DUREE::'],
			'dcerv_position' => ['cer93', 'ENUM::POSITIONCER::'],
			'rdv_sans_cer' => ['tableauxbords93', 'ENUM::BOOL::'],
			'pas_cer_pas_rdv' => ['tableauxbords93', 'ENUM::BOOL::'],
			'dcerv_formation' => ['tableauxbords93', 'ENUM::BOOL::'],
			'dcerv_emploi' => ['tableauxbords93', 'ENUM::BOOL::'],
			'dcerv_autonomie_soc' => ['tableauxbords93', 'ENUM::BOOL::'],
			'dcerv_logement' => ['tableauxbords93', 'ENUM::BOOL::'],
			'dcerv_sante' => ['tableauxbords93', 'ENUM::BOOL::'],
			'dcerv_autre' => ['tableauxbords93', 'ENUM::BOOL::'],
		];

        public function tableau2(){

			if(!empty( $this->request->data)){
				//On traite le formulaire et récupère les données
				$data = $this->request->data;
				$params['structure'] = $data['Search']['structure'];
				$params['referent'] = isset($data['Search']['referent']) ? substr($data['Search']['referent'], strpos($data['Search']['referent'], "_") + 1) : null;
				$params['numcom'] = null;
				if($data['Search']['numcom_choice'] == '1' && $data['Search']['numcom'] != '' ){
					$liste_ids = array_values($data['Search']['numcom']);
					$params['numcom'] = "'".implode('\',\'', $liste_ids)."'";
				}

				$params['date'] = $data['Search']['annee_trimestre'];
				//On lance la requête
				$resultats = $this->requeteTableau2($params);

				$this->set('resultats', $resultats[0][0]);
				$this->set(compact('params'));

			}

			//Annee et trimestre
			//On récupère ce qui existe et est enregistré
			$options['annee_trimestre'] =
			[
				'ajd' => 'Aujourd\'hui'
			];
			$trimestres_historises = $this->Personne->query(
				"select distinct trimestre, annee from tdb2_histo_corpus thc order by annee desc, trimestre desc;"
			);
			foreach($trimestres_historises as $trimestre){
				$options['annee_trimestre'][$trimestre[0]['annee'].'_'.$trimestre[0]['trimestre']] = $trimestre[0]['annee'].' - Trimestre '.$trimestre[0]['trimestre'];
			}

			//Villes
			$options['numcom'] = $this->Zonegeographique->listeCodesInseeLocalites([], false);

			$options['structure_referente'] = $this->InsertionsBeneficiaires->structuresreferentes( array('type' => 'optgroup', 'prefix' => false), true );

			$options['referent'] = $this->Allocataires->optionsSession()['PersonneReferent']['referent_id'];

			$this->set(compact('options'));
        }

		/*
		 Calculs : si true on récupère les stats, sinon le corpus
		*/
		public function requeteTableau2($params, $calculs = true){

			//Variables de la requête
			//Date du jour
			if($params['date'] == 'ajd'){
				$instantanne = true;
				$date_du_jour = strval(date("Y-m-d"));
				$annee = strval(date("Y"));
			} else {
				$instantanne = false;
				$tab = explode('_', $params['date']);
				$annee = $tab[0];
				$trimestre = $tab[1];
			}

			//Id Structure referente
			$id_structure = $params['structure'];
			//Id Référent de parcours
			$id_referent = $params['referent'];
			//Liste numcom
			$liste_communes = $params['numcom'];


			if($instantanne && $calculs) {
				$query_sql = $this->sql_tab2_calculs(true, $date_du_jour, $annee, $id_structure, $id_referent, $liste_communes, null);
			} else if ($instantanne && !$calculs) {
				$query_sql = $this->sql_tab2_corpus(true, $date_du_jour, $annee, $id_structure, $id_referent, $liste_communes, null);
			} else if (!$instantanne && $calculs){
				$query_sql = $this->sql_tab2_calculs(false, null, $annee, $id_structure, $id_referent, $liste_communes, $trimestre);
			} else {
				$query_sql = $this->sql_tab2_corpus(false, null, $annee, $id_structure, $id_referent, $liste_communes, $trimestre);
			}

			return $this->Personne->query($query_sql);

		}

		public function sql_tab2_histo_base($trimestre, $annee, $id_structure){
			return
			"
			with corpus as (
				select * from tdb2_histo_corpus where annee = $annee and trimestre = $trimestre and structure_referente = $id_structure
			)
			";
		}

		public function sql_tab2_instant_base($date_du_jour, $annee, $id_structure){
			$statut_rdv_honore = '1';
			$statut_rdv_prevu = '2';
			$type_rdv_indiv = '15';
			$type_rdv_coll = '14';

			return
			"
			-- Colonne 1
			with orient_dans_annee as
			(
				select distinct o.personne_id
				from orientsstructs o left join orientsstructs o2 on o.personne_id = o2.personne_id and o2.rgorient = o.rgorient + 1
				where
				extract(year from o.date_valid) <= '{$annee}' and (o2.date_valid > '{$annee}_01-01' or o2.id is null)
				and o.statut_orient = 'Orienté'
				and o.structurereferente_id = {$id_structure}
			),
			rang_derniere_orient as
			(
				select personne_id, max(rgorient) as rgmax
				from orientsstructs o 
				where o.statut_orient = 'Orienté'
				group by personne_id
			),
			toujours_orientes as
			(
				select o.personne_id, rdo.rgmax
				from orientsstructs o join rang_derniere_orient rdo on rdo.personne_id = o.personne_id and rdo.rgmax = o.rgorient
				where o.structurereferente_id = {$id_structure}
			),
			dernier_droit as
			(
				select
					h.personne_id,
					h.toppersdrodevorsa,
					h.etatdosrsa,
					h.created,
					rank() over(partition by h.personne_id order by created desc, id desc) as rang
				from historiquesdroits h join orient_dans_annee oda on oda.personne_id = h.personne_id
			),
			dernier_historique_pe as
			(
				select 
				p.id as personne_id,
				h.id as historique_id,
				h.identifiantpe as identifiant_pe,
				h.etat as etatpe,
				rank() over(partition by h.informationpe_id order by h.date_creation desc, h.id desc) as rang
				from historiqueetatspe h join informationspe i on h.informationpe_id = i.id
				join personnes p on SUBSTRING( i.nir FROM 1 FOR 13 ) = SUBSTRING( p.nir FROM 1 FOR 13 )
				join orient_dans_annee oda on oda.personne_id = p.id
			),
			nouveaux_orient as
			(
				select o.personne_id 	
				from orientsstructs o left join orientsstructs o_prec on o_prec.rgorient = o.rgorient - 1 and o_prec.personne_id = o.personne_id
				where extract(year from o.date_valid) = '{$annee}'
				and (o_prec.id is null or o.structurereferente_id <> o_prec.structurereferente_id)
				and o.structurereferente_id = {$id_structure}
			),
			--Dernière version de révision des DSP
			derniere_dsp_rev as
			(
				SELECT 
				dr.id,
				oda.personne_id,
				dr.nivetu as nivetu,
				rank() over(partition by dr.personne_id order by dr.modified desc, dr.id desc) as rang
				from orient_dans_annee oda left join dsps_revs dr on dr.personne_id = oda.personne_id
			),
			referent_actuel as
			(
				select
				oda.personne_id,
				pr.referent_id,
				pr.structurereferente_id,
				r.nom,
				r.id,
				r.prenom,
				r.actif,
				rank() over(partition by pr.personne_id order by pr.dddesignation desc, pr.id desc) as rang
				from
				orient_dans_annee oda join personnes_referents pr on pr.personne_id = oda.personne_id and dfdesignation is null
				join referents r on r.id = pr.referent_id
			),
			tag_entretien_diag as
			(
				select 
				oda.personne_id,
				case when vt.id is not null then true else false end as tag_diag,
				case when vt.id is not null then t.created else null end as date_tag
				from 
				orient_dans_annee oda left join entites_tags et on et.fk_value = oda.personne_id and et.modele = 'Personne'
				left join tags t on t.id = et.tag_id left join valeurstags vt on vt.id = t.valeurtag_id and vt.name = 'Entretien de diagnostic'
			),
			id_derniere_orient_hors_diag as
			(
				select
				o.personne_id,
				max(o.rgorient) as rgmax
				from orientsstructs o join orient_dans_annee oda on oda.personne_id = o.personne_id
				where o.statut_orient = 'Orienté'
				and o.structurereferente_id = {$id_structure}
				and o.origine <> 'entdiag'
				group by o.personne_id
			),
			derniere_orient_hors_diag as 
			(
				select
				o.id,
				o.personne_id,
				o.origine,
				o.date_valid,
				o.rgorient,
				t.lib_type_orient,
				o.typeorient_id,
				o.structurereferente_id,
				s.lib_struc
				from orientsstructs o
				join id_derniere_orient_hors_diag id on o.personne_id = id.personne_id and o.rgorient = id.rgmax
				join typesorients t on o.typeorient_id = t.id
				join structuresreferentes s on s.id = o.structurereferente_id
			),
			id_derniere_orient_diag as
			(
				select
				o.personne_id, 
				max(o.rgorient) as rgmax
				from orientsstructs o join orient_dans_annee oda on oda.personne_id = o.personne_id
				where o.statut_orient = 'Orienté'
				and o.structureorientante_id = {$id_structure}
				and o.origine = 'entdiag'
				group by o.personne_id
			),
			derniere_orient_diag as 
			(
				select
				o.id,
				o.personne_id, 
				o.origine,
				o.date_valid,
				o.rgorient,
				t.lib_type_orient,
				o.typeorient_id,
				o.structurereferente_id,
				s.lib_struc as structurereferente,
				o.structureorientante_id,
				s2.lib_struc as structureorientante
				from orientsstructs o 
				join id_derniere_orient_diag id on o.personne_id = id.personne_id and o.rgorient = id.rgmax 
				join typesorients t on o.typeorient_id = t.id
				join structuresreferentes s on s.id = o.structurereferente_id
				join structuresreferentes s2 on s2.id = o.structureorientante_id
				left join derniere_orient_hors_diag dohd on dohd.personne_id = o.personne_id
				where dohd.date_valid < o.date_valid
			),
			nombre_rdv as 
			(
				select 
				rdv.personne_id,
				count(*) filter (where typerdv_id = {$type_rdv_indiv} and rdv.statutrdv_id = {$statut_rdv_honore}) as nb_rdv_indiv,
				count(*) filter (where typerdv_id = {$type_rdv_coll} and rdv.statutrdv_id = {$statut_rdv_honore}) as nb_rdv_coll
				from orient_dans_annee oda join rendezvous rdv on rdv.personne_id = oda.personne_id
				where structurereferente_id = {$id_structure}
				group by rdv.personne_id
			),
			dernier_rdv_ind_prevu as 
			(
				select 
				rdv.personne_id,
				rdv.daterdv,
				rdv.id,
				rank() over(partition by rdv.personne_id order by rdv.daterdv desc, rdv.heurerdv desc, rdv.id desc) as rang
				from orient_dans_annee oda join rendezvous rdv on rdv.personne_id = oda.personne_id
				where structurereferente_id = {$id_structure}
				and typerdv_id = {$type_rdv_indiv}
				and statutrdv_id = {$statut_rdv_prevu}
			),
			dernier_rdv_ind_honore as 
			(
				select 
				rdv.personne_id,
				rdv.daterdv,
				rdv.id,
				rank() over(partition by rdv.personne_id order by rdv.daterdv desc, rdv.heurerdv desc, rdv.id desc) as rang
				from orient_dans_annee oda join rendezvous rdv on rdv.personne_id = oda.personne_id
				where structurereferente_id = {$id_structure}
				and typerdv_id = {$type_rdv_indiv}
				and statutrdv_id = {$statut_rdv_honore}
			),
			dernier_rdv_coll_prevu as 
			(
				select 
				rdv.personne_id,
				rdv.daterdv,
				rdv.id,
				rank() over(partition by rdv.personne_id order by rdv.daterdv desc, rdv.heurerdv desc, rdv.id desc) as rang
				from orient_dans_annee oda join rendezvous rdv on rdv.personne_id = oda.personne_id
				where structurereferente_id = {$id_structure}
				and typerdv_id = {$type_rdv_coll}
				and statutrdv_id = {$statut_rdv_prevu}
			),
			dernier_rdv_coll_honore as 
			(
				select 
				rdv.personne_id,
				rdv.daterdv,
				rdv.id,
				rank() over(partition by rdv.personne_id order by rdv.daterdv desc, rdv.heurerdv desc, rdv.id desc) as rang
				from orient_dans_annee oda join rendezvous rdv on rdv.personne_id = oda.personne_id
				where structurereferente_id = {$id_structure}
				and typerdv_id = {$type_rdv_coll}
				and statutrdv_id = {$statut_rdv_honore}
			),
			rdv_prevu_passe as 
			(
				select
				count(rdv.id) as nb_rdv,
				oda.personne_id
				from orient_dans_annee oda join rendezvous rdv on rdv.personne_id = oda.personne_id
				where structurereferente_id = {$id_structure}
				and statutrdv_id = {$statut_rdv_prevu}
				and daterdv < '{$date_du_jour}'
				group by oda.personne_id
			),
			d1_annuel AS 
			(
				select 
				q.*
				from orient_dans_annee oda join questionnairesd1pdvs93 q on q.personne_id = oda.personne_id
				where q.date_validation between '{$annee}-01-01' and '{$annee}-12-31'
			),
			cer_structure as 
			(
				select 
				oda.personne_id,
				count(ci.id) as nb_cer
				from orient_dans_annee oda join contratsinsertion ci on ci.personne_id = oda.personne_id
				where decision_ci = 'V'
				and dd_ci <= '{$annee}-12-31' and df_ci >= '{$annee}-01-01'
				and structurereferente_id = {$id_structure}
				group by oda.personne_id
			),
			cer_a_date as 
			(
				select 
				oda.personne_id,
				case when count(ci.id)>0 then true else false end as cer_valide_a_date 
				from orient_dans_annee oda join contratsinsertion ci on ci.personne_id = oda.personne_id
				where decision_ci = 'V'
				and dd_ci <= '{$date_du_jour}' and df_ci >= '{$date_du_jour}'
				and structurereferente_id = {$id_structure}
				group by oda.personne_id
			),
			id_dernier_cer_valide_cd as
			(
				select 
				oda.personne_id,
				max(ci.rg_ci) as max_rg
				from orient_dans_annee oda 
				join contratsinsertion ci on ci.personne_id = oda.personne_id
				join cers93 cer on cer.contratinsertion_id = ci.id
				where cer.positioncer = '99valide'
				group by oda.personne_id	
			),
			sujets_dernier_cer_valide as 
			(
				select 
				cer.id,
				bool_or(cersuj.sujetcer93_id = 1) as emploi,
				bool_or(cersuj.sujetcer93_id = 2) as formation,
				bool_or(cersuj.sujetcer93_id = 3) as autonomie_soc,
				bool_or(cersuj.sujetcer93_id = 4) as logement,
				bool_or(cersuj.sujetcer93_id = 5) as sante,
				bool_or(cersuj.sujetcer93_id = 6) as autre
				from id_dernier_cer_valide_cd oda 
				join contratsinsertion ci on ci.personne_id = oda.personne_id and ci.rg_ci = oda.max_rg
				join cers93 cer on cer.contratinsertion_id = ci.id
				left join cers93_sujetscers93 cersuj on cersuj.cer93_id = cer.id
				group by cer.id
			),
			dernier_cer_valide_cd as 
			(
				select 
				oda.personne_id,
				cer.id,
				ci.structurereferente_id,
				ci.referent_id,
				ci.created,
				ci.datevalidation_ci,
				ci.rg_ci,
				ci.dd_ci,
				ci.df_ci,
				cer.duree,
				cer.positioncer,
				suj.emploi,
				suj.formation,
				suj.autonomie_soc,
				suj.logement,
				suj.sante,
				suj.autre,
				struct.lib_struc,
				ref.nom || ' ' || ref.prenom as referent
				from id_dernier_cer_valide_cd oda
				join contratsinsertion ci on ci.personne_id = oda.personne_id and ci.rg_ci = oda.max_rg
				join cers93 cer on cer.contratinsertion_id = ci.id
				join structuresreferentes struct on struct.id = ci.structurereferente_id
				join referents ref on ref.id = ci.referent_id
				left join sujets_dernier_cer_valide suj on suj.id = cer.id
			),
			id_dernier_cer as
			(
				select 
				oda.personne_id,
				max(ci.rg_ci) as max_rg
				from orient_dans_annee oda 
				join contratsinsertion ci on ci.personne_id = oda.personne_id
				join cers93 cer on cer.contratinsertion_id = ci.id
				where cer.positioncer <> '00enregistre'
				group by oda.personne_id	
			),
			dernier_cer as 
			(
				select 
				oda.personne_id,
				cer.id,
				ci.created,
				ci.dd_ci,
				ci.df_ci,
				cer.positioncer,
				ci.rg_ci
				from id_dernier_cer oda 
				join contratsinsertion ci on ci.personne_id = oda.personne_id and ci.rg_ci = oda.max_rg
				join cers93 cer on cer.contratinsertion_id = ci.id
			),
			rdv_prevu_toutes_structures AS
			(
				SELECT
				oda.personne_id,
				count(r.id) > 0 as rdv_prevu_toutes_structures
				FROM orient_dans_annee oda join rendezvous r on r.personne_id = oda.personne_id
				where r.statutrdv_id = {$statut_rdv_prevu}
				group by oda.personne_id
			),
			rdv_30_jours AS
			(
				select
				case when count(rdv.id) = 0 then true else false end as pas_rdv_30j,
				oda.personne_id
				from orient_dans_annee oda left join rendezvous rdv on rdv.personne_id = oda.personne_id
				and rdv.structurereferente_id = {$id_structure}
				and rdv.statutrdv_id in ({$statut_rdv_prevu}, {$statut_rdv_honore})
				and rdv.typerdv_id = {$type_rdv_indiv}
				and rdv.daterdv > (date '{$date_du_jour}' - interval '30' day)
				and rdv.daterdv < (date '{$date_du_jour}' + interval '30' day)
				group by oda.personne_id
			),
			rdv_60_jours AS
			(
				select
				case when count(rdv.id) = 0 then true else false end as pas_rdv_60j,
				oda.personne_id
				from orient_dans_annee oda left join rendezvous rdv on rdv.personne_id = oda.personne_id
				and rdv.structurereferente_id = {$id_structure}
				and rdv.statutrdv_id in ({$statut_rdv_prevu}, {$statut_rdv_honore})
				and rdv.typerdv_id = {$type_rdv_indiv}
				and rdv.daterdv > (date '{$date_du_jour}' - interval '60' day)
				and rdv.daterdv < (date '{$date_du_jour}' + interval '60' day)
				group by oda.personne_id
			),
			corpus AS (
			select
			--identifiants
			p.id as personne_id,
			p.nir as nir,
			d.matricule as caf,
			dhpe.identifiant_pe as identifiant_pe,
			d.numdemrsa as numrsa,
			-- identité
			p.qual as civilite,
			p.nom as nom,
			p.nomnai as nom_naissance,
			p.prenom as prenom,
			p.dtnai as date_naissance,
			( EXTRACT ( YEAR FROM AGE(p.dtnai) ) ) as age,
			--contact
			adr.numvoie as numvoie,
			adr.libtypevoie as libtypevoie,
			adr.nomvoie as nomvoie,
			adr.codepos as codepos,
			adr.numcom as numcom,
			adr.nomcom as nomcom,
			p.numfixe as tel1,
			p.numport as tel2,
			p.email as email,
			-- droit
			d.dtdemrsa as datedemrsa,
			dd.etatdosrsa as etatdroit,
			dd.toppersdrodevorsa as sdd,
			dhpe.etatpe as statutpe,
			--sociodemo
			case when dsprev.id is not null then dsprev.nivetu else dsp.nivetu end as nivetu,
			-- TODO catégorie age
			-- TODO catégorie ancienneté
			p.sexe as sexe,
			--referent parcours
			refe.nom || ' ' || refe.prenom as nom_ref,
			refe.id as id_ref,
			case when dohd.date_valid >= dod.date_valid or dod.id is null
			then refe.structurereferente_id = dohd.structurereferente_id
			when dohd.date_valid < dod.date_valid or dohd.id is null
			then refe.structurereferente_id = dod.structurereferente_id
			end as refe_appartient_struct,
			refe.actif as referent_actif,
			--tag
			CASE WHEN ted.tag_diag IS NOT NULL THEN ted.tag_diag ELSE false end as tag_diag,
			ted.date_tag as date_creation_tag,
			--sous catégories
			case when nvo.personne_id is not null then true else false end as nveau_orient,
			case when tjo.personne_id is not null then true else false end as toujours_orient,
			case when tjo.personne_id is not null and dd.etatdosrsa in ('2', '3', '4') then true else false end as do_tjs_orient,
			case when tjo.personne_id is not null and dd.toppersdrodevorsa = '1' then true else false end as sdd_tjs_orient,
			--derniere orientation hors diag
			dohd.id as dohd_id,
			dohd.origine as dohd_origine,
			dohd.date_valid as dohd_date_valid,
			dohd.rgorient as dohd_rgorient,
			dohd.lib_type_orient as dohd_type,
			dohd.lib_struc as dohd_structurereferente,
			-- derniere orientation diag
			dod.id as dod_id,
			dod.origine as dod_origine,
			dod.date_valid as dod_date_valid,
			dod.rgorient as dod_rgorient,
			dod.lib_type_orient as dod_type,
			dod.structurereferente as dod_structurereferente,
			dod.structurereferente_id as dod_structurereferente_id,
			dod.structureorientante as dod_structureorientante,
			--rdv
			nbrdv.nb_rdv_indiv,
			nbrdv.nb_rdv_coll,
			--dsp
			case 
				when dsp.id is null
				or num_nonnulls(dsp.sitpersdemrsa, dsp.topisogroouenf, dsp.topdrorsarmiant, dsp.drorsarmianta2, dsp.topcouvsoc, dsp.accosocfam, dsp.libcooraccosocfam, dsp.accosocindi, dsp.libcooraccosocindi, dsp.soutdemarsoc, dsp.nivetu, dsp.nivdipmaxobt, dsp.annobtnivdipmax, dsp.topqualipro, dsp.libautrqualipro, dsp.topcompeextrapro, dsp.libcompeextrapro, dsp.topengdemarechemploi, dsp.hispro, dsp.libderact, dsp.libsecactderact, dsp.cessderact, dsp.topdomideract, dsp.libactdomi, dsp.libsecactdomi, dsp.duractdomi, dsp.inscdememploi,dsp.topisogrorechemploi, dsp.accoemploi, dsp.libcooraccoemploi, dsp.topprojpro, dsp.libemploirech, dsp.libsecactrech, dsp.topcreareprientre, dsp.concoformqualiemploi, dsp.topmoyloco, dsp.toppermicondub, dsp.topautrpermicondu, dsp.libautrpermicondu, dsp.natlog, dsp.demarlog, dsp.libformenv, dsp.statutoccupation, dsp.suivimedical, dsp.libderact66_metier_id, dsp.libsecactderact66_secteur_id , dsp.libactdomi66_metier_id, dsp.libsecactdomi66_secteur_id, dsp.libemploirech66_metier_id, dsp.libsecactrech66_secteur_id, dsp.deractromev3_id, dsp.deractdomiromev3_id, dsp.actrechromev3_id) = 0
				then true 
				else false 
			end as dsp_vide,
			--rdv
			drip.daterdv as date_drip,
			drih.daterdv as date_drih,
			drcp.daterdv as date_drcp,
			drch.daterdv as date_drch,
			case when rpp.nb_rdv > 0 then true else false end as rdv_prevu_passe,
			--D1
			case when d1.id is not null then true else false end as d1_existant,
			case
				when d1.id is null
				or num_nonnulls(d1.personne_id, d1.rendezvous_id, d1.situationallocataire_id, d1.inscritpe, d1.marche_travail, d1.vulnerable, d1.diplomes_etrangers, d1.categorie_sociopro, d1.nivetu, d1.autre_caracteristique, d1.autre_caracteristique_autre, d1.conditions_logement, d1.conditions_logement_autre, d1.date_validation) = 0
				then false
				else true
			end as d1_rempli,
			--CER
			case when cs.nb_cer > 0 then true else false end as cer_struct_valide,
			dcerv.structurereferente_id as dcerv_structurereferente_id,
			dcerv.id as dcerv_id,
			dcerv.lib_struc as dcerv_structurereferente_lib,
			dcerv.referent as dcerv_referent,
			dcerv.created as dcerv_created,
			dcerv.datevalidation_ci as dcerv_date_valid,
			dcerv.rg_ci as dcerv_rang,
			dcerv.dd_ci as dcerv_dd,
			dcerv.df_ci as dcerv_df,
			dcerv.duree as dcerv_duree,
			dcerv.positioncer as dcerv_position,
			dcerv.emploi as dcerv_emploi,
			dcerv.formation as dcerv_formation,
			dcerv.autonomie_soc as dcerv_autonomie_soc,
			dcerv.logement as dcerv_logement,
			dcerv.sante as dcerv_sante,
			dcerv.autre as dcerv_autre,
			--TODO vérifier si le dernier cer est terminé ou non et si un nouveau est prévu
			case when dercer.id is null or dercer.df_ci < '{$date_du_jour}' then true else false end as pas_cer_signe ,
			case when cvad.cer_valide_a_date is true then true else false end as cer_valide_a_date,
			case when rpts.rdv_prevu_toutes_structures is true then true else false end as rdv_prevu_toutes_structures,
			r30j.pas_rdv_30j,
			r60j.pas_rdv_60j
			from orient_dans_annee oda
			join personnes p on p.id = oda.personne_id
			join foyers f on f.id = p.foyer_id 
			join dossiers d on d.id = f.dossier_id 
			join dernier_droit dd on dd.personne_id = oda.personne_id and dd.rang = 1
			join adressesfoyers adf on adf.foyer_id = f.id and adf.rgadr = '01'
			join adresses adr on adr.id = adf.adresse_id
			left join dernier_historique_pe dhpe on dhpe.rang = 1 and dhpe.personne_id = p.id
			left join derniere_dsp_rev dsprev on dsprev.personne_id = p.id and dsprev.rang = 1
			left join dsps dsp on dsp.personne_id = p.id
			left join referent_actuel refe on refe.personne_id = p.id and refe.rang = 1
			left join tag_entretien_diag ted on ted.personne_id = p.id
			left join nouveaux_orient nvo on nvo.personne_id = p.id
			left join toujours_orientes tjo on tjo.personne_id = p.id
			left join derniere_orient_hors_diag dohd on dohd.personne_id = p.id
			left join derniere_orient_diag dod on dod.personne_id = p.id
			left join nombre_rdv nbrdv on nbrdv.personne_id = p.id
			left join dernier_rdv_ind_prevu drip on drip.personne_id = p.id and drip.rang = 1
			left join dernier_rdv_ind_honore drih on drih.personne_id = p.id and drih.rang = 1
			left join dernier_rdv_coll_prevu drcp on drcp.personne_id = p.id and drcp.rang = 1
			left join dernier_rdv_coll_honore drch on drch.personne_id = p.id and drch.rang = 1
			left join rdv_prevu_passe rpp on rpp.personne_id = p.id
			left join d1_annuel d1 on d1.personne_id = p.id
			left join cer_structure cs on cs.personne_id = p.id
			left join cer_a_date cvad on cvad.personne_id = p.id
			left join dernier_cer_valide_cd dcerv on dcerv.personne_id = p.id
			left join dernier_cer dercer on dercer.personne_id = p.id
			left join rdv_prevu_toutes_structures rpts on rpts.personne_id = p.id
			left join rdv_30_jours r30j on r30j.personne_id = p.id
			left join rdv_60_jours r60j on r60j.personne_id = p.id
			)";
		}



		public function sql_tab2_calculs($instant, $date_du_jour, $annee, $id_structure, $id_referent, $liste_communes, $trimestre){

			$where = "true";
			if(!empty($id_referent)){
				$where .= " and id_ref = {$id_referent}";
			}
			if(!empty($liste_communes)){
				$where .= " and numcom in ({$liste_communes})";
			}

			if($instant){
				$base = $this->sql_tab2_instant_base($date_du_jour, $annee, $id_structure);
			} else {
				$base = $this->sql_tab2_histo_base($trimestre, $annee, $id_structure);
			}

			return
			$base."
			select
			--Total
			count(*) as T_A
			,count(*) filter (WHERE nveau_orient IS true) as T_B
			,count(*) filter (WHERE toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) as T_C
			,count(*) filter (WHERE toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') as T_D
			--Conventionnel
			--orientés diag
			,count(*) filter (where (dod_id is not null and tag_diag is true)) as C1_A
			,count(*) filter (where (nveau_orient IS true) AND (dod_id is not null and tag_diag is true)) as C1_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and (dod_id is not null and tag_diag is true)) as C1_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and (dod_id is not null and tag_diag is true)) as C1_D
			-- Conservés après diagnostic
			,count(*) filter (where (dod_id is not null and tag_diag is true and dod_structurereferente_id = {$id_structure})) as C2_A
			,count(*) filter (where (nveau_orient IS true) AND (dod_id is not null and tag_diag is true and dod_structurereferente_id = {$id_structure})) as C2_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and (dod_id is not null and tag_diag is true and dod_structurereferente_id = {$id_structure})) as C2_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and (dod_id is not null and tag_diag is true and dod_structurereferente_id = {$id_structure})) as C2_D
			-- Orientés pour accompagnement
			,count(*) filter (where (dohd_id is not null and tag_diag is false)) as C3_A
			,count(*) filter (where (nveau_orient IS true) AND (dohd_id is not null and tag_diag is false)) as C3_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and (dohd_id is not null and tag_diag is false)) as C3_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and (dohd_id is not null and tag_diag is false)) as C3_D
			-- Au moins 1 RDV indiv ou collectif honoré
			,count(*) filter (where date_drih is not null or date_drch is not null) as C4_A
			,count(*) filter (where (nveau_orient IS true) and (date_drih is not null or date_drch is not null)) as C4_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and (date_drih is not null or date_drch is not null)) as C4_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and (date_drih is not null or date_drch is not null)) as C4_D
			--1 cer valide à date de la structure
			,count(*) filter (where cer_valide_a_date) as C5_A
			,count(*) filter (where (nveau_orient IS true) and cer_valide_a_date) as C5_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and cer_valide_a_date) as C5_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and cer_valide_a_date) as C5_D
			--moins de 4 rdv indiv honores et un CER valide au moins un jour
			,count(*) filter (where nb_rdv_indiv < 4 and cer_struct_valide) as C6_A
			,count(*) filter (where (nveau_orient IS true) and (nb_rdv_indiv < 4 and cer_struct_valide)) as C6_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and (nb_rdv_indiv < 4 and cer_struct_valide)) as C6_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and (nb_rdv_indiv < 4 and cer_struct_valide)) as C6_D
			-- au moins 4 rdv indiv honores et un CER valide au moins un jour
			,count(*) filter (where nb_rdv_indiv >= 4 and cer_struct_valide) as C7_A
			,count(*) filter (where (nveau_orient IS true) and (nb_rdv_indiv >= 4 and cer_struct_valide)) as C7_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and (nb_rdv_indiv >= 4 and cer_struct_valide)) as C7_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and (nb_rdv_indiv >= 4 and cer_struct_valide)) as C7_D
			--Pilotage
			-- pas de rdv indiv prevu ou honore depuis + ou - 30 jours
			,count(*) filter (where	pas_rdv_30j) as P1_A
			,count(*) filter (where
				(nveau_orient IS true) and
				(pas_rdv_30j)
			) as P1_B
			,count(*) filter (where
				(toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and
				(pas_rdv_30j)
			) as P1_C
			,count(*) filter (where
				(toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and
				(pas_rdv_30j)
			) as P1_D
			-- pas de rdv indiv prevu ou honore depuis + ou - 60 jours
			,count(*) filter (where pas_rdv_60j) as P2_A
			,count(*) filter (where
				(nveau_orient IS true) and
				(pas_rdv_60j)
			) as P2_B
			,count(*) filter (where
				(toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and
				(pas_rdv_60j)
			) as P2_C
			,count(*) filter (where
				(toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and
				(pas_rdv_60j)
			) as P2_D
			--rdv prévu dans le passé
			,count(*) filter (where rdv_prevu_passe) as P3_A
			,count(*) filter (where (nveau_orient IS true) and rdv_prevu_passe) as P3_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and  rdv_prevu_passe) as P3_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and rdv_prevu_passe) as P3_D
			-- au moins 1 rdv collectif honoré mais 0 rdv indiv honore
			,count(*) filter (where date_drch is not NULL AND date_drih is null) as P4_A
			,count(*) filter (where (nveau_orient IS true) and (date_drch is not NULL AND date_drih is null)) as P4_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and (date_drch is not NULL AND date_drih is null)) as P4_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and (date_drch is not NULL AND date_drih is null)) as P4_D
			--RDV indiv honore mais dsp vide
			,count(*) filter (where date_drih is not null and dsp_vide) as P5_A
			,count(*) filter (where (nveau_orient IS true) and (date_drih is not null and dsp_vide)) as P5_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and (date_drih is not null and dsp_vide)) as P5_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and (date_drih is not null and dsp_vide)) as P5_D
			--RDV indiv honoré mais pas de D1
			,count(*) filter (where date_drih is not null and extract(year from date_drih) = '{$annee}' and d1_existant is false) as P6_A
			,count(*) filter (where (nveau_orient IS true) and (date_drih is not null and extract(year from date_drih) = '{$annee}' and d1_existant is false)) as P6_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and (date_drih is not null and extract(year from date_drih) = '{$annee}' and d1_existant is false)) as P6_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and (date_drih is not null and extract(year from date_drih) = '{$annee}' and d1_existant is false)) as P6_D
			--rdv indiv honore mais pas de cer valide
			,count(*) filter (where date_drih is not null and cer_valide_a_date is false) as P7_A
			,count(*) filter (where (nveau_orient IS true) and (date_drih is not null and cer_valide_a_date is false)) as P7_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and (date_drih is not null and cer_valide_a_date is false)) as P7_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and (date_drih is not null and cer_valide_a_date is false)) as P7_D
			--pas de cer valide à date et pas de cer signé + pas de rdv prev peu importe la structure
			,count(*) filter (where cer_valide_a_date is FALSE and rdv_prevu_toutes_structures is false and pas_cer_signe is true) as P8_A
			,count(*) filter (where (nveau_orient IS true) and (cer_valide_a_date is FALSE and rdv_prevu_toutes_structures is false and pas_cer_signe is true)) as P8_B
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit IN ('2','3','4')) and (cer_valide_a_date is FALSE and rdv_prevu_toutes_structures is false and pas_cer_signe is true)) as P8_C
			,count(*) filter (where (toujours_orient IS TRUE AND etatdroit = '2' AND sdd = '1') and (cer_valide_a_date is FALSE and rdv_prevu_toutes_structures is false and pas_cer_signe is true)) as P8_D
			FROM corpus
			where {$where};
			";
		}

		public function sql_tab2_corpus($instant, $date_du_jour, $annee, $id_structure, $id_referent, $liste_communes, $trimestre){

			$where = "true";
			if(!empty($id_referent)){
				$where .= " and id_ref = {$id_referent}";
			}
			if(!empty($liste_communes)){
				$where .= " and numcom in ({$liste_communes})";
			}

			if($instant){
				$base = $this->sql_tab2_instant_base($date_du_jour, $annee, $id_structure);
				$date_export = $date_du_jour;
			} else {
				$base = $this->sql_tab2_histo_base($trimestre, $annee, $id_structure);
				$date_export = $trimestre;
			}


			return
			$base."
			select
			*,
			date_drih is not null and dsp_vide as rdv_sans_dsp,
			date_drch is not NULL AND date_drih is null as rdv_coll_sans_indiv,
			date_drih is not null and extract(year from date_drih) = '$annee' and d1_existant is false as rdv_sans_d1,
			date_drih is not null and cer_valide_a_date is false as rdv_sans_cer,
			cer_valide_a_date is FALSE and rdv_prevu_toutes_structures is false and pas_cer_signe is true as pas_cer_pas_rdv,
			$annee as annee_export,
			'$date_export' as date_export
			FROM corpus
			where {$where}
			";

		}

		/**
		 * Export CSV des données calculées pour le tableau 2
		 */
		public function exportcsv_tableau2_donnees() {
			$params['structure'] = isset($this->request->query['structure']) ? $this->request->query['structure'] : null;
			$params['referent']  = isset($this->request->query['referent']) && $this->request->query['referent'] != 0  ? $this->request->query['referent']  : null;
			$params['numcom']    = isset($this->request->query['numcom'])    ? $this->request->query['numcom']    : null;
			$params['date']    = isset($this->request->query['date'])        ? $this->request->query['date']    : null;

			$donnees = $this->requeteTableau2($params)[0][0];

			$export = array ();
			$i = 0;

			$export[$i++] = ['', __d('tableauxbords93', 'Tableau2.titre.colonneA'), __d('tableauxbords93', 'Tableau2.titre.colonneB'), __d('tableauxbords93', 'Tableau2.titre.colonneC'), __d('tableauxbords93', 'Tableau2.titre.colonneD')];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.t'), $donnees['t_a'], $donnees['t_b'], $donnees['t_c'], $donnees['t_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.c')];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.c1'), $donnees['c1_a'], $donnees['c1_b'], $donnees['c1_c'], $donnees['c1_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.c2'), $donnees['c2_a'], $donnees['c2_b'], $donnees['c2_c'], $donnees['c2_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.c3'), $donnees['c3_a'], $donnees['c3_b'], $donnees['c3_c'], $donnees['c3_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.c4'), $donnees['c4_a'], $donnees['c4_b'], $donnees['c4_c'], $donnees['c4_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.c5'), $donnees['c5_a'], $donnees['c5_b'], $donnees['c5_c'], $donnees['c5_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.c6'), $donnees['c6_a'], $donnees['c6_b'], $donnees['c6_c'], $donnees['c6_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.c7'), $donnees['c7_a'], $donnees['c7_b'], $donnees['c7_c'], $donnees['c7_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.p')];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.p1'), $donnees['p1_a'], $donnees['p1_b'], $donnees['p1_c'], $donnees['p1_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.p2'), $donnees['p2_a'], $donnees['p2_b'], $donnees['p2_c'], $donnees['p2_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.p3'), $donnees['p3_a'], $donnees['p3_b'], $donnees['p3_c'], $donnees['p3_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.p4'), $donnees['p4_a'], $donnees['p4_b'], $donnees['p4_c'], $donnees['p4_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.p5'), $donnees['p5_a'], $donnees['p5_b'], $donnees['p5_c'], $donnees['p5_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.p6'), $donnees['p6_a'], $donnees['p6_b'], $donnees['p6_c'], $donnees['p6_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.p7'), $donnees['p7_a'], $donnees['p7_b'], $donnees['p7_c'], $donnees['p7_d']];
			$export[$i++] = [ __d('tableauxbords93', 'Tableau2.titre.p8'), $donnees['p8_a'], $donnees['p8_b'], $donnees['p8_c'], $donnees['p8_d']];

			$this->set('export', $export);
			$this->set('options', []);
			$this->layout = '';
			$this->render('exportcsv_tableau2_donnees');
		}

		/**
		 * Export CSV des données brutes du corpus pour le tableau 2
		 */
		public function exportcsv_tableau2_corpus() {

			$params['structure'] = isset($this->request->query['structure']) ? $this->request->query['structure'] : null;
			$params['referent']  = isset($this->request->query['referent']) && $this->request->query['referent'] != 0  ? $this->request->query['referent']  : null;
			$params['numcom']    = isset($this->request->query['numcom'])    ? $this->request->query['numcom']    : null;
			$params['date']    = isset($this->request->query['date'])        ? $this->request->query['date']    : null;

			$donnees = $this->requeteTableau2($params, false);

			//On calcule les catégories d'âge et d'ancienneté
			$donnees = $this->calculCategories($donnees, $params['date']);

			$colonnes = $this->colonnes_export_corpus_tdb2();
			$nom_colonne_date_export = $params['date'] == 'ajd' ? 'Date de l\'export' : 'Trimestre de l\'export';
			$export = array ();
			$i = 0;

			//Noms colonnes
			$export[$i++] = array_merge(
				[
					'Année de l\'export',
					$nom_colonne_date_export
				],
				array_keys($colonnes)
			);
			foreach($donnees as $personne){
				$personne = $personne[0];

				$ligne = [$personne['annee_export'], $personne['date_export']];
				foreach(array_values($colonnes) as $champ){
					$valeur = isset($personne[$champ]) ? $personne[$champ] : '';
					if(isset(self::LISTE_ENUMS[$champ])){
						if($valeur !== ''){
							if($valeur == false) {
								$valeur = 0;
							}
							$valeur = __d(self::LISTE_ENUMS[$champ][0], self::LISTE_ENUMS[$champ][1].$valeur);
						}
					}
					array_push($ligne, $valeur);
				}
				$export[$i++] = $ligne;
			}

			$this->set('export', $export);
			$this->set('options', []);
			$this->layout = '';
			$this->render('exportcsv_tableau2_corpus');
		}


		public function colonnes_export_corpus_tdb2(){

			//TODO V2: factoriser la base commune si possible avec tdb1
			return [
				//identifiants
				__d('tableauxbords93', 'Corpus.colonne.p_id') => 'personne_id',
				__d('tableauxbords93', 'Corpus.colonne.nir') => 'nir',
				__d('tableauxbords93', 'Corpus.colonne.numcaf') => 'caf',
				__d('tableauxbords93', 'Corpus.colonne.id_pe') => 'identifiant_pe',
				__d('tableauxbords93', 'Corpus.colonne.numdosrsa') => 'numrsa',
				//identité
				__d('tableauxbords93', 'Corpus.colonne.qual') => 'civilite',
				__d('tableauxbords93', 'Corpus.colonne.nom') => 'nom',
				__d('tableauxbords93', 'Corpus.colonne.nomnai') => 'nom_naissance',
				__d('tableauxbords93', 'Corpus.colonne.prenom') => 'prenom',
				__d('tableauxbords93', 'Corpus.colonne.dtnai') => 'date_naissance',
				//contact
				__d('tableauxbords93', 'Corpus.colonne.numvoie') => 'numvoie',
				__d('tableauxbords93', 'Corpus.colonne.libtypevoie') => 'libtypevoie',
				__d('tableauxbords93', 'Corpus.colonne.nomvoie') => 'nomvoie',
				__d('tableauxbords93', 'Corpus.colonne.codepos') => 'codepos',
				__d('tableauxbords93', 'Corpus.colonne.numcom') => 'numcom',
				__d('tableauxbords93', 'Corpus.colonne.nomcom') => 'nomcom',
				__d('tableauxbords93', 'Corpus.colonne.tel1') => 'tel1',
				__d('tableauxbords93', 'Corpus.colonne.tel2') => 'tel2',
				__d('tableauxbords93', 'Corpus.colonne.email') => 'email',
				//droit
				__d('tableauxbords93', 'Corpus.colonne.datedemrsa') => 'datedemrsa',
				__d('tableauxbords93', 'Corpus.colonne.etatdroit') => 'etatdroit',
				__d('tableauxbords93', 'Corpus.colonne.sdd') => 'sdd',
				__d('tableauxbords93', 'Corpus.colonne.statutpe') => 'statutpe',
				//socio demo
				__d('tableauxbords93', 'Corpus.colonne.nivetu') => 'nivetu',
				__d('tableauxbords93', 'Corpus.colonne.cat_age') => 'cat_age',
				__d('tableauxbords93', 'Corpus.colonne.cat_anciennete') => 'cat_anciennete',
				__d('tableauxbords93', 'Corpus.colonne.sexe') => 'sexe',
				//referent de parcours
				__d('tableauxbords93', 'Corpus.colonne.nom_ref') => 'nom_ref',
				__d('tableauxbords93', 'Corpus.colonne.refe_appartient_struct') => 'refe_appartient_struct',
				__d('tableauxbords93', 'Corpus.colonne.referent_actif') => 'referent_actif',
				//tag
				__d('tableauxbords93', 'Corpus.colonne.tag_diag') => 'tag_diag',
				__d('tableauxbords93', 'Corpus.colonne.date_creation_tag') => 'date_creation_tag',
				//sous categories
				__d('tableauxbords93', 'Corpus.colonne.nveau_orient') => 'nveau_orient',
				__d('tableauxbords93', 'Corpus.colonne.do_tjs_orient') => 'do_tjs_orient',
				__d('tableauxbords93', 'Corpus.colonne.sdd_tjs_orient') => 'sdd_tjs_orient',
				//derniere orient hors diag
				__d('tableauxbords93', 'Corpus.colonne.dohd_origine') => 'dohd_origine',
				__d('tableauxbords93', 'Corpus.colonne.dohd_date_valid') => 'dohd_date_valid',
				__d('tableauxbords93', 'Corpus.colonne.dohd_rgorient') => 'dohd_rgorient',
				__d('tableauxbords93', 'Corpus.colonne.dohd_type') => 'dohd_type',
				__d('tableauxbords93', 'Corpus.colonne.dohd_structurereferente') => 'dohd_structurereferente',
				//derniere orient diag
				__d('tableauxbords93', 'Corpus.colonne.dod_origine') => 'dod_origine',
				__d('tableauxbords93', 'Corpus.colonne.dod_structureorientante') => 'dod_structureorientante',
				__d('tableauxbords93', 'Corpus.colonne.dod_structurereferente') => 'dod_structurereferente',
				__d('tableauxbords93', 'Corpus.colonne.date_drih') => 'date_drih',
				__d('tableauxbords93', 'Corpus.colonne.dod_rgorient') => 'dod_rgorient',
				__d('tableauxbords93', 'Corpus.colonne.dod_type') => 'dod_type',
				//rdv et dsp
				__d('tableauxbords93', 'Corpus.colonne.nb_rdv_indiv') => 'nb_rdv_indiv',
				__d('tableauxbords93', 'Corpus.colonne.nb_rdv_coll') => 'nb_rdv_coll',
				__d('tableauxbords93', 'Corpus.colonne.dsp_vide') => 'dsp_vide',
				__d('tableauxbords93', 'Corpus.colonne.rdv_sans_dsp') => 'rdv_sans_dsp',
				__d('tableauxbords93', 'Corpus.colonne.date_drip') => 'date_drip',
				__d('tableauxbords93', 'Corpus.colonne.date_drih') => 'date_drih',
				__d('tableauxbords93', 'Corpus.colonne.date_drcp') => 'date_drcp',
				__d('tableauxbords93', 'Corpus.colonne.date_drch') => 'date_drch',
				__d('tableauxbords93', 'Corpus.colonne.pas_rdv_30j') => 'pas_rdv_30j',
				__d('tableauxbords93', 'Corpus.colonne.pas_rdv_60j') => 'pas_rdv_60j',
				__d('tableauxbords93', 'Corpus.colonne.rdv_prevu_passe') => 'rdv_prevu_passe',
				__d('tableauxbords93', 'Corpus.colonne.rdv_coll_sans_indiv') => 'rdv_coll_sans_indiv',
				//D1
				__d('tableauxbords93', 'Corpus.colonne.rdv_sans_d1') => 'rdv_sans_d1',
				__d('tableauxbords93', 'Corpus.colonne.d1_rempli') => 'd1_rempli',
				//CER
				__d('tableauxbords93', 'Corpus.colonne.cer_struct_valide') => 'cer_struct_valide',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_id') => 'dcerv_id',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_structurereferente_lib') => 'dcerv_structurereferente_lib',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_referent') => 'dcerv_referent',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_created') => 'dcerv_created',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_date_valid') => 'dcerv_date_valid',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_rang') => 'dcerv_rang',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_dd') => 'dcerv_dd',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_df') => 'dcerv_df',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_duree') => 'dcerv_duree',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_position') => 'dcerv_position',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_emploi') => 'dcerv_emploi',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_formation') => 'dcerv_formation',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_autonomie_soc') => 'dcerv_autonomie_soc',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_logement') => 'dcerv_logement',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_sante') => 'dcerv_sante',
				__d('tableauxbords93', 'Corpus.colonne.dcerv_autre') => 'dcerv_autre',
				__d('tableauxbords93', 'Corpus.colonne.rdv_sans_cer') => 'rdv_sans_cer',
				__d('tableauxbords93', 'Corpus.colonne.pas_cer_pas_rdv') => 'pas_cer_pas_rdv',
			];
		}

		public function calculCategories($donnees, $date_jour){
			
			$date_jour = new DateTime($this->getDateFromParameter($date_jour));

			foreach($donnees as $key => $personne){
				$cat_age = '';
				$cat_anciennete = '';
				$age = $personne[0]['age'];
				$date_demande = new DateTime($personne[0]['datedemrsa']);
				$anciennete = $date_demande->diff($date_jour);

				switch (true){
					case $age < 25 :
						$cat_age = "<25 ans";
						break;
					case $age >= 25 && $age < 30:
						$cat_age = "25-29 ans";
						break;
					case $age >= 30 && $age < 40:
						$cat_age = "30-39 ans";
						break;
					case $age >= 40 && $age < 50:
						$cat_age = "40-49 ans";
						break;
					case $age >= 50 && $age < 60:
						$cat_age = "50-59 ans";
						break;
					case $age >= 60 :
						$cat_age = ">=60 ans";
						break;
				}

				switch (true){
					case $anciennete->y == 0 :
						$cat_anciennete = 'Moins d\'un an';
						break;
					case $anciennete->y == 1 :
						$cat_anciennete = 'Plus d\'un an et moins de 2 ans';
						break;
					case $anciennete->y >=2 && $anciennete->y <5 :
						$cat_anciennete = 'Plus de 2 ans et moins de 5 ans';
						break;
					case $anciennete->y >= 5 && $anciennete->y <10 :
						$cat_anciennete = 'Plus de 5 ans et moins de 10 ans';
						break;
					case $anciennete->y >= 10 :
						$cat_anciennete = 'Plus de 10 ans';
						break;

				}

				$donnees[$key][0]['cat_age'] = $cat_age;
				$donnees[$key][0]['cat_anciennete'] = $cat_anciennete;

			}

			return $donnees;
		}

		public function getDateFromParameter($date){
			if($date == 'ajd'){
				$date_du_jour = strval(date("Y-m-d"));
			} else {
				$tab = explode('_', $date);
				$annee = $tab[0];
				$trimestre = $tab[1];

				$date_du_jour = $this->getDateFromTrimestre($trimestre, $annee);
			}

			return $date_du_jour;
		}

		public function getDateFromTrimestre($trimestre, $annee){
			switch($trimestre){
				case 1:
					$date_du_jour = $annee.'-03-31';
					break;
				case 2:
					$date_du_jour = $annee.'-06-30';
					break;
				case 3:
					$date_du_jour = $annee.'-09-30';
					break;
				case 4:
					$date_du_jour = $annee.'-12-31';
					break;
			}

			return $date_du_jour;
		}

    }