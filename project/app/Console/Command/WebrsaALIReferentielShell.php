<?php
	/**
	 * Fichier source de la classe WebrsaALIReferentielShell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     *
     * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake WebrsaALIReferentiel -app app
	 *
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe WebrsaALIReferentielShell ...
	 *
	 * @package app.Console.Command
	 */
	class WebrsaALIReferentielShell extends XShell
	{
		public $uses = array(
            'CorrespondanceReferentiel',
            'StructurereferenteZonegeographique',
            'Sortieaccompagnementd2pdv93'
        );


		/**
		 *
		 */
		public function main() {
            //on récupère les référentiels à utiliser dans la variable de conf et leurs ids dans la table sujetsreferentiels
            $sql_sujets = "
                SELECT *
                FROM administration.sujetsreferentiels
                WHERE actif = true
            ";
            $sujets = $this->CorrespondanceReferentiel->query($sql_sujets);

            $codes_actuels = $this->CorrespondanceReferentiel->find('all');
            $update = [];


            //ENUMS
            //On récupère les enum et compare avec les valeurs dans la table
            $sujets_enum = $this->getSujetsParCategorie($sujets, 'enum');
            foreach($sujets_enum as $sujet) {
                $sujet = $sujet[0];

                //On cherche les infos dans les enum
                $model = ClassRegistry::init($sujet['modele_enum'] );
                $enum = $model->enum($sujet['nom_enum']);

                //On récupère les valeurs déjà présentes dans la table
                $codes_table = $this->getCodes($codes_actuels, $sujet['id']);

                //On prépare les màj de la table
                $update = $this->constructionTableauMaj($update, $enum, $codes_table, $sujet['id']);
            }

            //CONFIGURATIONS
            //On récupère les configurations et compare avec les valeurs dans la table
            $sujets_config = $this->getSujetsParCategorie($sujets, 'conf');

            foreach ($sujets_config as $sujet){
                $sujet = $sujet[0];

                //On récupère la valeur de la variable de conf
                $sql_conf = "SELECT value_variable from public.configurations where lib_variable like '{$sujet['nom_config']}'";
                $conf = $this->CorrespondanceReferentiel->query($sql_conf);
                $conf = json_decode($conf[0][0]['value_variable'], true);

                //On récupère les valeurs déjà présentes dans la table
                $codes_table = $this->getCodes($codes_actuels, $sujet['id']);

                //On prépare les màj de la table
                $update = $this->constructionTableauMaj($update, $conf, $codes_table, $sujet['id']);


            }


            //TABLES
            //On récupère les sujets stockés dans des tables
            $sujets_bdd = $this->getSujetsParCategorie($sujets, 'bdd');

            foreach ($sujets_bdd as $sujet){
                $sujet = $sujet[0];

                if(!is_null($sujet['correspondance_colonnes'])){
                    $sujet['correspondance_colonnes'] = json_decode($sujet['correspondance_colonnes'], true);
                    $present_base = $this->GetFromTable($sujet);

                    //On récupère les valeurs déjà présentes dans la table
                    $codes_table = $this->getCodes($codes_actuels, $sujet['id']);

                    $update = $this->constructionTableauMajTable($update, $present_base, $codes_table, $sujet);


                }
            }


            //On enregistre les modifications
            $success = true;
            if(!empty($update)){
                $success = $this->CorrespondanceReferentiel->saveMany($update, ['validate' => false]);
            }

            //On vérifie les zones géographiques des structures référentes
            $sql_struct_zone =
            "select sz.structurereferente_id, array_agg(sz.zonegeographique_id) as zones
                from public.structuresreferentes_zonesgeographiques sz
                join public.structuresreferentes s on s.id = sz.structurereferente_id
                where s.actif = 'O'
                group by sz.structurereferente_id
                order by sz.structurereferente_id";
            $struct_zone = $this->CorrespondanceReferentiel->query($sql_struct_zone);
            $struct_zone = array_column($struct_zone, 0);

            $id_sujet = $this->CorrespondanceReferentiel->query("Select id from sujetsreferentiels where code = 'structuresreferentes'");
            $struct_actuel = $this->CorrespondanceReferentiel->find(
                'list',
                [
                    'fields' => [
                        'CorrespondanceReferentiel.id',
                        'CorrespondanceReferentiel.structuresreferentes_zonesgeo',
                        'CorrespondanceReferentiel.id_dans_table'
                    ],
                    'conditions' => [
                        'sujetsreferentiels_id' => $id_sujet[0][0]['id']
                    ]
                ]
            );


            //On compare et on met à jour si besoin
            foreach($struct_zone as $id_str => $value){
                if(isset($struct_actuel[$value['structurereferente_id']]) && current($struct_actuel[$value['structurereferente_id']]) != $value['zones']){
                    $update_struct[] = [
                        'id' => key($struct_actuel[$value['structurereferente_id']]),
                        'sujetsreferentiels_id' => $id_sujet[0][0]['id'],
                        'structuresreferentes_zonesgeo' => $value['zones']
                    ];
                }
            }

            if(!empty($update_struct)){
                $success = $success && $this->CorrespondanceReferentiel->saveMany($update_struct, ['validate' => false]);
            }

            //On vérifie les thématiques autorisées qu'une fois par an
            $id_sujet = $this->CorrespondanceReferentiel->query("Select id from sujetsreferentiels where code = 'rdv_thematique'");
            $conf_thematiques = Configure::read('Rendezvous.thematiqueAnnuelleParStructurereferente');
            $thematiques_actuel = $this->CorrespondanceReferentiel->find(
                'list',
                [
                    'fields' => [
                        'CorrespondanceReferentiel.id',
                        'CorrespondanceReferentiel.rdv_thematique_unefoisparan',
                        'CorrespondanceReferentiel.id_dans_table'
                    ],
                    'conditions' => [
                        'sujetsreferentiels_id' => $id_sujet[0][0]['id']
                    ]
                ]
            );

            $update_thematique = [];

            foreach ($thematiques_actuel as $key_thematique => $value){
                if(array_search($key_thematique, $conf_thematiques) !== false && current($value) != true){
                    $update_thematique[] = [
                        'id' => key($value),
                        'sujetsreferentiels_id' => $id_sujet[0][0]['id'],
                        'rdv_thematique_unefoisparan' => true
                    ];
                } else if (array_search($key_thematique, $conf_thematiques) === false && current($value) == true) {
                    $update_thematique[] = [
                        'id' => key($value),
                        'sujetsreferentiels_id' => $id_sujet[0][0]['id'],
                        'rdv_thematique_unefoisparan' => false
                    ];
                }
            }

            if(!empty($update_thematique)){
                $success = $success && $this->CorrespondanceReferentiel->saveMany($update_thematique, ['validate' => false]);
            }

            if($success) {
                $this->out('Mise à jour terminée avec succès');
            }
        }

        public function getSujetsParCategorie($sujets, $categorie){
            return
                array_filter(
                    $sujets,
                    function ($v) use ($categorie) {
                        switch ($categorie) {
                            case 'enum':
                                return ((!empty($v[0]['modele_enum'])) && (!empty($v[0]['nom_enum'])));
                                break;
                            case 'conf':
                                return (!empty($v[0]['nom_config']));
                                break;
                            case 'bdd':
                                return (!empty($v[0]['nom_table']));
                                break;
                        }
                    }
                );
        }

        public function getCodes($codes_actuels, $id){
            return
                array_column(
                    array_filter(
                        $codes_actuels,
                        function($a) use ($id){
                            return $a['CorrespondanceReferentiel']['sujetsreferentiels_id'] == $id;
                        }
                    ),
                    'CorrespondanceReferentiel'
                );
        }

        public function constructionTableauMaj($update, $actuel, $codes_table, $sujet_id){
            //On regarde les lignes différentes entre les 2 tableaux
            //Ligne présente dans les actuels et pas dans table => On crée dans la table
            $codes_a_ajouter = array_diff(array_keys($actuel), array_column($codes_table, 'code'));
            foreach ($codes_a_ajouter as $code){
                $update[] = [
                    'sujetsreferentiels_id' => $sujet_id,
                    'code' => $code,
                    'libelle' => $actuel[$code],
                    'actif' => true
                ];
            }

            foreach ($codes_table as $code) {
                //TODO : rajouter une restriction sur le actif des tables qui l'ont
                if(!isset($actuel[$code['code']]) && $code['actif'] == true) {
                    //Ligne présente dans table et pas dans actuel => On désactive dans table
                    $update[] = [
                        'id' => $code['id'],
                        'actif' => false
                    ];

                } else if (isset($actuel[$code['code']]) && $actuel[$code['code']] != $code['libelle']) {
                    //Ligne présente dans actuel et dans table mais avec un libellé différent => On modifie dans table
                    $update[] = [
                        'id' => $code['id'],
                        'libelle' => $actuel[$code['code']],
                        'actif' => true
                    ];
                } else if (isset($actuel[$code['code']]) && $code['actif'] == false){
                    // Ligne présente mais inactive => on réactive
                    $update[] = [
                        'id' => $code['id'],
                        'actif' => true
                    ];
                }
            }

            return $update;
        }

        public function constructionTableauMajTable($update, $actuel, $codes_table, $sujet){
            $colonnes = $sujet['correspondance_colonnes'];
            unset($colonnes['actif']);
            unset($colonnes['condition']);

            //On ajoute le libelle parent dans le cas de sorties d'accompagnement
            if(in_array($sujet['code'], ['motif_sortie_obligation_accompagnement','motif_changement_admin'])){
                if($sujet['code'] == 'motif_sortie_obligation_accompagnement'){
                    $colonnes['mot_sortie_oblign_acc_parent'] = 'parent_lib';
                } else if ($sujet['code'] == 'motif_changement_admin'){
                    $colonnes['mot_chang_admin_parent'] = 'parent_lib';
                }

                $actuel = array_map(
                    function ($v) {
                        $v['parent_lib'] = $this->Sortieaccompagnementd2pdv93->findById($v['parent_id'])['Sortieaccompagnementd2pdv93']['name'];
                        return $v;
                    },
                    $actuel
                );
            }

            //On regarde les lignes différentes entre les 2 tableaux
            //Ligne présente dans les actuels et pas dans table => On crée dans la table
            $codes_a_ajouter = array_diff(array_column($actuel, 'id'), array_column($codes_table, 'id_dans_table'));
            foreach ($codes_a_ajouter as $code){
                $id = array_search($code, array_column($actuel, 'id'));
                $actif = true;
                if(isset($actuel[$id]['actif']) && ($actuel[$id]['actif'] == false || $actuel[$id]['actif'] == 'N')){
                    $actif = false;
                }
                $maj =
                [
                    'sujetsreferentiels_id' => $sujet['id'],
                    'actif' => $actif
                ];
                foreach($colonnes as $nom_new => $nom_table){
                    $maj[$nom_new] = $actuel[$id][$nom_table];
                }
                $update[] = $maj;
            }


            foreach ($codes_table as $code) {
                $id = array_search($code['id_dans_table'], array_column($actuel, 'id'));

                if($id === false && $code['actif'] == true) {
                    // Ligne présente dans table et pas dans actuel => On désactive dans table
                    $update[] = [
                        'id' => $code['id'],
                        'actif' => false
                    ];

                } else if ($id !== false) {
                    //Ligne présente dans les 2 mais avec des différences => On met à jour les différences
                    $maj = [];

                    foreach($colonnes as $nom_new => $nom_table){
                    	if($nom_new == 'actif') {
                    		if(($actuel[$id]['actif'] === false || $actuel[$id]['actif'] == 'N' || $actuel[$id]['actif'] === 0) && $code['actif'] == true){
	                    		$maj['actif'] = false;
	                    		$actif = false;
	                    	}
                       } else if($actuel[$id][$nom_table] != $code[$nom_new]){
                            $maj[$nom_new] = $actuel[$id][$nom_table];
                        }
                    }
                    if(!empty($maj)) {
                    	     $actif = true;
                            if(isset($actuel[$id]['actif']) && ($actuel[$id]['actif'] === false || $actuel[$id]['actif'] == 'N' || $actuel[$id]['actif'] === 0)){
                            $actif = false;
                        }
                        $maj = array_merge(
                            [
                                'id' => $code['id'],
                                'sujetsreferentiels_id' => $sujet['id'],
                                'actif' => $actif
                            ],
                            $maj
                        );
                        $update[] = $maj;
                   }  else if ($code['actif'] == true) {
                        // Ligne présente et identique mais active à tort => on désactive
                        if(isset($actuel[$id]['actif']) && ($actuel[$id]['actif'] === false || $actuel[$id]['actif'] == 'N' || $actuel[$id]['actif'] === 0)){
                        	$update[] = [
                                'id' => $code['id'],
                                'actif' => false
		                    ];
                        }
                   } else if ($code['actif'] == false && !isset($sujet['correspondance_colonnes']['actif'])) {
                        // Ligne présente et identique mais inactive à tort => on réactive
                        $update[] = [
                            'id' => $code['id'],
                            'actif' => true
                        ];
                   }
                }
            }

            return $update;
        }

        public function GetFromTable($sujet){
            $colonnes = $sujet['correspondance_colonnes'];
            $conditions = "";

            if(isset($colonnes['condition'])){
                $conditions = "where {$colonnes['condition']}";

            }
            $sql = "select * from public.{$sujet['nom_table']} {$conditions}";
            $sujets = $this->CorrespondanceReferentiel->query($sql);

            return array_column($sujets, 0);

        }

	}