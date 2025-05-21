<?php
	/**
	 * Fichier source de la classe FranceTravailRecuperationOrientationlShell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     *
     * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake FranceTravailRecuperationOrientation -app app
	 *
	 */
	App::uses( 'XShell', 'Console/Command' );
    App::uses('HttpSocket', 'Network/Http');


	/**
	 * La classe FranceTravailRecuperationOrientationShell récupère les données de France Travail permettant
     * de calculer les orientations automatiquement
	 *
	 * @package app.Console.Command
	 */
	class FranceTravailRecuperationOrientationShell extends XShell
	{
		private $base_url;
        private $access_token_url;
        private $access_token_username;
        private $access_token_password;
        private $scopes;
        private $api_recherche_usager_dt_nir;
        private $api_lecture_orientation;

        private $token;
        private $end_session;

        public $uses = array(
            "Personne",
            "Orientationfrancetravail"
        );


        private function _init() {
            $this->access_token_url = env("FRANCETRAVAIL_API_ACCESSTOKEN_URL");
            $this->access_token_username = env("FRANCETRAVAIL_API_ACCESSTOKEN_USERNAME");
            $this->access_token_password = env("FRANCETRAVAIL_API_ACCESSTOKEN_PASSWORD");

            $this->base_url = Configure::read("Module.Francetravail.APIURL.BaseURL");
            $this->scopes = Configure::read("Module.Francetravail.APIURL.ListeScope");

            $this->api_recherche_usager_dt_nir = Configure::read("Module.Francetravail.APIURL.RechercheUsager_parDateNaissance-NIR");
            $this->api_lecture_orientation = Configure::read("Module.Francetravail.APIURL.LectureOrientation");
        }


		/**
		 *
		 */
		public function main() {
            $this->_init();

            $personnes = $this->getPersonnes();
            $this->out("<info>Nombre de personnes à traiter : " . count($personnes) . "</info>");

            $this->getTokenFT();

            $nb_lecture = 0;
            $this->XProgressBar->start( count($personnes) );
            foreach($personnes as $personne_array) {
                $this->XProgressBar->next();

                // Renouveller le token si expiré
                $now = new DateTime();
                if($now > $this->end_session) {
                    $this->getTokenFT();
                }

                // Récupérer le token usager
                $personne = $personne_array[0];
                $token_usager = $this->getTokenUsager($personne);

                if(isset($token_usager)) {
                    $informations_usager = $this->getLectureOrientation($token_usager, $personne);
                    if(isset($informations_usager)) {
                        $this->setInformationsOrientation($informations_usager, $personne);
                        $nb_lecture++;
                    }
                }
                else {
                    $this->out("<error>La personne " . $personne["personne_id"] . " n'a pas été trouvée sur l'API de France Travail</error>" );
                }

            }

            $this->out("Traitement terminé : " . $nb_lecture . " orientations enregistrées pour " . count($personnes) . " personnes recherchées");
        }

        /**
         * Récupère la liste des personnes éligibles à la lecture des orientations selon la configuration en BDD
         */
        public function getPersonnes(){
            $info_flux = Configure::read("Module.Francetravail.Flux");

            $queryUseDernierePersonne = "";
            $joinUseDernierePersonne = "";

            if(isset($info_flux["UseDernierePersonne"]) && $info_flux["UseDernierePersonne"] == "true") {
                $queryUseDernierePersonne = "
                    , get_last_pers_last_dossier AS (
                        SELECT
                            DISTINCT p.id
                        FROM derniersdossiersallocataires dda
                        JOIN dossiers d ON dda.dossier_id = d.id
                        JOIN foyers f ON f.dossier_id = d.id
                        JOIN personnes p ON p.foyer_id = f.id
                    )";
                $joinUseDernierePersonne = "JOIN get_last_pers_last_dossier glpld ON glpld.id = p.id";
            }
            $query = "
                WITH get_orientation AS (
	                SELECT
                        DISTINCT ON (personne_id) personne_id
                        , is_envoye_francetravail
                        , date_envoi_francetravail
                    FROM orientsstructs o
                    ORDER BY o.personne_id, rgorient DESC NULLS LAST
                )
                " . $queryUseDernierePersonne . "
                SELECT
                    p.id                                                            personne_id
                    , p.dtnai                                                       date_naissance
                    , LEFT(TRIM(p.nir),13) || calcul_cle_nir(LEFT(TRIM(p.nir),13))  nir
                FROM personnes p
                JOIN derniersdossiersallocataires dda ON p.id = dda.personne_id
                JOIN situationsdossiersrsa s ON s.dossier_id = dda.dossier_id
                JOIN calculsdroitsrsa c ON c.personne_id = p.id
                JOIN prestations presta ON presta.personne_id = p.id
                " . $joinUseDernierePersonne . "
                LEFT JOIN orientations_francetravail ofr ON ofr.personne_id = p.id
                LEFT JOIN get_orientation o ON o.personne_id = p.id
                WHERE
                    presta.natprest = 'RSA'
                    AND p.dtnai IS NOT NULL
                    AND p.nir IS NOT NULL
                    AND LENGTH(TRIM(p.nir)) >= 13
            ";

            if(isset($info_flux["Situationdossierrsa.etatdosrsa"])) {
                $etatdosrsa = implode("','", $info_flux["Situationdossierrsa.etatdosrsa"]);
                $query .= " AND s.etatdosrsa IN ('" . $etatdosrsa . "')";
            }

            if(isset($info_flux["Calculdroitrsa.toppersdrodevorsa"])) {
                $query .= " AND c.toppersdrodevorsa = '" . $info_flux["Calculdroitrsa.toppersdrodevorsa"] . "'";
            }

            if(isset($info_flux["NbJoursDerniereRecuperationDonnees"])) {
                $query .= " AND ( ofr.id IS NULL OR ofr.modified > CURRENT_DATE - INTERVAL '" . $info_flux["NbJoursDerniereRecuperationDonnees"] . " days' )";
            }

            if(isset($info_flux["NbJoursDerniereMAJOrientations"])) {
                $query .= " AND ( o.personne_id IS NULL OR o.is_envoye_francetravail IS FALSE OR (o.is_envoye_francetravail IS TRUE AND o.date_envoi_francetravail > CURRENT_DATE - INTERVAL '" . $info_flux["NbJoursDerniereMAJOrientations"] . " days' ) )";
            }

            return $this->Personne->query($query);
        }

        /**
         * Récupère le token de connexion permettant de récupérer les jetons usagers
         */
        public function getTokenFT($attemps = 1){
            $params = [
                "grant_type" => "client_credentials",
                "client_id" => $this->access_token_username,
                "client_secret" => $this->access_token_password,
                "scope" => $this->scopes
            ];

            $HttpSocket = new HttpSocket();

            $response = $HttpSocket->post(
                $this->access_token_url,
                $params
            );

            if( $response->code = 200) {
                $results = json_decode($response->body);
                $this->token = $results->access_token;

                $this->end_session = new DateTime('+' . $results->expires_in . ' seconds');
            }
            else {
                if($attemps < 5) {
                    $this->out("<warning> Impossible de récupérer le token France travail pour la raison suivante : " . $response->reasonPhrase . " . Nouvelle tentative en cours (" . $attemps . "/5) </warning>");
                    sleep(5);
                    $this->getTokenFT($attemps++);
                }
                else {
                    $this->out("<error>Echec de récupération du token, France Travail, merci de les contacter ou de réessayer plus tard</error>");
                    exit(0);
                }
            }
        }

        /**
         * Récupération du jeton usager
         */
        public function getTokenUsager($personne) {
            $header = [
                "Accept: application/json",
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
                'pa-identifiant-agent: BATCH-ATOL',
                'pa-nom-agent: atol-recherche-usager',
                'pa-prenom-agent: atol-recherche-usager',
            ];

            $curl_opt_basics = [
                CURLOPT_POST => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_HTTPHEADER => $header,
            ];

            $url = $this->base_url . $this->api_recherche_usager_dt_nir;
            $params = [
                "dateNaissance" => $personne["date_naissance"],
                "nir" => $personne["nir"]
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $curl_opt_basics);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

            $result_json = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $result = json_decode($result_json);

            if($http_code != 200){
                $message ="La personne " . $personne["personne_id"] . " n'a pas été trouvée via sa date de naissance et son NIR";
                if($result_json  === false) {
                    $message .= "(code retour HTTP : " . $http_code . ", erreur reçu : " . curl_error($ch);
                } else {
                    $message .= "(codeRetour : " . $result->codeRetour . ", message : " . $result->message . ")";
                }
                $this->out("<warning>" . $message . "</warning>");
                return null;
            }

            return $result->jetonUsager;
        }

        /**
         * Lecture de l'orientation de l'usager passé en paramètre
         */
        public function getLectureOrientation($token_usager, $personne) {
            $header = [
                "Accept: application/json",
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
                'ft-jeton-usager: ' . $token_usager,
                'pa-identifiant-agent: BATCH-ATOL',
                'pa-nom-agent: atol-recherche-usager',
                'pa-prenom-agent: atol-recherche-usager',
            ];

            $url = $this->base_url . $this->api_lecture_orientation;

            $curl_opt_basics = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_URL => $url
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $curl_opt_basics);

            $result_json = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $result = json_decode($result_json, true);
            if($http_code == 200) {
                if(empty($result)) {
                    return array();
                }
                return $result[0];
            }
            elseif($http_code == 204){
                $this->out("<info>La personne " . $personne["personne_id"] . " n'a pas d'informations d'orientation sur France Travail");
            }
            else {
                $this->out("<warning>Problème de récupération des informations d'orientation pour la personne " . $personne['personne_id'] . " (codeRetour : " . $result->codeRetour . ", message : " . $result->message . ")</warning>");
            }
            return null;
        }

        /**
         * Ajoute ou met à jour l'orientation de l'usager
         */
        public function setInformationsOrientation($informations_usager, $personne){
            $this->Orientationfrancetravail->clear();
            $orientation_existante = $this->Orientationfrancetravail->find(
                'first',
                [
                    'fields' => ['id'],
                    'conditions' => [
						'Orientationfrancetravail.personne_id' => $personne['personne_id']
                    ],
                    'recursive' => -1
                ]
            );

            if(isset($orientation_existante) && !empty($orientation_existante['Orientationfrancetravail']['id'])) {
                $this->Orientationfrancetravail->id = $orientation_existante['Orientationfrancetravail']['id'];
            }

            $data_init = [
                'personne_id' => $personne['personne_id'],
                'code_parcours' => $informations_usager['parcours'] ?? null,
                'organisme' => $informations_usager['organisme'] ?? null,
                'struct_libelle' => $informations_usager['structure']['libelle_structure'] ?? null,
                'struct_decision_libelle' => $informations_usager['structure_decision']['libelle_structure'] ?? null,
                'statut' => $informations_usager['statut'] ?? null,
                'etat' => $informations_usager['etat'] ?? null,
                'date_entree_parcours' => $informations_usager['date_entree_parcours'] ?? $informations_usager['date_creation'],
                'date_modification' => $informations_usager['date_modification'] ?? null,
                'crit_origine_calcul' => $informations_usager['origine'] ?? null
            ];

            $data_critere = [];
            if(isset($informations_usager["criteres_orientation"])) {
                $data_critere = [
                    'crit_situation_professionnelle' => $informations_usager['criteres_orientation']['situation_professionnelle'] ?? null,
                    'crit_type_emploi' => $informations_usager['criteres_orientation']['type_emploi'] ?? null,
                    'crit_niveau_etude' => $informations_usager['criteres_orientation']['niveau_etude'] ?? null,
                    'crit_capacite_a_travailler' => $informations_usager['criteres_orientation']['capacite_a_travailler'] ?? false,
                    'crit_projet_pro' => $informations_usager['criteres_orientation']['projet_pro'] ?? null,
                    'crit_contrainte_sante' => $informations_usager['criteres_orientation']['contrainte_sante'] ?? null,
                    'crit_contrainte_logement' => $informations_usager['criteres_orientation']['contrainte_logement'] ?? null,
                    'crit_contrainte_mobilite' => $informations_usager['criteres_orientation']['contrainte_mobilite'] ?? null,
                    'crit_contrainte_familiale' => $informations_usager['criteres_orientation']['contrainte_familiale'] ?? null,
                    'crit_contrainte_financiere' => $informations_usager['criteres_orientation']['contrainte_financiere'] ?? null,
                    'crit_contrainte_numerique' => $informations_usager['criteres_orientation']['contrainte_numerique'] ?? null,
                    'crit_contrainte_admin_jur' => $informations_usager['criteres_orientation']['contrainte_administrative_juridique'] ?? null,
                    'crit_contrainte_francais_calcul' => $informations_usager['criteres_orientation']['contrainte_francais_calcul'] ?? null,
                    'crit_boe' => $informations_usager['criteres_orientation']['boe'] ?? false,
                    'crit_baeeh' => $informations_usager['criteres_orientation']['baeeh'] ?? false,
                    'crit_scolarite_etab_spec' => $informations_usager['criteres_orientation']['scolarite_etablissement_specialise'] ?? false,
                    'crit_esat' => $informations_usager['criteres_orientation']['esat'] ?? false,
                    'crit_boe_souhait_accompagnement' => $informations_usager['criteres_orientation']['boe_souhait_accompagnement'] ?? false,
                    'crit_msa_autonomie_recherche_emploi' => $informations_usager['criteres_orientation']['msa_autonomie_recherche_emploi'] ?? null,
                    'crit_msa_demarches_professionnelles' => $informations_usager['criteres_orientation']['msa_demarches_professionnelles'] ?? null,
                ];
            }

            $data_decision = [];
            if(isset($informations_usager['decision'])) {
                $data_decision = [
                    'decision_date_sortie_parcours' => $informations_usager['date_sortie_parcours'] ?? null,
                    'decision_motif_sortie_parcours' => $informations_usager['motif_sortie_parcours'] ?? null,
                    'decision_etat' => $informations_usager['decision']['etat_decision'] ?? null,
                    'decision_date' => $informations_usager['decision']['date_decision'] ?? null,
                    'decision_organisme' => $informations_usager['decision']['organisme_decision'] ?? null,
                    'decision_motif_refus' => $informations_usager['decision']['motif_refus'] ?? null,
                    'decision_commentaire_refus' => $informations_usager['decision']['commentaire_refus'] ?? null,
                    'decision_structure_libelle' => $informations_usager['decision']['structure']['libelle_structure'] ?? null,
                ];
            }

            $data = array_merge($data_init, $data_critere, $data_decision);

            if(!$this->Orientationfrancetravail->save($data)) {
                $this->out("<warning>Problème d'enregistrement en base de données" . $this->Orientationfrancetravail->validationErrors . "</warning>");
            }
        }
	}