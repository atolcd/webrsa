<?php
    /**
     * Fichier source de la classe FranceTravailEnvoiOrientationShell.
     *
     * PHP 7.2
     *
     * @package app.Console.Command
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     *
     * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake FranceTravailEnvoiOrientation -app app
     *
     */
    App::uses( 'XShell', 'Console/Command' );
    App::uses('HttpSocket', 'Network/Http');


    /**
     * La classe FranceTravailEnvoiOrientationShell envoie les données d'orientation de WebRSA à France Travail
     *
     * @package app.Console.Command
     */
    class FranceTravailEnvoiOrientationShell extends XShell
    {
        private $base_url;
        private $access_token_url;
        private $access_token_username;
        private $access_token_password;
        private $scopes;
        private $api_recherche_usager_dt_nir;
        private $api_envoi_orientation;

        private $token;
        private $end_session;

        public $uses = array(
            'Personne',
            'Orientstruct',
            'RapportFluxFranceTravail'
        );


        private function _init() {
            $this->access_token_url = env('FRANCETRAVAIL_API_ACCESSTOKEN_URL');
            $this->access_token_username = env('FRANCETRAVAIL_API_ACCESSTOKEN_USERNAME');
            $this->access_token_password = env('FRANCETRAVAIL_API_ACCESSTOKEN_PASSWORD');

            $this->base_url = Configure::read('Module.Francetravail.APIURL.BaseURL');
            $this->scopes = Configure::read('Module.Francetravail.APIURL.ListeScope');

            $this->api_recherche_usager_dt_nir = Configure::read('Module.Francetravail.APIURL.RechercheUsager_parDateNaissance-NIR');
            $this->api_envoi_orientation = Configure::read('Module.Francetravail.APIURL.OrientationUsager');
        }


        /**
         *
         */
        public function main() {
            if( Configure::read('Module.Francetravail.EnvoiOrientation') == false ) {
                return;
            }
            $this->_init();

            $this->out('Démarrage : ' . date('d/m/Y H:i'));
            $orientations = $this->getOrientations();

            $now = date('Y-m-d H:i:s');
            $data = [
                'date_debut' => $now,
                'nb_pers_prevus' => count($orientations),
            ];

            $this->RapportFluxFranceTravail->save($data);

            $erreurs = $this->getTokenFT();
            if(isset($erreurs)) {
                $data = [
                    'id' => $this->RapportFluxFranceTravail->id,
                    'erreurs'=> 'Erreur lors de la récupération du token France Travail =>' . $erreurs
                ];

                $this->RapportFluxFranceTravail->save($data);
                $this->out('<error>Traitement échoué lors de la récupération du token France Travail =>' . $erreurs . '</error>');
                exit();
            }

            $nb_pers_traitees = 0;
            $nb_pers_non_traitees = 0;
            $liste_pers_traitee = [];
            $liste_pers_non_traite = [];
            $erreurs_traitement = [];

            foreach($orientations as $orientation) {
                // Renouveller le token si expiré
                $now = new DateTime();
                if($now > $this->end_session) {
                    $this->getTokenFT();
                    if(isset($error)) {
                        $data = [
                            'id' => $this->RapportFluxFranceTravail->id,
                            'modified' => $now,
                            'erreurs'=> 'Erreur lors de la récupération du token France Travail =>' . $error
                        ];
                        $this->RapportFluxFranceTravail->save($data);
                        $this->out('<error>Traitement échoué lors de la récupération du token France Travail =>' . $error . '</error>');
                    }
                }

                // Récupérer le token usager
                $token_usager = $this->getTokenUsager($orientation);

                if(isset($token_usager)) {
                    $success_or_message = $this->setOrientation($token_usager, $orientation);
                    if($success_or_message==true){
                        $this->Orientstruct->save([
                            'id' => $orientation['orientation_id'],
                            'is_envoye_francetravail' => true,
                            'date_envoi_francetravail' => $now,
                        ]);
                        $nb_pers_traitees++;
                        $liste_pers_traitee[] = $orientation['personne_id'];
                    }
                    else {
                        $nb_pers_non_traitees++;
                        $liste_pers_non_traite[] = $orientation['personne_id'];
                        $erreurs_traitement[] = "Erreur lors de l'enregistrement de l'orientation de la personne " . $orientation["personne_id"] . " : " . $success_or_message['message'] . "\n";
                    }
                }
                else {
                    $nb_pers_non_traitees++;
                    $liste_pers_non_traite[] = $orientation['personne_id'];
                    $erreurs_traitement []= "La personne " . $orientation["personne_id"] . " n'a pas été trouvée sur l'API de France Travail\n";
                }
            }

            // Enregistrer les logs
            $data = [
                'id' => $this->RapportFluxFranceTravail->id,
                'date_fin' => date('Y-m-d H:i:s'),
                'nb_pers_traitees' => $nb_pers_traitees,
                'nb_pers_non_traitees' => $nb_pers_non_traitees,
                'liste_pers_traitee' => ( isset($liste_pers_traitee) && !empty($liste_pers_traitee) ? implode(', ', $liste_pers_traitee) : null),
                'liste_pers_non_traite' => ( isset($liste_pers_non_traite) && !empty($liste_pers_non_traite) ? implode(', ', $liste_pers_non_traite) : null),
                'erreurs' => ( isset($erreurs_traitement) && !empty($erreurs_traitement) ? implode('\n', $erreurs_traitement) : null),
            ];

            $this->RapportFluxFranceTravail->save($data);

            if(isset($erreurs_traitement) && !empty($erreurs_traitement)) {
                $this->out('Traitement terminé avec des erreurs (voir table rapport_flux_francetravail');
            }
            else {
                $this->out('Traitement terminé avec succès');
            }
        }

        /**
         * Récupère les dernières orientations des personnes non envoyé à France travail
         */
        public function getOrientations(){
            $dernier_envoi_query = "
                SELECT
                    date_fin
                FROM administration.rapport_flux_francetravail
                ORDER BY id DESC NULLS LAST
                LIMIT 1";

            $dernier_envoi_array = $this->Personne->query($dernier_envoi_query);

            if(isset($dernier_envoi_array) && !empty($dernier_envoi_array) ){
                $date_dernier_envoi = date('Y-m-d', $dernier_envoi_array[0][0]["date_fin"]);
            } else {
                $date_dernier_envoi = '1900-01-01';
            }

            $query = "
                SELECT
                    DISTINCT ON (o.personne_id)                                     o.personne_id
                    , o.id                                                          orientation_id
                    , COALESCE(
                        ofr.crit_origine_calcul,
                        'INSCRIPTION_AUTO_CAF'
                    )                                                               origine
                    , p.dtnai                                                       date_naissance
                    , LEFT(TRIM(p.nir),13) || calcul_cle_nir(LEFT(TRIM(p.nir),13))  nir
                    , CASE
                        WHEN COALESCE(tp.code_type_orient, t.code_type_orient) = 'EMPLOI' THEN 'PED'
                        WHEN COALESCE(tp.code_type_orient, t.code_type_orient) = 'SOCIAL' THEN 'PSP'
                        WHEN COALESCE(tp.code_type_orient, t.code_type_orient) = 'SOCIOPRO' THEN 'PSO'
                    END                             parcours
                FROM orientsstructs o
                JOIN personnes p ON o.personne_id = p.id
                JOIN orientations_francetravail ofr ON p.id = ofr.personne_id
                JOIN typesorients t ON o.typeorient_id = t.id
                LEFT JOIN typesorients tp ON t.parentid = tp.id
                WHERE
                    p.dtnai IS NOT NULL
                    AND p.nir IS NOT NULL
                    AND LENGTH(TRIM(p.nir)) >= 13
                    AND o.statut_orient = 'Orienté'
                    AND o.date_valid > '" . $date_dernier_envoi ."'
                    AND o.is_envoye_francetravail IS FALSE
                ORDER BY o.personne_id, rgorient DESC NULLS LAST
            ";

            $orientations =$this->Orientstruct->query($query);

            // Enlever la 2eme dimension du tableau
            $results = array();
            foreach($orientations as $orient_array) {
                $results[] = $orient_array[0];
            }
            return $results;
        }

        /**
         * Récupère le token de connexion permettant de récupérer les jetons usagers
         */
        public function getTokenFT(){
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
                return;
            }
            else {
                return $response->reasonPhrase;
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
                $message ="La personne " . $personne["personne_id"] . " n'a pas été trouvée via sa date de naissance et son NIR (codeRetour : " . $result->codeRetour . ", message : " . $result->message . ")";
                $this->out("<warning>" . $message . "</warning>");
                return null;
            }

            return $result->jetonUsager;
        }

        /**
         * Envoi l'orientation du CD à France Travail
         */
        public function setOrientation($token_usager, $info_orientation) {
            $header = [
                "Accept: application/json",
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
                'ft-jeton-usager: ' . $token_usager,
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

            $url = $this->base_url . $this->api_envoi_orientation;
            $params = [
                "origine" => $info_orientation['origine'],
                "parcours" => $info_orientation['parcours'],
                "organisme" => 'CD'
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $curl_opt_basics);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

            $results = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if($http_code != 201){
                return json_decode($results)['message'];
            }

            return true;
        }

    }