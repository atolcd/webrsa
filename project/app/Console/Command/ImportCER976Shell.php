<?php
	/**
	 * Code source de la classe ImportCER976Shell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 *
	 * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake ImportCER976 -v -s ',' -app app --typeClasseur 1 Classeur1.csv
	 *
	 */
	App::uses( 'CsvAbstractImporterShell', 'Csv.Console/Command/Abstract' );

	/**
	 * La classe ImportCER976Shell permet d'importer les CER de Mayotte
	 *
	 * @package app.Console.Command
	 */
	class ImportCER976Shell extends CsvAbstractImporterShell
	{
		/**
		 * Les modèles utilisés par ce shell.
		 *
		 * @var array
		 */
		public $uses = array(
			'Personne', 'Contratinsertion', 'Referent', 'ContratinsertionSujetcer', 'Sujetcer', 'Soussujetcer', 'Valeurparsoussujetcer',  'Orientstruct'
		);

		/**
		 * Les tâches utilisées par ce shell.
		 *
		 * @var array
		 */
		public $tasks = array( 'XProgressBar' );

		/**
		 * Les en-têtes par défaut tels qu'ils sont attendus.
		 *
		 * @var array
		 */
		protected $_defaultHeaders = array();

		/**
		 * Tableau de correspondances entre les en-têtes et des chemins de
		 * modèles CakePHP.
		 *
		 * @var array
		 */
		protected $_correspondances = array();

		/**
		 * Les chemins de données et données complémentaires pour chacun des
		 * modèles nécessaires à la méthode processModel().
		 *
		 * @var array
		 */
		public $processModelDetails = array();

        /**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description("Ce script permet d'importer les CER. \n
            - Sur tous les classeurs, supprimer les retours à la ligne \n
            - Sur la colonne Thèmes des classeurs, remplacer \n les caractères <info> . : , - </info> \n par le caractère <info>/</info> \n
            - Sur le classeur 1, pour le NUM 372, inverser les valeurs \n des colonnes Conclusion et Référent Unique \n
            - Sur le classeur 1, pour le NUM 688, \n changer la valeur 217 en 2017 \n dans la colonne Date signature RU et BRSA \n
            ");
			$options = array(
				'typeClasseur' => array(
					'short' => 'T',
					'default' => 1,
					'help' => '1 ou 2 selon le classeur à importer'
				),
			);
			$parser->addOptions( $options );
			return $parser;
		}

        /**
		 * Nettoyage des valeurs des champs (suppression des espaces excédentaires)
		 * et transformation des clés via les correspondances.
		 *
		 * @param array $row
		 * @return array
		 */
		public function normalizeRow( array $row ) {
			$new = array();

			foreach( $row as $key => $value ) {
				if( isset( $this->_correspondances[$key] ) ) {
					$new = Hash::insert(
						$new,
						$this->_correspondances[$key],
						trim( preg_replace( '/[ ]+/', ' ', $value ) )
					);
				}
			}

			return $new;
		}


		/**
		 * Traitement d'une ligne de données du fichier CSV.
		 *
		 * @param array $row
		 * @return boolean
		 */
		public function processRow( array $row ) {
			$success = true;

			if( empty( $row ) ) {
				$this->empty[] = $row;
			}
			else {
			    $data = $this->normalizeRow( $row );

                //on transforme la date de naissance au bon format
                if(date_create_from_format('n/j/Y', $data['D N']) != false && (strlen($data['D N']) - strrpos($data['D N'], '/')) > 4){
                    $date = date_format(date_create_from_format('n/j/Y', $data['D N']), 'Y-m-d');
                } else if (date_create_from_format('n/j/y', $data['D N']) != false){
                    $date = date_format(date_create_from_format('n/j/y', $data['D N']), 'Y-m-d');
                    if(substr($date, 0, 4) > 2022){
                        $date = '19'.substr($date, 2);
                    }
                } else {
                    $date = null;
                }

                //On récupère l'id personne de l'allocataire
                $personne_id = $this->getIdPersonne(str_replace("'", "''", $data['Nom-Prénom']), $date, $data['N°alloc']);

                if($personne_id != null){
                    //On modifie l'orientation pour correspondre au format attendu
                    switch(strtolower($data['Orientation'])){
                        case 'sociale':
                            $Orientation_format = 'Social';
                            break;
                        case 'socio-professionnelle':
                            $Orientation_format = 'Socioprofessionnelle';
                            break;
                        default :
                            $Orientation_format = $data['Orientation'];
                    }

                    //recherche de la structure référente
                    $querysite = "
                        select s.id as site, s.typeorient_id as typeorient
                        from structuresreferentes s
                        where s.lib_struc ilike '%$Orientation_format%'
                        and (unaccent(s.lib_struc) ilike unaccent('%{$data['Site']}%') or unaccent(s.ville) ilike unaccent('%{$data['Site']}%'))
                    ";
				    $site = $this->Personne->query($querysite);

                    if(isset($site[0][0]['site'])){
                        //on récupère le nom correct du référent
                        $referent_unique = $this->getNomReferent($data['Référent Unique']);
                        //on recherche le référent
                        if($referent_unique != null){
                            $queryref = "
                                select r.id as ref
                                from referents r join structuresreferentes s on s.id = r.structurereferente_id
                                where s.id = {$site[0][0]['site']}
                                and r.nom ilike '{$referent_unique['nom']}' and r.prenom ilike '{$referent_unique['prenom']}'
                            ";
                            $ref = $this->Personne->query($queryref);
                        }
                        if(!isset($ref[0][0]['ref'])){
                            //s'il n'est pas retrouvé, on crée le référent
                            $infos_connues = isset($this->Referent->findByNomAndPrenom($referent_unique['nom'], $referent_unique['prenom'])['Referent']) ? $this->Referent->findByNomAndPrenom($referent_unique['nom'], $referent_unique['prenom'])['Referent'] : null;
                            $nouveau_ref = [
                                'structurereferente_id' => $site[0][0]['site'],
                                'nom' => $referent_unique['nom'],
                                'prenom' => $referent_unique['prenom']
                            ];
                            if(!empty($infos_connues)){
                                $nouveau_ref['numero_poste'] = $infos_connues['numero_poste'];
                                $nouveau_ref['email'] = $infos_connues['email'];
                                $nouveau_ref['qual'] = $infos_connues['qual'];
                                $nouveau_ref['fonction'] = $infos_connues['fonction'];
                            }
                            $this->Referent->save($nouveau_ref);
                            $id_referent = $this->Referent->id;

                        } else {
                            $id_referent = $ref[0][0]['ref'];
                        }
                        if(!empty($data['Date signature RU et BRSA']) && (date_create_from_format('n/j/Y', $data['Date signature RU et BRSA']) != false || date_create_from_format('n/j/y', $data['Date signature RU et BRSA']) != false)){
                            $duree_engag = intval(substr($data['Échéance'], 0,2)) != 0 ? intval(substr($data['Échéance'], 0,2)) : 12;
                            if(date_create_from_format('n/j/Y', $data['Date signature RU et BRSA']) != false && (strlen($data['Date signature RU et BRSA']) - strrpos($data['Date signature RU et BRSA'], '/')) > 4){
                                $dd_ci = date_format(date_create_from_format('n/j/Y', $data['Date signature RU et BRSA']), 'Y-m-d');
                            } else {
                                $dd_ci = date_format(date_create_from_format('n/j/y', $data['Date signature RU et BRSA']), 'Y-m-d');
                            }
                            if(empty($data['Date signature Président']) || (date_create_from_format('n/j/Y', $data['Date signature Président']) == false && date_create_from_format('n/j/y', $data['Date signature Président']) == false)){
                                $date_signature_pres = $dd_ci;
                            }
                            elseif(date_create_from_format('n/j/Y', $data['Date signature Président']) != false && (strlen($data['Date signature Président']) - strrpos($data['Date signature Président'], '/')) > 4){
                                $date_signature_pres = date_format(date_create_from_format('n/j/Y', $data['Date signature Président']), 'Y-m-d');
                            } else {
                                $date_signature_pres = date_format(date_create_from_format('n/j/y', $data['Date signature Président']), 'Y-m-d');
                            }
                            $df_ci = date_format(date_add(date_create($dd_ci), date_interval_create_from_date_string($duree_engag.'months')), 'Y-m-d');
                            $rang_cer = strpos($data['Type CER'], 'Premier') !== false ? 1 : (int) filter_var($data['Type CER'], FILTER_SANITIZE_NUMBER_INT)+1;

                            //On vérifie qu'il n'existe pas déjà un cer de même rang
                            $sql = "
                                select c.id
                                from contratsinsertion c join personnes p on p.id = c.personne_id
                                where c.rg_ci = {$rang_cer} and p.id = {$personne_id}
                            ";
                            $cer = $this->Personne->query($sql);

                            while(!empty($cer)){
                                $rang_cer++;
                                //Si il existe déjà un cer de même rang on ajoute un au rang et on revérifie
                                $sql = "
                                select c.id
                                from contratsinsertion c join personnes p on p.id = c.personne_id
                                where c.rg_ci = {$rang_cer} and p.id = {$personne_id}
                                ";
                                $cer = $this->Personne->query($sql);

                            }
                            $date_du_jour = date_create();

                            //on ajoute les sujets de cer associés
                            $themes = [];
                            if(!empty($data['Thèmes'])){
                                $themes = $this->getThemes($data['Thèmes'], 3);
                            }
                            if($themes == -1){
                                $this->rejectRow($data, null, "Les thèmes sont concurrents et ne peuvent donc pas être intégrés");
                            } else if($themes === null){
                                $this->Contratinsertion->rollback();
                                $this->rejectRow($data, null, "Le(s) thème(s) n'existent pas dans WebRSA");
                            } else {
                                $donnees = [
                                    'personne_id' => $personne_id,
                                    'referent_id' => $id_referent,
                                    'structurereferente_id' => $site[0][0]['site'],
                                    'rg_ci' => $rang_cer,
                                    'dd_ci' => $dd_ci,
                                    'df_ci' => $df_ci,
                                    'duree_engag' => $duree_engag,
                                    'observ_ci' => $data['Conclusion'],
                                    'observ_benef' => $data['Bilan'],
                                    'nature_projet' => $data['Objectif accompagnement'],
                                    'descriptionaction' => $data["Description de l'action"],
                                    'lieu_saisi_ci' => 'Conseil Départemental de Mayotte',
                                    'date_saisi_ci' => $date_du_jour->format('Y-m-d'),
                                    'decision_ci' => 'V',
                                    'datevalidation_ci' => $date_signature_pres
                                ];
                                $donnees['Sujetcer']['Sujetcer'] = $themes;
                                $success = $this->Contratinsertion->save( $donnees , array('validate' => false, 'atomic' => false ) );
                                $this->Contratinsertion->clear();
                            }

                            //on enregistre une orientation
                            $donnees_orient = [
                                'personne_id' => $personne_id,
                                'referent_id' => $id_referent,
                                'structurereferente_id' => $site[0][0]['site'],
                                'typeorient_id' => $site[0][0]['typeorient'],
                                'statut_orient' => 'Orienté',
                                'date_valid' => $dd_ci,
                                'date_propo' => $dd_ci,
                                'origine' => 'manuelle',
                                'valid_cg' => true,
                            ];
                            $success = $success && $this->Orientstruct->save( $donnees_orient , array('validate' => false, 'atomic' => false ) );

                            $donnees_orient['Orientstruct'] = $donnees_orient;
                            $donnees_orient['Orientstruct']['referent_id'] = $donnees_orient['structurereferente_id'].'_'.$donnees_orient['referent_id'];
                            //On recalcule le rang des orientations
                            $this->Orientstruct->forceRecalculeRang($donnees_orient);
                            $this->Orientstruct->clear();
                            //on ajoute le référent en référent de parcours (et on clôt le précédent si il existe)
                            //On utilise la fonction en place dans WebRSA (si l'orientation est plus ancienne, le référent n'est pas modifié)
                             $success = $success && $this->Orientstruct->Referent->PersonneReferent->referentParModele(
                                $donnees_orient,
                                $this->Orientstruct->alias,
                                'date_valid'
                            );
                        } else {
                            //Il manque la date de signature
                            $this->rejectRow($data, null, "La date de signature par le BRSA est obligatoire et doit respecter le format de date");
                        }
                    } else {
                        //on ne trouve pas la structure
                        $this->rejectRow($data, null, "La structure est introuvable");
                    }
                } else {
                    //on ne retrouve pas la personne
                    $this->rejectRow($data, null, "La personne est introuvable");
                }
			}

			return $success;
		}

		/**
		 * Surcharge de la méthode startup pour vérifier que le département soit
		 * uniquement le 976 et démarrage de la barre de progression.
		 */
		public function startup() {

            if( $this->params['typeClasseur'] == 1 ) {
                $this->_correspondances = [
                    "NUM",
                    "Civilité",
                    "Nom-Prénom",
                    "N°alloc",
                    "D N",
                    "Age",
                    "Adresse",
                    "Situation familiale",
                    "Orientation",
                    "Type CER",
                    "Niveau de fomation",
                    "Difficulté pour la lecture/ Ecriture",
                    "Expérience (emploi, formation)",
                    "Compétences et connaissances",
                    "Métier recherché",
                    "Thèmes",
                    "Description de l'action",
                    "Échéance",
                    "Bilan",
                    "Objectif accompagnement",
                    "Conclusion",
                    "Référent Unique",
                    "Site",
                    "Durée des Actions",
                    "Date signature RU et BRSA",
                    "Date signature Président",
                    "Date échéance Contrat"
                ];

                $this->_defaultHeaders = [
                    "NUM",
                    "Civilité",
                    "Nom-Prénom",
                    "N°alloc",
                    "D N",
                    "Age",
                    "Adresse",
                    "Situation familiale",
                    "Orientation",
                    "Type CER",
                    "Niveau de fomation",
                    "Difficulté pour la lecture/ Ecriture",
                    "Expérience (emploi, formation)",
                    "Compétences et connaissances",
                    "Métier recherché",
                    "Thèmes",
                    "Description de l'action",
                    "Échéance",
                    "Bilan",
                    "Objectif accompagnement",
                    "Conclusion",
                    "Référent Unique",
                    "Site",
                    "Durée des Actions",
                    "Date signature RU et BRSA",
                    "Date signature Président",
                    "Date échéance Contrat"
                ];

            } else if ($this->params['typeClasseur'] == 2) {
                $this->_correspondances = [
                    "NUM",
                    "Civilité",
                    "Nom-Prénom",
                    "N°alloc",
                    "D N",
                    "Adresse",
                    "Situation familiale",
                    "Orientation",
                    "Type CER",
                    "Niveau de fomation",
                    "Difficulté pour la lecture/ Ecriture",
                    "Expérience (emploi, formation)",
                    "Compétences et connaissances",
                    "Métier recherché",
                    "Thèmes",
                    "Description de l'action",
                    "Échéance",
                    "Bilan",
                    "Objectif accompagnement",
                    "Conclusion",
                    "Référent Unique",
                    "Site",
                    "Durée des Actions",
                    "Date signature RU et BRSA",
                    "Date signature Président",
                    "Date échéance Contrat"
                ];

                $this->_defaultHeaders = [
                    "Num",
                    "Civilité",
                    "Nom/Prenom",
                    "N°Alloc",
                    "DN",
                    "Adresse",
                    "Situation familiale",
                    "Orientation",
                    "Type CER",
                    "Niveau de formation",
                    "Difficultés pour la  lecture/écriture",
                    "Experience (emploi, formation)",
                    "Compétences et connaissances",
                    "Métier recherché",
                    "Thèmes",
                    "Description de l'action",
                    "Échéance",
                    "Bilan",
                    "Objectif accompagnement",
                    "Conclusion de l'entretien",
                    "Référent unique",
                    "Site",
                    "Durée des actions",
                    "Date signature RU et BRSA",
                    "Date signature Président",
                    "Date échéance contrat"
                ];

            }

			$this->checkDepartement( 976 );
            parent::startup();
            $this->out( '<info>Nombre d\'enregistrements à traiter</info> : '.$this->_Csv->count());
		}

        public function getIdPersonne($nom, $datenaiss, $numalloc){
            //numéro allocataire + date de naissance
            $query = "
            select p.id
            from personnes p
            join foyers f on f.id = p.foyer_id
            join dossiers d on d.id = f.dossier_id
            where
            regexp_replace(d.matricule,'(^0)+(\d{6,})+(0{8,}$)', '\\2') = '{$numalloc}'
            and p.dtnai = '{$datenaiss}'
            order by p.id desc
            limit 1
            ";

            if($datenaiss != null){
                $personne = $this->Personne->query($query);
            }

            if (!isset($personne[0][0]['id'])){
                //Numéro allocataire + nom, prénom
                $query = "
                select p.id
                from personnes p
                join foyers f on f.id = p.foyer_id
                join dossiers d on d.id = f.dossier_id
                where
                regexp_replace(d.matricule,'(^0)+(\d{6,})+(0{8,}$)', '\\2') = '{$numalloc}'
                and '{$nom}' ilike any (array[concat(p.nom, ' ', p.prenom), concat(p.prenom, ' ', p.nom)])
                order by p.id desc
                limit 1
                ";

                $personne = $this->Personne->query($query);
            }

            if (!isset($personne[0][0]['id'])){
                //Date de naissance + nom, prénom
                $query = "
                select p.id
                from personnes p
                join foyers f on f.id = p.foyer_id
                join dossiers d on d.id = f.dossier_id
                where
                p.dtnai = '{$datenaiss}'
                and '{$nom}' ilike any (array[concat(p.nom, ' ', p.prenom), concat(p.prenom, ' ', p.nom)])
                order by p.id desc
                limit 1
                ";

                if($datenaiss != null){
                    $personne = $this->Personne->query($query);
                }
            }

            return isset($personne[0][0]['id']) ? $personne[0][0]['id'] : null;
        }

        public function getNomReferent($nomref){
            $correspondances = file('referents.csv');
            $correspondances = array_map(
                function($v){
                    $tab =  explode(',', $v);
                    return ['referent' => $tab[0], 'nom' => $tab[1], 'prenom' => trim($tab[2])];
                }
                , $correspondances
            );
            $id = array_search(
                $nomref,
				array_column(
					$correspondances,
					'referent'
                )
            );

            return $correspondances[$id];
        }

        public function getThemes($themes){
            $sujets = [];
            $correspondances = file('themes.csv');
            $correspondances = array_map(
                function($v){
                    $tab =  explode(',', $v);
                    return ['nom' => $tab[0], 'sujet' => $tab[1], 'soussujet' => $tab[2], 'valeurparsoussujet' => $tab[3]];
                }
                , $correspondances
            );
            $tabthemes = explode('/', $themes);
            $tabthemes = array_map(
                function($v){
                    return trim($v);
                }
                , $tabthemes
            );

            foreach ($tabthemes as $theme){
                if(!empty($theme)){
                    $idtheme = array_search(
                        $theme,
                        array_column(
                            $correspondances,
                            'nom'
                        )
                    );
                    if(!empty($idtheme)){
                        $themeformat = $correspondances[$idtheme];
                        if(!empty($themeformat['sujet'])){
                            $sujet = $this->Sujetcer->findByLibelle(trim($themeformat['sujet']));
                            if(!empty($sujet)){
                                 //on vérifie qu'il n'existe pas déjà la même ligne
                                 if(array_search(
                                    $sujet['Sujetcer']['id'],
                                    array_column(
                                        $sujets,
                                        'sujetcer_id'
                                    )
                                ) === false){
                                    $sujets[] = [
                                        'sujetcer_id' => $sujet['Sujetcer']['id'],
                                    ];
                                }
                            } else {
                                return null;
                            }
                        } elseif (!empty($themeformat['soussujet'])){
                            $soussujet = $this->Soussujetcer->findByLibelle(trim($themeformat['soussujet']));
                            if(!empty($soussujet)){
                                //On vérifie si le sujet existe déjà
                                $key = array_search(
                                    $soussujet['Soussujetcer']['sujetcer_id'],
                                    array_column(
                                        $sujets,
                                        'sujetcer_id'
                                    )
                                );
                                if($key !== false){
                                    //Si il n'y a pas de sous sujet, on l'ajoute
                                    if(!isset($sujets[$key]['soussujetcer_id'])){
                                        $sujets[$key]['soussujetcer_id'] = $soussujet['Soussujetcer']['id'];
                                    //Si il y a un sous sujet mais qu'il est différent, on sort
                                    } else if($sujets[$key]['soussujetcer_id'] != $soussujet['Soussujetcer']['id']){
                                        return -1;
                                    }
                                } else {
                                    $sujets[] = [
                                        'soussujetcer_id' => $soussujet['Soussujetcer']['id'],
                                        'sujetcer_id' => $soussujet['Soussujetcer']['sujetcer_id'],
                                    ];
                                }
                            } else {
                                return null;
                            }
                        } elseif (!empty($themeformat['valeurparsoussujet'])){
                            $valeur = $this->Valeurparsoussujetcer->findByLibelle(trim($themeformat['valeurparsoussujet']));
                            if(!empty($valeur)){
                                $soussujet = $this->Soussujetcer->findById($valeur['Valeurparsoussujetcer']['soussujetcer_id']);
                                //On vérifie si le sujet existe déjà
                                $key = array_search(
                                    $soussujet['Soussujetcer']['sujetcer_id'],
                                    array_column(
                                        $sujets,
                                        'sujetcer_id'
                                    )
                                );
                                if($key !== false){
                                    //Si il n'y a pas de sous sujet, on l'ajoute
                                    if(!isset($sujets[$key]['soussujetcer_id'])){
                                        $sujets[$key]['soussujetcer_id'] = $soussujet['Soussujetcer']['id'];
                                        $sujets[$key]['valeurparsoussujetcer_id'] = $valeur['Valeurparsoussujetcer']['id'];
                                    //Si il y a un sous sujet mais qu'il est différent, on sort
                                    } else if($sujets[$key]['soussujetcer_id'] != $soussujet['Soussujetcer']['id']){
                                        return -1;
                                    //Si il y a le même sous sujet on regarde s'il y a une valeur par sous sujet
                                    } else if ($sujets[$key]['soussujetcer_id'] == $soussujet['Soussujetcer']['id']){
                                        //Si il n'y a pas de valeur par sous sujet, on l'ajoute
                                        if(!isset($sujets[$key]['valeurparsoussujetcer_id'])){
                                            $sujets[$key]['valeurparsoussujetcer_id'] = $valeur['Valeurparsoussujetcer']['id'];
                                        }
                                        //Si la valeur par sous sujet est différente on sort
                                        if($sujets[$key]['valeurparsoussujetcer_id'] != $valeur['Valeurparsoussujetcer']['id']){
                                            return -1;
                                        }
                                    }
                                } else {
                                    $sujets[] = [
                                        'valeurparsoussujetcer_id' => $valeur['Valeurparsoussujetcer']['id'],
                                        'soussujetcer_id' => $valeur['Valeurparsoussujetcer']['soussujetcer_id'],
                                        'sujetcer_id' => $soussujet['Soussujetcer']['sujetcer_id'],
                                    ];
                                }
                            } else {
                                return null;
                            }
                        }
                    } else {
                        return null;
                    }
                }
            }

            return $sujets;
        }
	}


?>