<?php
	/**
	 * Fichier source de la classe WebrsaALIReferentielXMLShell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     *
     * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake WebrsaALIReferentielXML -app app [Filepath] [diff]
	 *
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe WebrsaALIReferentielXMLShell ...
	 *
	 * @package app.Console.Command
	 */
	class WebrsaALIReferentielXMLShell extends XShell
	{
		public $uses = [
            'CorrespondanceReferentiel',
            'SujetReferentiel',
            'Structurereferente',
            'StructurereferenteZonegeographique',
            'RapportEchangeALI'
        ];

        public $stock = true;
        public $date_dernier = null;


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


            //On récupère tous les référentiels à intégrer dans le fichier
            if(!$this->stock){
                $query_dernier =
                "
                select created from administration.rapportsechangesali r left join administration.erreursechangesali e on e.rapport_id = r.id
                where r.type = 'referentiel'
                and e.id is null
                order by r.created desc, r.id desc
                limit 1
                ";

			    $this->date_dernier = isset($this->RapportEchangeALI->query($query_dernier)[0][0]['created']) ? $this->RapportEchangeALI->query($query_dernier)[0][0]['created'] : '1970-01-01';

                $this->SujetReferentiel->hasMany['CorrespondanceReferentiel']['conditions'] = array('CorrespondanceReferentiel.modified >=' => $this->date_dernier);
                $referentiels = $this->SujetReferentiel->find(
                    'all',
                    [
                        'recursive' => 1,
                        'contain' => array('CorrespondanceReferentiel')
                    ]
                );
            } else {
                $referentiels = $this->SujetReferentiel->find('all', ['recursive' => 1]);
            }

            //On récupère les ALI pour lesquelles il faut générer un fichier
            $alis = $this->Structurereferente->getALIexport();

            //on boucle sur chaque ALI pour créer un fichier par ALI
            foreach ($alis as $ali){
                $now = new DateTime();
                $referentiels_ali = $referentiels;
                if(!$this->stock){
                    $file = $path . 'referentiels_webrsa_ali_'.$ali['Structurereferente']['id'].'_diff_'. $now->format('Y-m-d_H-i-s') . '.xml';
                } else {
                    $file = $path . 'referentiels_webrsa_ali_'.$ali['Structurereferente']['id'].'_stock_'. $now->format('Y-m-d_H-i-s') . '.xml';
                }

                //on récupère la liste des zones geographiques associées à la structure
			    $zonesgeo_ali = $this->CorrespondanceReferentiel->getZonesGeoALI($ali['Structurereferente']['id']);

                //On récupère la liste des structures associées à la zone géographiques + celles associées à aucune zone
                $structuresOK_array = $this->StructurereferenteZonegeographique->getStructuresOkParZonesGeos($zonesgeo_ali);


                $id_referentiel_referents = array_search('referents', array_column(array_column($referentiels_ali, 'SujetReferentiel'), 'code'));

                //On filtre les référents en fonction de la zone géographique de leur structure associée
                $referentiels_ali[$id_referentiel_referents]['CorrespondanceReferentiel'] = array_filter(
                    $referentiels_ali[$id_referentiel_referents]['CorrespondanceReferentiel'],
                    function($s) use ($structuresOK_array){
                        return (array_search($s['referents_structurereferente_id'], $structuresOK_array) !== false);
                    }
                );


                //écriture du fichier XML
                $this->_ecritureXml($referentiels_ali, $file, $now, $ali);

                // //Validation du format
                $dom = new DOMDocument;
                $dom->load($file);
                $schema_valide = $dom->schemaValidate(Configure::read('EchangeALI.CheminValidation').'/Referentiel-WebRSA.xsd');

                // Ecriture dans la table de rapports
                $rapport['RapportEchangeALI'] = [
                    'nom_fichier' => $file,
                    'debut' => $now->format('Y-m-d_H:i:s'),
                    'type' => 'referentiel',
                    'ali_id' => $ali['Structurereferente']['id'],
                    'stock' => $this->stock
                ];

                //Si le schéma n'est pas validé on enregistre une erreur
                if(!$schema_valide){
                    $rapport['ErreurEchangeALI'] = [
                        ['code' => 'validation_schema']
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
	private function _ecritureXml($referentiels, $file, $now, $ali) {
		// Création  du XML
		$xml = new XMLWriter();
		$success = $xml->openUri($file);
		if (!$success) {
            return $success;
		}
        $xml->setIndent(true);
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('Referentiel_WebRSA');
        $xml->startElement('entete');
		$xml->writeElement('date_generation', $now->format('Y-m-d H:i:s'));
		$xml->writeElement('id_ali', $ali['Structurereferente']['id']);
		$xml->writeElement('libelle_ali',$ali['Structurereferente']['lib_struc']);
		$xml->writeElement('fichier_stock',($this->stock != false) ? $this->stock : '0');
		$xml->endElement();
		foreach ($referentiels as $sujet) {
            //Un élément par sujet
            //Si le sujet ne contient pas de valeurs, on le saute
            if(!empty($sujet['CorrespondanceReferentiel'])){
                $xml->startElement('liste_'.$sujet['SujetReferentiel']['code']);
                $xml->writeAttribute('referentiel', $sujet['SujetReferentiel']['libelle']);
                foreach($sujet['CorrespondanceReferentiel'] as $valeur){
                    //un élément par ligne dans le sujet
                    $xml->startElement($sujet['SujetReferentiel']['code']);
                    $xml->writeElement('identifiant', $valeur['id']);
                    if(!is_null($valeur['code'])){
                        $xml->writeElement('code', $valeur['code']);
                    }
                    if(!is_null($valeur['libelle'])){
                        $xml->writeElement('libelle', $valeur['libelle']);
                    }

                    //on ajoute les éléments particuliers
                    //types orientation
                    if(!is_null($valeur['typesorients_parent_id'])){
                        $xml->writeElement(
                            'parent_id',
                            $this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
                                'typesorients',
                                $valeur['typesorients_parent_id']
                            )
                        );
                    }

                    //zones geographiques
                    if(!is_null($valeur['zonesgeographiques_code_insee'])){
                        $xml->writeElement('code_insee', $valeur['zonesgeographiques_code_insee']);
                    }

                    //structures référentes
                    if(!is_null($valeur['structuresreferentes_typeorient_id'])){
                        $xml->writeElement(
                            'typeorient_id',
                            $this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
                                'typesorients',
                                $valeur['structuresreferentes_typeorient_id']
                            )
                        );
                    }
                    if(!is_null($valeur['structuresreferentes_numvoie'])){
                        $xml->writeElement('numvoie', $valeur['structuresreferentes_numvoie']);
                    }
                    if(!is_null($valeur['structuresreferentes_typevoie'])){
                        $xml->writeElement('typevoie', $valeur['structuresreferentes_typevoie']);
                    }
                    if(!is_null($valeur['structuresreferentes_nomvoie'])){
                        $xml->writeElement('nomvoie', $valeur['structuresreferentes_nomvoie']);
                    }
                    if(!is_null($valeur['structuresreferentes_codepostal'])){
                        $xml->writeElement('codepostal', $valeur['structuresreferentes_codepostal']);
                    }
                    if(!is_null($valeur['structuresreferentes_ville'])){
                        $xml->writeElement('ville', $valeur['structuresreferentes_ville']);
                    }
                    if(!is_null($valeur['structuresreferentes_codeinsee'])){
                        $xml->writeElement('codeinsee', $valeur['structuresreferentes_codeinsee']);
                    }
                    if(!is_null($valeur['structuresreferentes_numtel'])){
                        $xml->writeElement('numtel', $valeur['structuresreferentes_numtel']);
                    }
                    if(!is_null($valeur['structuresreferentes_email'])){
                        $xml->writeElement('email', $valeur['structuresreferentes_email']);
                    }
                    if(!is_null($valeur['structuresreferentes_zonesgeo'])){
                        $xml->writeElement('zonesgeo', $valeur['structuresreferentes_zonesgeo']);
                    }

                    //referents
                    if(!is_null($valeur['referents_structurereferente_id'])){
                        $xml->writeElement(
                            'structurereferente_id',
                            $this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
                                'structuresreferentes',
                                $valeur['referents_structurereferente_id']
                            )
                        );
                    }
                    if(!is_null($valeur['referents_civilite'])){
                        $xml->writeElement(
                            'civilite',
                            $this->CorrespondanceReferentiel->getIdReferentielFromCode(
                                'civilite',
                                $valeur['referents_civilite']
                            )
                        );
                    }
                    if(!is_null($valeur['referents_nom'])){
                        $xml->writeElement('nom', $valeur['referents_nom']);
                    }
                    if(!is_null($valeur['referents_prenom'])){
                        $xml->writeElement('prenom', $valeur['referents_prenom']);
                    }
                    if(!is_null($valeur['referents_email'])){
                        $xml->writeElement('email', $valeur['referents_email']);
                    }
                    if(!is_null($valeur['referents_fonction'])){
                        $xml->writeElement('fonction', $valeur['referents_fonction']);
                    }
                    if(!is_null($valeur['referents_numtel'])){
                        $xml->writeElement('numtel', $valeur['referents_numtel']);
                    }
                    if(!is_null($valeur['referents_date_cloture'])){
                        $xml->writeElement('date_cloture', $valeur['referents_date_cloture']);
                    }

                    //rdv thématiques
                    if(!is_null($valeur['rdv_thematique_typerdv_id'])){
                        $xml->writeElement(
                            'objetrdv_id',
                            $this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
                                'rdv_objet',
                                $valeur['rdv_thematique_typerdv_id']
                            )
                        );
                    }
                    if(!is_null($valeur['rdv_thematique_statutrdv_id'])){
                        $xml->writeElement(
                            'statutrdv_id',
                            $this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
                                'rdv_statut',
                                $valeur['rdv_thematique_statutrdv_id']
                            )
                        );
                    }
                    if(!is_null($valeur['rdv_thematique_acomptabiliser'])){
                        $xml->writeElement('acomptabiliser',($valeur['rdv_thematique_acomptabiliser'] != false) ? $valeur['rdv_thematique_acomptabiliser'] : '0');

                    }
                    if(!is_null($valeur['rdv_thematique_unefoisparan'])){
                        $xml->writeElement('unefoisparan',($valeur['rdv_thematique_unefoisparan'] != false) ? $valeur['rdv_thematique_unefoisparan'] : '0');

                    }

                    //code domaine
                    if(!is_null($valeur['code_domaine_codefamille_id'])){
                        $xml->writeElement(
                            'codefamille_id',
                            $this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
                                'code_famille',
                                $valeur['code_domaine_codefamille_id']
                            )
                        );
                    }

                    //code metier
                    if(!is_null($valeur['code_metier_codedomaine_id'])){
                        $xml->writeElement(
                            'codedomaine_id',
                            $this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
                                'code_domaine',
                                $valeur['code_metier_codedomaine_id']
                            )
                        );
                    }

                    //appellation métier
                    if(!is_null($valeur['appell_metier_codemetier_id'])){
                        $xml->writeElement(
                            'codemetier_id',
                            $this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
                                'code_metier',
                                $valeur['appell_metier_codemetier_id']
                            )
                        );
                    }

                    //nature contrat
                    if(!is_null($valeur['nature_contrat_definir_duree'])){
                        $xml->writeElement('definir_duree',($valeur['nature_contrat_definir_duree'] != false) ? $valeur['nature_contrat_definir_duree'] : '0');

                    }

                    //sujet cer
                    if(!is_null($valeur['cer_sujet_champ_texte'])){
                        $xml->writeElement('champ_texte',($valeur['cer_sujet_champ_texte'] != false) ? $valeur['cer_sujet_champ_texte'] : '0');

                    }

                    //sous sujet cer
                    if(!is_null($valeur['cer_sous_sujet_sujet_id'])){
                        $xml->writeElement(
                            'sujet_id',
                            $this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
                                'cer_sujet',
                                $valeur['cer_sous_sujet_sujet_id']
                            )
                        );
                    }
                    if(!is_null($valeur['cer_sous_sujet_champ_texte'])){
                        $xml->writeElement('champ_texte',($valeur['cer_sous_sujet_champ_texte'] != false) ? $valeur['cer_sous_sujet_champ_texte'] : '0');

                    }

                    //valeur par sous sujet cer
                    if(!is_null($valeur['cer_valeurs_sous_sujet_sujet_id'])){
                        $xml->writeElement(
                            'sous_sujet_sujet_id',
                            $this->CorrespondanceReferentiel->getIdReferentielFromIdTable(
                                'cer_sous_sujet',
                                $valeur['cer_valeurs_sous_sujet_sujet_id']
                            )
                        );
                    }
                    if(!is_null($valeur['cer_valeurs_sous_sujet_champ_texte'])){
                        $xml->writeElement('champ_texte',($valeur['cer_valeurs_sous_sujet_champ_texte'] != false) ? $valeur['cer_valeurs_sous_sujet_champ_texte'] : '0');

                    }

                    //commentaire cer
                    if(!is_null($valeur['cer_commentaire_champ_texte'])){
                        $xml->writeElement('champ_texte',($valeur['cer_commentaire_champ_texte'] != false) ? $valeur['cer_commentaire_champ_texte'] : '0');
                    }

                    //motifs sortie obligation accompagnement
                    if(!is_null($valeur['mot_sortie_oblign_acc_parent'])){
                        $xml->writeElement('parent', $valeur['mot_sortie_oblign_acc_parent']);
                    }
                    if(!is_null($valeur['mot_sortie_oblig_acc_typeemploi_code'])){
                        $xml->writeElement('typeemploi_code', $valeur['mot_sortie_oblig_acc_typeemploi_code']);
                    }

                    //type emploi
                    if(!is_null($valeur['type_emploi_code_type_emploi'])){
                        $xml->writeElement('codetypeemploi', $valeur['type_emploi_code_type_emploi']);
                    }

                    $xml->writeElement('actif', ($valeur['actif'] != false) ? $valeur['actif'] : '0');
                    $xml->endElement();
                }
                // Fin du sujet
                $xml->endElement();
            }
		}
		$xml->endElement();
		$xml->endDocument();
		$octets = $xml->flush();
		if ($octets == 0) {
			$success = true;
		} else {
			$success = false;
		}
		return $success;
	}


}