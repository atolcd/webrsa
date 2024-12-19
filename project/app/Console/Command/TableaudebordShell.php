<?php
	/**
	 * Fichier source de la classe Tableaudebord.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     *
     *
     * Arguments attendus : année, trimestre, tableau
     * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake Tableaudebord -app app
	 *
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::import( 'Controller', 'Tableauxbords93' );

	/**
	 * La classe Tableaudebord ...
	 *
	 * @package app.Console.Command
	 */
	class TableaudebordShell extends XShell
	{
		public $uses = [
			'Tdb2HistoCorpus',
			'Structurereferente',
			'Personne',
			'Tdb1ACorpus',
			'Tdb1BCorpus',
			'Tdb1CCorpus'
		];

        public function main(){

			//On vérifie que les arguments sont corrects
			//trimestre, annee, tableau (si vide, par défaut on enregistre tous les tableaux)


            if (!isset($this->args[0])) {
                $this->out("<error>Paramètres manquants</error>");
                exit();
            }

			//Tableau 1
			if($this->args[0] == 'tab1') {

				//Nombre de mois pendant lesquels on continue de mettre les données à jour
				$nb_mois_maj = Configure::read('Tableauxbords93.Tableau1.Shell.nombres_mois');
				//On récupère le mois et l'annee en cours
				$date_format = date('Y-m');
				$date = new Datetime($date_format);
				$interval = DateInterval::createFromDateString('1 month');

				//on definit les X mois à enregistrer
				$dates_a_enregistrer = [$date_format];
				$i = 0;
				while($i < $nb_mois_maj){
					array_push($dates_a_enregistrer, $date->sub($interval)->format('Y-m'));
					$i++;
				}

				foreach($dates_a_enregistrer as $date){
					$date = explode('-', $date);
					$annee = $date[0];
					$mois = $date[1];

					//on supprime les données du mois et on enregistre à nouveau
					$this->enregistrementTableau1($mois, $annee);
				}








				//tableau 2
			} else if ($this->args[0] == 'tab2') {
				if (!isset($this->args[2])) {
					$this->out("<error>Paramètres manquants</error>");
					exit();
				} else if (!in_array($this->args[2], ['T1', 'T2', 'T3', 'T4'])) {
				   $this->out("<error>Trimestre incorrect</error>");
				   exit();
			   } else if (!is_int((int)$this->args[1]) || strlen((string)$this->args[1]) != 4){
				   $this->out("<error>Année incorrecte</error>");
				   exit();
			   }

			   $trimestre = substr($this->args[2], -1);
			   $annee = $this->args[1];

			   //On vérifie que le couple trimestre /année n'a pas déjà été enregistré
			   $deja_enregistre = $this->Tdb2HistoCorpus->find(
				   'first',
				   [
					   'conditions' => [
						   'trimestre' => $trimestre,
						   'annee' => $annee
					   ]
				   ]
			   );

			   if (!empty($deja_enregistre)) {
				   $this->out("<error>Ce trimestre a déjà été enregistré</error>");
				   exit();
			   }

			   //On récupère le controller des tableaux de bord
			   $tdb = new Tableauxbords93Controller();

			   //On récupère la date du dernier jour du trimestre
			   $date_du_jour = $tdb->getDateFromTrimestre($trimestre, $annee);


			   //On récupère la liste des structures actives
			   $structures = $this->Structurereferente->find(
				   'list',
				   [
					   'conditions' => [
						   'Structurereferente.actif' => 'O'
						   ]
				   ]
			   );

			   //Pour chaque structure, on récupère les infos du corpus et on enregistre dans la table tdb2_histo_corpus
			   foreach($structures as $id_structure => $libelle){
				   //On récupère les données du corpus
				   $query_corpus = $tdb->sql_tab2_corpus(true, $date_du_jour, $annee, $id_structure, null, null, null);
				   $donnees_corpus = $this->Personne->query($query_corpus);

				   //On enregistre dans la table associée chaque personne
				   foreach($donnees_corpus as $data){
					   $data = $data[0];
					   $data['annee'] = $annee;
					   $data['trimestre'] = $trimestre;
					   $data['structure_referente'] = $id_structure;
					   $saved = $this->Tdb2HistoCorpus->save($data);
					   $this->Tdb2HistoCorpus->clear();
				   }
			   }
			} 
			// erreur
			else {
				$this->out("<error>Paramètre inconnu</error>");
				exit();
			}


        }


		public function enregistrementTableau1($mois, $annee) {

			//On supprime les données enregistrées pour le mois et
			// l'année en paramètres, pour toutes les structures
			//dans les 3 tables
			$sql_delete = "delete from tdb1_a_corpus where annee = $annee and mois = $mois;";
			$this->Personne->query($sql_delete);
			$sql_delete = "delete from tdb1_b_corpus where annee = $annee and mois = $mois;";
			$this->Personne->query($sql_delete);
			$sql_delete = "delete from tdb1_c_corpus where annee = $annee and mois = $mois;";
			$this->Personne->query($sql_delete);



			//On récupère toutes les structures référentes 
			$liste_structures = $this->Structurereferente->find(
				'list',
			[
				'conditions' => [
					'Structurereferente.actif' => 'O'
					]
			]);
			//Pour chacune des structures référentes, on enregistre les données
			//dans les 3 tables : tdb1_a_corpus, tdb1_b_corpus, tdb1_c_corpus
			foreach ($liste_structures as $structure_id => $libelle){
				$this->enregistrement_tdb1a($annee, $mois, $structure_id);
				$this->enregistrement_tdb1b($annee, $mois, $structure_id);
				$this->enregistrement_tdb1c($annee, $mois, $structure_id);
			}
		}

		public function enregistrement_tdb1a($annee, $mois, $structure_id){
			
			$sql = $this->requete_tdb1a($annee, $mois, $structure_id);
			$donnees = $this->Tdb1ACorpus->query($sql);


			foreach($donnees as $data){
				$data = $data[0];
				$data['annee'] = $annee;
				$data['mois'] = $mois;
				$data['structure_referente'] = $structure_id;
				$saved = $this->Tdb1ACorpus->save($data);
				$this->Tdb1ACorpus->clear();
			}
		}

		public function enregistrement_tdb1b($annee, $mois, $structure_id){
			
			$sql = $this->requete_tdb1b($annee, $mois, $structure_id);
			$donnees = $this->Tdb1BCorpus->query($sql);


			foreach($donnees as $data){
				$data = $data[0];
				$data['annee'] = $annee;
				$data['mois'] = $mois;
				$data['structure_referente'] = $structure_id;
				$saved = $this->Tdb1BCorpus->save($data);
				$this->Tdb1BCorpus->clear();
			}
		}

		public function enregistrement_tdb1c($annee, $mois, $structure_id){
			
			$sql = $this->requete_tdb1c($annee, $mois, $structure_id);
			$donnees = $this->Tdb1CCorpus->query($sql);


			foreach($donnees as $data){
				$data = $data[0];
				$data['annee'] = $annee;
				$data['mois'] = $mois;
				$data['structure_referente'] = $structure_id;
				$saved = $this->Tdb1CCorpus->save($data);
				$this->Tdb1CCorpus->clear();
			}
		}

		public function requete_tdb1a($annee, $mois, $structure_id){

			$statut_rdv_honore = '1';
			$type_rdv_indiv = '15';
			$type_rdv_coll = '14';

			return
			"
			with
			orient_mois_structure as 
			(
				select 
					o.personne_id,
					min(o.id) as orient_plus_ancienne_id,
					max(o.id) as orient_plus_recente_id
				from 
					orientsstructs o 
				where 
					o.statut_orient='Orienté'
					and o.structurereferente_id = $structure_id
					and extract(month from o.date_valid) = $mois 
					and extract(year from o.date_valid) = $annee
				group by o.personne_id
			)
			, nouveau_orient as 
			(
				select
				o.personne_id as personne_id,
					(
						o_1.id is null 
						or o_1.structurereferente_id <> o.structurereferente_id
					) as nveau_orient
				from orient_mois_structure oms
				join orientsstructs o on o.id = oms.orient_plus_ancienne_id
				left join orientsstructs o_1 on o.personne_id = o_1.personne_id and o_1.rgorient = o.rgorient - 1
			)
			, assiette as 
			(
				select 
					o.personne_id,
					o.rgorient,
					o.structurereferente_id,
					o.structureorientante_id,
					o.origine,
					o.referentorientant_id,
					case when vt.id is not null then true else false end as tag_diag,
					case when vt.id is not null then t.created else null end as date_tag
				from 
					orient_mois_structure oms
					left join orientsstructs o on o.id = oms.orient_plus_recente_id
					left join entites_tags et on et.fk_value = o.personne_id and et.modele = 'Personne'
					left join tags t on t.id = et.tag_id 
					left join valeurstags vt on vt.id = t.valeurtag_id and vt.name = 'Entretien de diagnostic'
			) 
			, dernier_droit as
			(
				select
				h.id,
					h.personne_id,
					h.toppersdrodevorsa,
					h.etatdosrsa,
					h.created,
					rank() over(partition by h.personne_id order by created desc, id desc) as rang
				from historiquesdroits h join assiette a on a.personne_id = h.personne_id
			)
			, dernier_historique_pe as
			(
				select 
				p.id as personne_id,
				h.id as historique_id,
				h.identifiantpe as identifiant_pe,
				h.etat as etatpe,
				rank() over(partition by h.informationpe_id order by h.date_creation desc, h.id desc) as rang
				from historiqueetatspe h join informationspe i on h.informationpe_id = i.id
				join personnes p on SUBSTRING( i.nir FROM 1 FOR 13 ) = SUBSTRING( p.nir FROM 1 FOR 13 )
				join assiette a on a.personne_id = p.id
			)
			, derniere_dsp_rev as
			(
				SELECT 
				dr.id,
				a.personne_id,
				dr.nivetu as nivetu,
				rank() over(partition by dr.personne_id order by dr.modified desc, dr.id desc) as rang
				from assiette a left join dsps_revs dr on dr.personne_id = a.personne_id
			), referent_actuel as
			(
				select
				a.personne_id,
				pr.referent_id,
				pr.structurereferente_id,
				r.nom,
				r.id,
				r.prenom,
				r.actif,
				rank() over(partition by pr.personne_id order by pr.dddesignation desc, pr.id desc) as rang
				from
				assiette a join personnes_referents pr on pr.personne_id = a.personne_id and dfdesignation is null
				join referents r on r.id = pr.referent_id
			), id_derniere_orient_hors_diag as
			(
				select
				o.personne_id,
				max(o.rgorient) as rgmax
				from orientsstructs o join assiette a on a.personne_id = o.personne_id
				where o.statut_orient = 'Orienté'
				and o.structurereferente_id = $structure_id
				and o.origine <> 'entdiag'
				group by o.personne_id
			), derniere_orient_hors_diag as 
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
			), id_derniere_orient_diag as
			(
				select
				o.personne_id, 
				max(o.rgorient) as rgmax
				from orientsstructs o join assiette a on a.personne_id = o.personne_id
				where o.statut_orient = 'Orienté'
				and o.structureorientante_id = $structure_id
				and o.origine = 'entdiag'
				group by o.personne_id
			), derniere_orient_diag as 
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
			), nombre_rdv as 
			(
				select 
				rdv.personne_id,
				count(*) filter (where typerdv_id = $type_rdv_indiv and rdv.statutrdv_id = $statut_rdv_honore) as nb_rdv_indiv,
				count(*) filter (where typerdv_id = $type_rdv_coll and rdv.statutrdv_id = $statut_rdv_honore) as nb_rdv_coll
				from assiette a join rendezvous rdv on rdv.personne_id = a.personne_id
				where rdv.structurereferente_id = $structure_id
				group by rdv.personne_id
			), cer_structure as 
			(
				select 
				a.personne_id,
				count(ci.id) as nb_cer
				from assiette a join contratsinsertion ci on ci.personne_id = a.personne_id
				where decision_ci = 'V'
				and dd_ci <= '$annee-12-31' and df_ci >= '$annee-01-01'
				and ci.structurereferente_id = $structure_id
				group by a.personne_id
			)
			select 
				--export
				$annee as annee,
				$mois as mois,
				$structure_id as structure_referente,
				--identifiants
				a.personne_id as personne_id,
				p.nir as nir,
				d.matricule as caf,
				dhpe.identifiant_pe as identifiant_pe,
				d.numdemrsa as numrsa,
				--identite
				p.qual as civilite,
				p.nom as nom,
				p.nomnai as nom_naissance,
				p.prenom as prenom,
				p.dtnai as date_naissance,
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
				--socio demo
				case when dsprev.id is not null then dsprev.nivetu else dsp.nivetu end as nivetu,
				( EXTRACT ( YEAR FROM AGE(p.dtnai) ) ) as age,
				p.sexe as sexe,
				-- referent de parcours
				refe.nom || ' ' || refe.prenom as nom_ref,
				refe.id as id_ref,
				case when dohd.date_valid >= dod.date_valid or dod.id is null
				then refe.structurereferente_id = dohd.structurereferente_id
				when dohd.date_valid < dod.date_valid or dohd.id is null
				then refe.structurereferente_id = dod.structurereferente_id
				end as refe_appartient_struct,
				refe.actif as referent_actif,
				--tag 
				a.tag_diag as tagdiag,
				a.date_tag as date_creation_tag,
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
				dod.lib_type_orient as dhd_type,
				dod.structurereferente as dod_structurereferente,
				dod.structurereferente_id as dod_structurereferente_id,
				dod.structureorientante as dod_structureorientante,
				--rdv
				nbrdv.nb_rdv_indiv,
				nbrdv.nb_rdv_coll,
				--cer
				case when cs.nb_cer > 0 then true else false end as cer_struct_valide,
				nveau.nveau_orient,
				a.origine as origine_assiette,
				a.structurereferente_id as structuref_assiette,
				a.structureorientante_id as structorient_assiette,
				a.referentorientant_id as referent_assiette
			from assiette a
				join nouveau_orient nveau on nveau.personne_id = a.personne_id
				join personnes p on p.id = a.personne_id
				join foyers f on f.id = p.foyer_id 
				join dossiers d on d.id = f.dossier_id 
				join dernier_droit dd on dd.personne_id = a.personne_id and dd.rang = 1
				join adressesfoyers adf on adf.foyer_id = f.id and adf.rgadr = '01'
				join adresses adr on adr.id = adf.adresse_id
				left join dernier_historique_pe dhpe on dhpe.rang = 1 and dhpe.personne_id = p.id
				left join derniere_dsp_rev dsprev on dsprev.personne_id = p.id and dsprev.rang = 1
				left join dsps dsp on dsp.personne_id = p.id
				left join referent_actuel refe on refe.personne_id = p.id and refe.rang = 1
				left join derniere_orient_hors_diag dohd on dohd.personne_id = p.id
				left join derniere_orient_diag dod on dod.personne_id = p.id
				left join nombre_rdv nbrdv on nbrdv.personne_id = p.id
				left join cer_structure cs on cs.personne_id = p.id
			";
		}


		public function requete_tdb1b($annee, $mois, $structure_id){

			return
			"
				with assiette as
				(
					select
					r.id as rdv_id,
					r.personne_id,
					r.structurereferente_id,
					r.referent_id,
					r.daterdv,
					r.heurerdv,
					r.typerdv_id,
					r.statutrdv_id,
					r.objetrdv,
					r.commentairerdv,
					case when vt.id is not null then true else false end as tag_diag,
					case when vt.id is not null then t.created else null end as date_tag
					from rendezvous r
					left join entites_tags et on et.fk_value = r.personne_id and et.modele = 'Personne'
					left join tags t on t.id = et.tag_id
					left join valeurstags vt on vt.id = t.valeurtag_id and vt.name = 'Entretien de diagnostic'
					where extract(month from r.daterdv) = $mois
					and extract(year from r.daterdv) = $annee
					and structurereferente_id = $structure_id
				),
				id_orient AS
				(
					SELECT
					a.rdv_id,
					max(o.id) as orientid
					FROM assiette a
					JOIN orientsstructs o ON o.personne_id = a.personne_id
					left join orientsstructs oplusun on oplusun.rgorient = o.rgorient + 1
					WHERE o.date_valid <= a.daterdv and (oplusun.id is null or oplusun.date_valid > a.daterdv)
					group by a.rdv_id
				),
				dernier_droit as
				(
					select
						h.id,
						h.personne_id,
						h.toppersdrodevorsa,
						h.etatdosrsa,
						h.created,
						rank() over(partition by h.personne_id order by h.created desc, h.id desc) as rang
					from historiquesdroits h join assiette a on a.personne_id = h.personne_id
				)
				, dernier_historique_pe as
				(
					select
					p.id as personne_id,
					h.id as historique_id,
					h.identifiantpe as identifiant_pe,
					h.etat as etatpe,
					rank() over(partition by h.informationpe_id order by h.date_creation desc, h.id desc) as rang
					from historiqueetatspe h join informationspe i on h.informationpe_id = i.id
					join personnes p on SUBSTRING( i.nir FROM 1 FOR 13 ) = SUBSTRING( p.nir FROM 1 FOR 13 )
					join assiette a on a.personne_id = p.id
				)
				, derniere_dsp_rev as
				(
					SELECT
					dr.id,
					a.personne_id,
					dr.nivetu as nivetu,
					rank() over(partition by dr.personne_id order by dr.modified desc, dr.id desc) as rang
					from assiette a left join dsps_revs dr on dr.personne_id = a.personne_id
				), referent_actuel as
				(
					select
					a.personne_id,
					pr.referent_id,
					pr.structurereferente_id,
					r.nom,
					r.id,
					r.prenom,
					r.actif,
					rank() over(partition by pr.personne_id order by pr.dddesignation desc, pr.id desc) as rang
					from
					assiette a join personnes_referents pr on pr.personne_id = a.personne_id and dfdesignation is null
					join referents r on r.id = pr.referent_id
				), liste_thematiquesrdv as
				(
					select
					a.rdv_id,
					array_agg(rt.thematiquerdv_id) as tab_thematiques
					from assiette a join rendezvous_thematiquesrdvs rt on rt.rendezvous_id = a.rdv_id
					group by a.rdv_id
				), thematiquesrdv as
				(
					select
					rdv_id,
					10 = any(tab_thematiques) as thematique_cer,
					13 = any(tab_thematiques) as thematique_encoursdeparcours,
					5 = any(tab_thematiques) as thematique_premierrdv,
					12 = any(tab_thematiques) as thematique_autre,
					case when (not (10 = any(tab_thematiques)) and not(13 = any(tab_thematiques)) and not(5 = any(tab_thematiques)) and not(12 = any(tab_thematiques)))
					then tab_thematiques[1] end as thematique_collectif
					from liste_thematiquesrdv
				)
				select
				--export
					distinct on (a.rdv_id)
					$annee as annee,
					$mois as mois,
					$structure_id as structure_referente,
					--identifiants
					a.personne_id as personne_id,
					p.nir as nir,
					d.matricule as caf,
					dhpe.identifiant_pe as identifiant_pe,
					d.numdemrsa as numrsa,
					--identite
					p.qual as civilite,
					p.nom as nom,
					p.nomnai as nom_naissance,
					p.prenom as prenom,
					p.dtnai as date_naissance,
				--	contact
					adr.numvoie as numvoie,
					adr.libtypevoie as libtypevoie,
					adr.nomvoie as nomvoie,
					adr.codepos as codepos,
					adr.numcom as numcom,
					adr.nomcom as nomcom,
					p.numfixe as tel1,
					p.numport as tel2,
					p.email as email,
				--	 droit
					d.dtdemrsa as datedemrsa,
					dd.etatdosrsa as etatdroit,
					dd.toppersdrodevorsa as sdd,
					dhpe.etatpe as statutpe,
				--	socio demoA
					case when dsprev.id is not null then dsprev.nivetu else dsp.nivetu end as nivetu,
					( EXTRACT ( YEAR FROM AGE(p.dtnai) ) ) as age,
					p.sexe as sexe,
				--	 referent de parcours
					refe.nom || ' ' || refe.prenom as nom_ref,
					refe.id as id_ref,
					refe.structurereferente_id = o.structurereferente_id as refe_appartient_struct,
					refe.actif as referent_actif,
				--	tag
					a.tag_diag as tagdiag,
					a.date_tag as date_creation_tag,
				--	orientation
					o.id as orient_id,
					o.origine as orient_origine,
					o.typeorient_id as orient_type,
					o.date_valid as orient_date,
					o.rgorient as orient_rang,
				--	assiette
					o.structurereferente_id as orient_structuref,
					o.structureorientante_id as structorient_assiette,
					r.referent_id as referent_assiette,
				--	rendezvous
					r.id as rdv_id,
					r.structurereferente_id as rdv_structurereferente,
					r.referent_id as rdv_referent,
					r.daterdv as rdv_date,
					r.heurerdv as rdv_heure,
					trdv.libelle as rdv_type,
					s.libelle as rdv_statut,
					r.objetrdv as rdv_objectif,
					r.commentairerdv as rdv_commentaire,
					-- thématique
					t.thematique_cer,
					t.thematique_encoursdeparcours,
					t.thematique_premierrdv,
					t.thematique_autre,
					th.name as thematique_collectif
				from assiette a
					join personnes p on p.id = a.personne_id
					join foyers f on f.id = p.foyer_id
					join dossiers d on d.id = f.dossier_id
					join dernier_droit dd on dd.personne_id = a.personne_id and dd.rang = 1
					join adressesfoyers adf on adf.foyer_id = f.id and adf.rgadr = '01'
					join adresses adr on adr.id = adf.adresse_id
					join id_orient idorient on idorient.rdv_id = a.rdv_id
					join orientsstructs o on o.id = idorient.orientid
					join rendezvous r on r.id = a.rdv_id
					left join typesrdv trdv on trdv.id = r.typerdv_id
					left join statutsrdvs s on s.id = r.statutrdv_id
					left join dernier_historique_pe dhpe on dhpe.rang = 1 and dhpe.personne_id = p.id
					left join derniere_dsp_rev dsprev on dsprev.personne_id = p.id and dsprev.rang = 1
					left join dsps dsp on dsp.personne_id = p.id
					left join referent_actuel refe on refe.personne_id = p.id and refe.rang = 1
					left join thematiquesrdv t on t.rdv_id = r.id
					left join thematiquesrdvs th on th.id = t.thematique_collectif
			";
		}

		public function requete_tdb1c($annee, $mois, $structure_id){

			return 
			"
				with assiette as
				(
					select 
					c2.id as cer_id,
					c.personne_id as personne_id,
					c.structurereferente_id as cer_structure,
					c.referent_id as cer_referent,
					c2.created as cer_created,
					c.datevalidation_ci as cer_datevalidcd,
					c.rg_ci as cer_rang,
					c.dd_ci as cer_dd,
					c.df_ci as cer_df,
					c2.duree as cer_duree,
					c2.positioncer as cer_statut,
					c2.datesignature as cer_datesignature,
					case when vt.id is not null then true else false end as tag_diag,
					case when vt.id is not null then t.created else null end as date_tag
					from contratsinsertion c 
					join cers93 c2 on c2.contratinsertion_id = c.id
					left join entites_tags et on et.fk_value = c.personne_id and et.modele = 'Personne'
					left join tags t on t.id = et.tag_id 
					left join valeurstags vt on vt.id = t.valeurtag_id and vt.name = 'Entretien de diagnostic'
					where c2.positioncer not in ('00enregistre', '99rejete', '99rejetecpdv', '99annule')
					and extract(month from c.dd_ci) = $mois
					and extract(year from c.dd_ci) = $annee
					and c.structurereferente_id = $structure_id 
				),
				liste_sujetscer as 
				(
					select 
					a.cer_id,
					array_agg(cs.sujetcer93_id) as tab_sujets
					from assiette a join cers93_sujetscers93 cs on cs.cer93_id = a.cer_id
					group by a.cer_id
				),
				sujetsscer as 
				(
					select 
					cer_id,
					1 = any(tab_sujets) as sujet_emploi,
					2 = any(tab_sujets) as sujet_formation,
					3 = any(tab_sujets) as sujet_autonomie,
					4 = any(tab_sujets) as sujet_logement,
					5 = any(tab_sujets) as sujet_sante,
					6 = any(tab_sujets) as sujet_autre
					from liste_sujetscer
				),
				id_orient AS 
				(
					SELECT 
					a.cer_id,
					max(o.id) as orientid
					FROM assiette a
					JOIN orientsstructs o ON o.personne_id = a.personne_id
					left join orientsstructs oplusun on oplusun.rgorient = o.rgorient + 1
					WHERE o.date_valid <= a.cer_datesignature and (oplusun.id is null or oplusun.date_valid > a.cer_datesignature)
					group by a.cer_id
				),
				dernier_droit as
				(
					select
						h.id,
						h.personne_id,
						h.toppersdrodevorsa,
						h.etatdosrsa,
						h.created,
						rank() over(partition by h.personne_id order by h.created desc, h.id desc) as rang
					from historiquesdroits h join assiette a on a.personne_id = h.personne_id
				)
				, dernier_historique_pe as
				(
					select 
					p.id as personne_id,
					h.id as historique_id,
					h.identifiantpe as identifiant_pe,
					h.etat as etatpe,
					rank() over(partition by h.informationpe_id order by h.date_creation desc, h.id desc) as rang
					from historiqueetatspe h join informationspe i on h.informationpe_id = i.id
					join personnes p on SUBSTRING( i.nir FROM 1 FOR 13 ) = SUBSTRING( p.nir FROM 1 FOR 13 )
					join assiette a on a.personne_id = p.id
				)
				, derniere_dsp_rev as
				(
					SELECT 
					dr.id,
					a.personne_id,
					dr.nivetu as nivetu,
					rank() over(partition by dr.personne_id order by dr.modified desc, dr.id desc) as rang
					from assiette a left join dsps_revs dr on dr.personne_id = a.personne_id
				), referent_actuel as
				(
					select
					a.personne_id,
					pr.referent_id,
					pr.structurereferente_id,
					r.nom,
					r.id,
					r.prenom,
					r.actif,
					rank() over(partition by pr.personne_id order by pr.dddesignation desc, pr.id desc) as rang
					from
					assiette a join personnes_referents pr on pr.personne_id = a.personne_id and dfdesignation is null
					join referents r on r.id = pr.referent_id
				)
				select 
					distinct on (a.cer_id)
					$annee as annee,
					$mois as mois,
					$structure_id as structure_referente,
					--identifiants
					a.personne_id as personne_id,
					p.nir as nir,
					d.matricule as caf,
					dhpe.identifiant_pe as identifiant_pe,
					d.numdemrsa as numrsa,
					--identite
					p.qual as civilite,
					p.nom as nom,
					p.nomnai as nom_naissance,
					p.prenom as prenom,
					p.dtnai as date_naissance,
				--	contact
					adr.numvoie as numvoie,
					adr.libtypevoie as libtypevoie,
					adr.nomvoie as nomvoie,
					adr.codepos as codepos,
					adr.numcom as numcom,
					adr.nomcom as nomcom,
					p.numfixe as tel1,
					p.numport as tel2,
					p.email as email,
				--	 droit
					d.dtdemrsa as datedemrsa,
					dd.etatdosrsa as etatdroit,
					dd.toppersdrodevorsa as sdd,
					dhpe.etatpe as statutpe,
				--	socio demoA
					case when dsprev.id is not null then dsprev.nivetu else dsp.nivetu end as nivetu,
					( EXTRACT ( YEAR FROM AGE(p.dtnai) ) ) as age,
					p.sexe as sexe,
				--	 referent de parcours
					refe.nom || ' ' || refe.prenom as nom_ref,
					refe.id as id_ref,
					refe.structurereferente_id = o.structurereferente_id as refe_appartient_struct,
					refe.actif as referent_actif,
				--	tag 
					a.tag_diag as tagdiag,
					a.date_tag as date_creation_tag,
				--	orientation
					o.id as orient_id,
					o.origine as orient_origine,
					o.typeorient_id as orient_type,
					o.date_valid as orient_date,
					o.rgorient as orient_rang,
				--	assiette
					o.structurereferente_id as orient_structuref,
					o.structureorientante_id as structorient_assiette,
					a.cer_referent as referent_assiette,
				--  cer
					a.cer_id,
					a.cer_structure,
					a.cer_referent,
					a.cer_created,
					a.cer_datevalidcd,
					a.cer_rang,
					a.cer_dd,
					a.cer_df,
					a.cer_duree,
					a.cer_statut,
				-- sujets cer
					s.sujet_emploi,
					s.sujet_formation,
					s.sujet_logement,
					s.sujet_sante,
					s.sujet_autonomie,
					s.sujet_autre
				from assiette a
					join personnes p on p.id = a.personne_id
					join foyers f on f.id = p.foyer_id 
					join dossiers d on d.id = f.dossier_id 
					join dernier_droit dd on dd.personne_id = a.personne_id and dd.rang = 1
					join adressesfoyers adf on adf.foyer_id = f.id and adf.rgadr = '01'
					join adresses adr on adr.id = adf.adresse_id
					join id_orient idorient on idorient.cer_id = a.cer_id
					join orientsstructs o on o.id = idorient.orientid
					left join dernier_historique_pe dhpe on dhpe.rang = 1 and dhpe.personne_id = p.id
					left join derniere_dsp_rev dsprev on dsprev.personne_id = p.id and dsprev.rang = 1
					left join dsps dsp on dsp.personne_id = p.id
					left join referent_actuel refe on refe.personne_id = p.id and refe.rang = 1
					left join sujetsscer s on s.cer_id = a.cer_id
			";

		}
    }