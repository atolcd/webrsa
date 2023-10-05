<?php
	/**
	 * Fichier source de la classe WebrsaALIExportXMLShell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     *
     * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake WebrsaALIExportXML -app app [Filepath] [diff]
	 *
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe WebrsaALIExportXMLShell ...
	 *
	 * @package app.Console.Command
	 */
	class WebrsaALIExportXMLShell extends XShell
	{
		public $uses = [
            'CorrespondanceReferentiel',
            'SujetReferentiel',
            'Structurereferente',
            'StructurereferenteZonegeographique',
			'Personne',
			'PersonneReferent',
			'RapportEchangeALI'
        ];

        public $date_dernier = null;
        public $stock = true;


		/**
		 *
		 */
		public function main() {
            // Vérification du path passé en argument
            if (!isset($this->args[0])) {
                $this->out("<error>Il n'y a pas de chemin de dossier passé en argument</error>");
                exit();
            } else if (substr($this->args[0], -1) !== '/') {
                $path = $this->args[0] . '/';
            } else {
                $path = $this->args[0];
            }

            // Vérification que l'écriture est possible dans le dossier spécifié
            if (file_put_contents($path . 'test', '') === false) {
                $this->out("<error>Il n'est pas possible d'écrire un fichier dans le dossier spécifié</error>");
                exit();
            } else {
                unlink($path . 'test');
            }

			if (isset($this->args[1])) {
                if(($this->args[1]) == 'diff'){
                    $this->out("<comment>Création des fichiers différentiels</comment>");
                    $this->stock = false;
                } else {
                    $this->out("<error>Argument ".$this->args[1]." inconnu</error>");
                    exit();
                }
            } else {
                $this->out("<comment>Création des fichiers stock</comment>");
            }

			//On récupère les ALI pour lesquelles il faut générer un fichier
            $alis = $this->Structurereferente->getALIexport();

			$now = new DateTime();
			$query_dernier =
			"
				select created from administration.rapportsechangesali r left join administration.erreursechangesali e on e.rapport_id = r.id
				where r.type = 'export'
				and e.id is null
				order by r.created desc, r.id desc
				limit 1
			";


			$this->date_dernier = isset($this->RapportEchangeALI->query($query_dernier)[0][0]['created']) ? $this->RapportEchangeALI->query($query_dernier)[0][0]['created'] : $now->format('Y-m-d');


            //on boucle sur chaque ALI pour créer un fichier par ALI
            foreach ($alis as $ali){
				$now = new DateTime();

                if(!$this->stock){
                    $file = $path . 'export_webrsa_ali_'.$ali['Structurereferente']['id'].'_diff_'. $now->format('Y-m-d_H-i-s') . '.xml';
                } else {
                    $file = $path . 'export_webrsa_ali_'.$ali['Structurereferente']['id'].'_stock_'. $now->format('Y-m-d_H-i-s') . '.xml';
                }

				//on récupère la liste des zones geographiques associées à la structure
				$zonesgeo_ali = $this->CorrespondanceReferentiel->getZonesGeoALI($ali['Structurereferente']['id']);

				//On récupère la liste des structures associées à la zone géographiques + celles associées à aucune zone
				$structuresOK = $this->StructurereferenteZonegeographique->getStructuresOkParZonesGeos($zonesgeo_ali);


				//Récupération des données
				//Récupération des id des personnes entrant des les critères du stock
				$sql_stock =
				"
				with listeHisto as
				(
					select
						id,
						etatdosrsa,
						personne_id,
						toppersdrodevorsa,
						created,
						rank() OVER (PARTITION BY (personne_id) ORDER BY created desc, id desc) AS rang,
						CASE
							WHEN
								lag(historiquedroit.etatdosrsa) OVER (PARTITION BY historiquedroit.personne_id ORDER BY historiquedroit.created) != historiquedroit.etatdosrsa
								OR lag(historiquedroit.etatdosrsa) OVER (PARTITION BY historiquedroit.personne_id ORDER BY historiquedroit.created) IS NULL
							THEN true
							ELSE false
						END AS first_date,
						CASE
							WHEN
								lag(historiquedroit.toppersdrodevorsa) OVER (PARTITION BY historiquedroit.personne_id ORDER BY historiquedroit.created) != historiquedroit.toppersdrodevorsa
							THEN true
							ELSE false
						END AS modif_sdd
					from historiquesdroits historiquedroit
				),
				dernierHisto as
				(
					select distinct on (l1.id)
					l1.id,
					l1.etatdosrsa,
					l1.personne_id,
					l1.toppersdrodevorsa,
					l1.modif_sdd,
					l2.created,
					l2.created as firstcreated
					from listeHisto l1 join listeHisto l2
						on l2.first_date = true
						and l1.personne_id = l2.personne_id
						and l2.created <= l1.created
					where l1.rang = 1
					order by l1.id, l2.created desc
				),
				liste_personnes as
				(
					select
					p.modified,
					p.foyer_id as foyer_id,
					p.modified_numfixe as modified_numfixe,
					p.numfixe as numfixe,
					p.modified_numport as modified_numport,
					p.numport as numport,
					p.modified_email as modified_email,
					p.email as email,
					p.qual as qual,
					p.nom as nom,
					p.prenom as prenom,
					p.nir as nir,
					p.dtnai as dtnai,
					p2.rolepers as rolepers,
					dh.created as dh_created,
					dh.etatdosrsa as dh_etatdos,
					dh.firstcreated as dh_firstcreated,
					dh.toppersdrodevorsa as dh_toppersdrodevorsa,
					dh.modif_sdd as dh_modif_sdd,
					p.id as p_id
					from personnes p
					join prestations p2 on p2.personne_id = p.id and p2.natprest = 'RSA' and p2.rolepers in ('DEM', 'CJT')
					join dernierHisto dh on dh.personne_id = p.id and (dh.etatdosrsa in ('2', '3', '4') or (dh.firstcreated > '{$this->date_dernier}'))
				),
				liste_personnes_filtre_adresse as
				(
					select
					p.*,
					a.numvoie as numvoie,
					a.libtypevoie as typevoie,
					a.nomvoie as nomvoie,
					a.compladr as compladr,
					a.codepos as codepos,
					a.nomcom as nomcom,
					af.dtemm as dtemm,
					f.ddsitfam as ddsitfam,
					d.dtdemrsa as dtdemrsa,
					d.numdemrsa as numdemrsa,
					d.matricule as matricule,
					f.sitfam as sitfam,
					f.modified as modified_foyer,
					d2.modified as modified_details,
					a.modified as modified_adresse,
					af.modified as modified_adressefoyer,
					d2.nbenfautcha as nbenfautcha
					from liste_personnes p
					join foyers f on f.id = p.foyer_id
					join adressesfoyers af on af.foyer_id = f.id and af.rgadr = '01'
					join adresses a on a.id = af.adresse_id
					join zonesgeographiques z on z.codeinsee = a.numcom
					join dossiers d on d.id = f.dossier_id
					join detailsdroitsrsa d2 on d2.dossier_id  = f.dossier_id
					where z.id in {$zonesgeo_ali}
				),
				derniereModifOrient as
				(
					select o.personne_id, max(o.modified) as date_modif
					from orientsstructs o
					join liste_personnes_filtre_adresse l on l.p_id = o.personne_id
					group by o.personne_id
				),
				derniereModifReferent as
				(
					select pr.personne_id, max(pr.modified) as date_modif
					from personnes_referents pr
					join liste_personnes_filtre_adresse l on l.p_id = pr.personne_id
					group by pr.personne_id
				),
				derniereModifCer as
				(
					select c.personne_id, greatest(max(c.modified), max(c2.modified)) as date_modif
					from contratsinsertion c
					join cers93 c2 on c2.contratinsertion_id = c.id
					join liste_personnes_filtre_adresse l on l.p_id = c.personne_id
					group by c.personne_id
				),
				derniereModifDsp as
				(
					select d.personne_id, greatest(max(d.created), max(dr.modified)) as date_modif
					from dsps d join dsps_revs dr on dr.dsp_id = d.id
					join liste_personnes_filtre_adresse l on l.p_id = d.personne_id
					group by d.personne_id
				)
				select
				p.p_id,
				p.*,
				(p.modified_adresse > '{$this->date_dernier}' or p.modified_adressefoyer > '{$this->date_dernier}') as modif_adresse,
				p.modified_foyer > '{$this->date_dernier}' as modif_foyer,
				p.modified > '{$this->date_dernier}' as modif_personne,
				p.modified_details > '{$this->date_dernier}' as modif_details,
				(p.dh_modif_sdd and p.dh_created > '{$this->date_dernier}') as modif_sdd,
				(p.dh_firstcreated > '{$this->date_dernier}') as modif_etatdos,
				(p.modified_numfixe > '{$this->date_dernier}' or p.modified_numport > '{$this->date_dernier}' or p.modified_email > '{$this->date_dernier}') as modif_contact,
				(dmo.date_modif > '{$this->date_dernier}') as modif_orient,
				(dmr.date_modif > '{$this->date_dernier}') as modif_referent,
				(dmc.date_modif > '{$this->date_dernier}') as modif_cer,
				(dmd.date_modif > '{$this->date_dernier}') as modif_dsp
				from liste_personnes_filtre_adresse p
				left join derniereModifOrient dmo on dmo.personne_id = p.p_id
				left join derniereModifReferent dmr on dmr.personne_id = p.p_id
				left join derniereModifCer dmc on dmc.personne_id = p.p_id
				left join derniereModifDsp dmd on dmd.personne_id = p.p_id
				";

				$liste_personnes = $this->Personne->query($sql_stock);

				//mapper sur les résultats pour crééer un tableau avec toutes les données ???
				$donnees = [];
				$liste_personnes_id = [];
				foreach($liste_personnes as $personne){
					$personne = $personne[0];

					$modif_allocataire = ($personne['modif_adresse'] || $personne['modif_foyer'] || $personne['modif_sdd'] || $personne['modif_etatdos'] || $personne['modif_details'] || $personne['modif_personne']);
					$modif_referent = $personne['modif_referent'];
					$modif_contact = $personne['modif_contact'];
					$modif_orient = $personne['modif_orient'];
					$modif_cer = $personne['modif_cer'];
					$modif_dsp = $personne['modif_dsp'];


					//on ne garde la personne qui si on est dans le stock ou s'il y a des modifs
					if($this->stock || $modif_allocataire || $modif_contact || $modif_orient || $modif_referent  || $modif_cer || $modif_dsp){

						//Allocataire
						if($this->stock || $modif_allocataire){

							$donnees[$personne['p_id']]['allocataire'] = [
								'civilite' => $personne['qual'],
								'nom' => $personne['nom'],
								'prenom' => $personne['prenom'],
								'nir' => ($personne['nir'] != null) ? substr($personne['nir'], 0, 13) : null,
								'numero_caf' => substr($personne['matricule'], 0, 7),
								'date_naissance' => $personne['dtnai'],
								'numero_voie' => $personne['numvoie'],
								'type_voie' => $personne['typevoie'],
								'nom_voie' => $personne['nomvoie'],
								'complement_adresse' => $personne['compladr'],
								'code_postal' => $personne['codepos'],
								'ville' => $personne['nomcom'],
								'date_demande_rsa' => $personne['dtdemrsa'],
								'numero_demande_rsa' => $personne['numdemrsa'],
								'etat_droit' => $personne['dh_etatdos'],
								'soumis_droits_devoirs' => $personne['dh_toppersdrodevorsa'],
								'role_personne' => $personne['rolepers'],
								'nb_enfants_foyer' => $personne['nbenfautcha'],
								'situation_familiale' => $personne['sitfam'],
							];
						}

						//Référent
						if($this->stock || $modif_referent){
							$donnees = $this->donneesReferent($personne, $donnees, $structuresOK);
						}

						//Contact
						if($this->stock || $modif_contact){
							$donnees = $this->donneesContact($personne, $donnees);
						}

						//Orientation
						if($this->stock || $modif_orient){
							$donnees = $this->donneesOrientation($personne, $donnees);
						}

						//Cer
						if($this->stock || $modif_cer){
							$donnees = $this->donneesCer($personne, $donnees);
						}

						//Dsp
						if($this->stock || $modif_dsp){
							$donnees = $this->donneesDsp($personne, $donnees);
						}

						$liste_personnes_id[] = ['personne_id' => $personne['p_id']];
					}

				}


				//écriture du fichier XML
				$this->_ecritureXml($donnees, $file, $now, $ali);


				//Validation du format
				$dom = new DOMDocument;
                $dom->load($file);
                $schema_valide = $dom->schemaValidate(Configure::read('EchangeALI.CheminValidation').'/WebRSA-ALI.xsd');

				 // Ecriture dans la table de rapports
				 $rapport['RapportEchangeALI'] = [
                    'nom_fichier' => $file,
                    'type' => 'export',
                    'debut' => $now->format('Y-m-d_H:i:s'),
                    'ali_id' => $ali['Structurereferente']['id'],
                    'stock' => $this->stock
                ];

				//On enregistre toutes les personnes associées au flux
				$rapport['PersonneEchangeALI'] = $liste_personnes_id;

                //Si le schéma n'est pas validé on enregistre une erreur
                if(!$schema_valide){
					$rapport['ErreurEchangeALI'] = [
						[
							'code' => 'validation_schema',
							'bloc' => 'global'
						]
                    ];
                }

                $this->RapportEchangeALI->saveAssociated($rapport);
                $this->RapportEchangeALI->clear();

			}


        }



    /**
	 * Ecrit la totalité du XML
	 * @param array
	 * @param string
	 * @return bool
	 */
	private function _ecritureXml($data, $file, $now, $ali) {
		// Création  du XML
		$xml = new XMLWriter();
		$success = $xml->openUri($file);
		if (!$success) {
            return $success;
		}
        $xml->setIndent(true);
        //début du document
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('WebRSA-ALI');
        $xml->startElement('entete');
		$xml->writeElement('date_generation', $now->format('Y-m-d H:i:s'));
		$xml->writeElement('id_structure_ali', $ali['Structurereferente']['id']);
		$xml->writeElement('libelle_ali',$ali['Structurereferente']['lib_struc']);
		$xml->writeElement('fichier_stock',($this->stock != false) ? $this->stock : '0');

        //fin de l'entête
		$xml->endElement();

		if(!empty($data)){
			$xml->startElement('dossiers');

			foreach($data as $personne_id => $donnee){
				//début du dossier
				$xml->startElement('dossier');

				//id de l'allocataire
				$xml->writeElement('id_personne', $personne_id);

				//allocataire
				if(isset($donnee['allocataire'])){
					$xml->startElement('allocataire');


					if(!empty($donnee['allocataire']['civilite'])){
						$xml->writeElement(
							'civilite',
							$this->CorrespondanceReferentiel->getIdReferentielFromCode(
								'civilite',
								$donnee['allocataire']['civilite']
							)
						);
					}

					$xml->writeElement('nom', $donnee['allocataire']['nom']);
					$xml->writeElement('prenom', $donnee['allocataire']['prenom']);

					if(!is_null($donnee['allocataire']['nir'])){
						$xml->writeElement('nir', $donnee['allocataire']['nir']);
					}

					$xml->writeElement('numero_caf', $donnee['allocataire']['numero_caf']);
					$xml->writeElement('date_naissance', $donnee['allocataire']['date_naissance']);
					$xml->writeElement('numero_voie', $donnee['allocataire']['numero_voie']);
					$xml->writeElement('type_voie', $donnee['allocataire']['type_voie']);
					$xml->writeElement('nom_voie', $donnee['allocataire']['nom_voie']);

					if(!is_null($donnee['allocataire']['complement_adresse'])){
						$xml->writeElement('complement_adresse', $donnee['allocataire']['complement_adresse']);
					}

					$xml->writeElement('code_postal', $donnee['allocataire']['code_postal']);
					$xml->writeElement('ville', $donnee['allocataire']['ville']);
					$xml->writeElement('date_demande_rsa', $donnee['allocataire']['date_demande_rsa']);
					$xml->writeElement('numero_demande_rsa', $donnee['allocataire']['numero_demande_rsa']);
					$xml->writeElement(
						'etat_droit',
						$this->CorrespondanceReferentiel->getIdReferentielFromCode(
							'etatdos',
							$donnee['allocataire']['etat_droit']
						)
					);

					if(!is_null($donnee['allocataire']['soumis_droits_devoirs'])){
						$xml->writeElement('soumis_droits_devoirs', $donnee['allocataire']['soumis_droits_devoirs']);
					}

					$xml->writeElement(
						'role_personne',
						$this->CorrespondanceReferentiel->getIdReferentielFromCode(
							'rolepers',
							$donnee['allocataire']['role_personne']
						)
					);

					//Si on n'a pas l'info on met 0
					$xml->writeElement('nb_enfants_foyer', $donnee['allocataire']['nb_enfants_foyer'] != '' ? $donnee['allocataire']['nb_enfants_foyer'] : 0);

					$xml->writeElement(
						'situation_familiale',
						$this->CorrespondanceReferentiel->getIdReferentielFromCode(
							'sitfam',
							$donnee['allocataire']['situation_familiale']
						)
					);

					$xml->endElement();
				}

				//referent de parcours
				if(isset($donnee['referent'])){

					$xml->startElement('referents_parcours');

					foreach($donnee['referent'] as $ref){
						$ref = $ref[0];

						$xml->startElement('referent_parcours');

						$xml->writeElement(
							'id_structure_referente',
							$this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
								'structuresreferentes',
								$ref['structurereferente_id']
							)
						);

						if(!is_null($ref['referent_id'])){
							$xml->writeElement(
								'id_referent',
								$this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
									'referents',
									$ref['referent_id']
								)
							);
						}

						$xml->writeElement('date_debut_designation', $ref['dddesignation']);

						if(!is_null($ref['dfdesignation'])){
							$xml->writeElement('date_fin_designation', $ref['dfdesignation']);
						}


						$xml->endElement();
					}

					$xml->endElement();
				}

				//modes de contact
				if(
					isset($donnee['contact'])
					&& (!empty($donnee['contact']['numport']) || !empty($donnee['contact']['numfixe']) || !empty($donnee['contact']['email']))
				){
					$xml->startElement('modes_contact');

					if(!empty($donnee['contact']['numfixe'])){
						$xml->writeElement('telephone_1', $donnee['contact']['numfixe']);
						$xml->writeElement('modif_telephone_1', $donnee['contact']['modified_numfixe']);
					}
					if(!empty($donnee['contact']['numport'])){
						$xml->writeElement('telephone_2', $donnee['contact']['numport']);
						$xml->writeElement('modif_telephone_2', $donnee['contact']['modified_numport']);
					}
					if(!empty($donnee['contact']['email'])){
						$xml->writeElement('email', $donnee['contact']['email']);
						$xml->writeElement('modif_email', $donnee['contact']['modified_email']);
					}


					$xml->endElement();
				}

				//orientation
				if(isset($donnee['orient'])){

					$xml->startElement('orientations');

					foreach($donnee['orient'] as $orient){
						$orient = $orient[0];

						$xml->startElement('orientation');

						$xml->writeElement('id_orient_webrsa', $orient['id']);

						if(!is_null($orient['id_base_ali'])){
							$xml->writeElement('id_orient_ali', $orient['id_base_ali']);
						}

						$xml->writeElement('date_orient', $orient['date_valid']);
						$xml->writeElement(
							'origine_orient',
							$this->CorrespondanceReferentiel->getIdReferentielFromCode(
								'orient_origine',
								$orient['origine']
							)
						);
						$xml->writeElement(
							'statut_orient',
							$this->CorrespondanceReferentiel->getIdReferentielFromCode(
								'orient_statut',
								$orient['statut_orient']
							)
						);
						$xml->writeElement(
							'type_orient',
							$this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
								'typesorients',
								$orient['typeorient_id']
							)
						);

						if(!is_null($orient['rgorient'])){
							$xml->writeElement('rang_orient', $orient['rgorient']);
						}

						$xml->writeElement(
							'structure_referente',
							$this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
								'structuresreferentes',
								$orient['structurereferente_id']
							)
						);

						$xml->writeElement('tag_entretien_diag', ($orient['entretiendiag'] != false) ? '1' : '0');



						$xml->endElement();
					}

					$xml->endElement();
				}

				//cer
				if(isset($donnee['cer'])){

					$xml->startElement('cers');

					foreach($donnee['cer'] as $cer){
						$cer = $cer[0];

						$xml->startElement('cer');

						$xml->writeElement('id_cer_webrsa', $cer['id']);
						if(!is_null($cer['id_base_ali'])){
							$xml->writeElement('id_cer_ali', $cer['id_base_ali']);
						}
						$xml->writeElement(
							'structure_referente',
							$this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
								'structuresreferentes',
								$cer['structurereferente_id']
							)
						);
						$xml->writeElement(
							'statut',
							$this->CorrespondanceReferentiel->getIdReferentielFromCode(
								'cer_statut',
								$cer['positioncer']
							)
						);

						if(!is_null($cer['rg_ci'])){
							$xml->writeElement('rang', $cer['rg_ci']);
						}
						if(!is_null($cer['dd_ci'])){
							$xml->writeElement('date_debut', $cer['dd_ci']);
						}
						if(!is_null($cer['df_ci'])){
							$xml->writeElement('date_fin', $cer['df_ci']);
						}

						$xml->endElement();
					}

					$xml->endElement();

				}

				//dsp
				if(isset($donnee['dsp']) && $donnee['dsp'][0][0]['id'] != ''){

					$dsp = $donnee['dsp'][0][0];

					$xml->startElement('dsp');

						$xml->writeElement('id_dsp_webrsa', $dsp['id']);
						if(!is_null($dsp['id_base_ali'])){
							$xml->writeElement('id_dsp_ali', $dsp['id_base_ali']);
						}

						$xml->startElement('niveau_etude');

							if(!is_null($dsp['niveau_etude'])){
								$xml->writeElement(
									'niveau_etude',
									$this->CorrespondanceReferentiel->getIdReferentielFromCode(
										'dsp_nivetu',
										$dsp['niveau_etude']
									)
								);
							}

							if(!is_null($dsp['diplome_plus_eleve'])){
								$xml->writeElement(
									'diplome_plus_eleve',
									$this->CorrespondanceReferentiel->getIdReferentielFromCode(
										'diplome_max',
										$dsp['diplome_plus_eleve']
									)
								);
							}

							if(!is_null($dsp['annee_obtention'])){
								$xml->writeElement('annee_obtention', $dsp['annee_obtention']);
							}

							if(!is_null($dsp['qualifications_pro'])){
								$xml->writeElement('qualifications_pro', $dsp['qualifications_pro']);
							}

							if(!is_null($dsp['precisions_qualifications_pro'])){
								$xml->writeElement('precisions_qualifications_pro', $dsp['precisions_qualifications_pro']);
							}

							if(!is_null($dsp['competences_extrapro'])){
								$xml->writeElement('competences_extrapro', $dsp['competences_extrapro']);
							}

							if(!is_null($dsp['precisions_competences_extrapro'])){
								$xml->writeElement('precisions_competences_extrapro', $dsp['precisions_competences_extrapro']);
							}

						//niveau etude
						$xml->endElement();
					$xml->endElement();
				}

				//fin du dossier
				$xml->endElement();
			}
			$xml->endElement();
		}


        //fin du document
		$xml->endDocument();
		$octets = $xml->flush();
		if ($octets == 0) {
			$success = true;
		} else {
			$success = false;
		}


		return $success;
	}

	public function donneesReferent($personne, $donnees, $structuresOK){

		$diff="";
		if(!$this->stock){
			$diff = " and pr.modified > '$this->date_dernier'";
		}
		$structures = implode(',',$structuresOK);
		$ref = $this->PersonneReferent->query(
			"
			select
			pr.structurereferente_id,
			pr.dddesignation,
			pr.dfdesignation,
			case when pr.structurereferente_id in ({$structures})
			then pr.referent_id
			else null
			end as referent_id
			from personnes_referents pr
			where pr.personne_id = {$personne['p_id']}
			".$diff.";"
		);


		if(!empty($ref)){
			$donnees[$personne['p_id']]['referent'] = $ref;
		}

		return $donnees;
	}

	public function donneesContact($personne, $donnees){
		$contact = [];

		if($this->stock || ($personne['modified_numfixe'] != null && $personne['modified_numfixe']  > $this->date_dernier)){
			$contact['numfixe'] = $personne['numfixe'];
			$contact['modified_numfixe'] = ($personne['modified_numfixe'] != null) ? substr($personne['modified_numfixe'], 0, 19) : null;
		}
		if($this->stock || ($personne['modified_numport'] != null && $personne['modified_numport']  > $this->date_dernier)){
			$contact['numport'] = $personne['numport'];
			$contact['modified_numport'] = ($personne['modified_numport'] != null) ? substr($personne['modified_numport'], 0, 19) : null;
		}
		if($this->stock || ($personne['modified_email'] != null && $personne['modified_email']  > $this->date_dernier)){
			$contact['email'] = $personne['email'];
			$contact['modified_email'] = ($personne['modified_email'] != null) ? substr($personne['modified_email'], 0, 19) : null;
		}


		if(!empty($contact)){
			$donnees[$personne['p_id']]['contact'] = $contact;
		}

		return $donnees;


	}
	public function donneesOrientation($personne, $donnees){

		$diff="";
		if(!$this->stock){
			$diff = " and o.modified > '$this->date_dernier'";
		}

		$orient = $this->Personne->query(
			"
			select
			id,
			id_base_ali,
			date_valid,
			origine,
			statut_orient,
			typeorient_id,
			rgorient,
			structurereferente_id,
			(	select count(*)
				from entites_tags et
				join tags t on t.id = et.tag_id
				join valeurstags v on v.id = t.valeurtag_id
				where v.name = 'Entretien de diagnostic'
				and et.modele = 'Personne'
				and et.fk_value = {$personne['p_id']}
			) != 0 as entretiendiag
			from orientsstructs o
			where personne_id = {$personne['p_id']}
			and date_valid is not null
			".$diff.";"
		);

		if(!empty($orient)){
			$donnees[$personne['p_id']]['orient'] = $orient;
		}

		return $donnees;
	}

	public function donneesCer($personne, $donnees){

		$diff="";
		if(!$this->stock){
			$diff = " and (c.modified > '$this->date_dernier' or c2.modified > '$this->date_dernier')";
		}

		$cer = $this->Personne->query(
			"
			select
			c2.id,
			c.id_base_ali,
			c2.structurereferente_id,
			c.positioncer,
			c2.rg_ci,
			c2.dd_ci,
			c2.df_ci
			from cers93 c join contratsinsertion c2 on c.contratinsertion_id = c2.id
			where c.positioncer = '99valide'
			and c2.personne_id = {$personne['p_id']}
			".$diff.";"
		);

		if(!empty($cer)){
			$donnees[$personne['p_id']]['cer'] = $cer;
		}

		return $donnees;
	}

	public function donneesDsp($personne, $donnees){

		$diff="";
		if(!$this->stock){
			$diff = " and (d.created > '$this->date_dernier' or dr.modified > '$this->date_dernier')";
		}

		$dsp = $this->Personne->query(
			"
			--Dernière version de révision des DSP
			with DernierDspRev as (SELECT id, personne_id, rank() over(partition by personne_id order by modified desc, id desc) as rang FROM dsps_revs
			where personne_id = {$personne['p_id']}
			order by personne_id)
			-- DSP
			select
				p.id as personne_id,
				(case when dr.id is not null then dr.id else d.id end) as id,
				d.id_base_ali as id_base_ali,
				(case when dr.id is not null then dr.nivetu else d.nivetu end) as niveau_etude,
				(case when dr.id is not null then dr.nivdipmaxobt else d.nivdipmaxobt end) as diplome_plus_eleve,
				(case when dr.id is not null then dr.annobtnivdipmax else d.annobtnivdipmax end) as annee_obtention,
				(case when dr.id is not null then dr.topqualipro else d.topqualipro end) as qualifications_pro,
				(case when dr.id is not null then dr.libautrqualipro else d.libautrqualipro end) as precisions_qualifications_pro,
				(case when dr.id is not null then dr.topcompeextrapro else d.topcompeextrapro end) as competences_extrapro,
				(case when dr.id is not null then dr.libcompeextrapro else d.libcompeextrapro end) as precisions_competences_extrapro
			FROM personnes p
				left join DernierDspRev ddr on ddr.personne_id = p.id and ddr.rang = 1
				LEFT JOIN dsps d on p.id = d.personne_id
				LEFT JOIN dsps_revs dr on ddr.id = dr.id
			where p.id = {$personne['p_id']}
			".$diff.";"
		);

		if(!empty($dsp)){
			$donnees[$personne['p_id']]['dsp'] = $dsp;
		}

		return $donnees;
	}

}