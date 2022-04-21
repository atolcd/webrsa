<?php
	/**
	 * Code source de la classe AlgorithmeorientationController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses( 'AppController', 'Controller' );
	/**
	 * La classe AlgorithmeorientationController s'occupe de la gestion de l'algorithme d'orientation
	 *
	 * @package app.Controller
	 */
	class AlgorithmeorientationController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Algorithmeorientation';

         /**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Algorithmeorientation',
			'Orientstruct',
			'Personne',
			'Criterealgorithmeorientation',
			'Zonegeographique',
			'StructurereferenteTypeorientZonegeographique',
			'Structurereferente',
			'Typeorient',
			'Communautesr',
			'CommunautesrStructurereferente',
			'Rendezvous'
		);


		/**
		 * Components utilisés
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index' => array('filter' => 'Search'),
					'listeOrientables' => array('filter' => 'Search'),
					'search' => array('filter' => 'Search'),
				),
			),
			'InsertionsBeneficiaires'
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Allocataires',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Search.SearchForm',
			'Html',
			'Default'
		);


		/**
		 * Fonction d'entrée dans l'algorithme d'orientation
		 */
		public function orientation(){
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}
			Cache::delete('orientables');
			Cache::delete('orientations');
			Cache::delete('pbAdresses');
			Cache::delete('depassements');
			Cache::delete('orientations_apres_arbitrage');
			Cache::delete('orientations_reformartees');

			$bloquer = $this->StructurereferenteTypeorientZonegeographique->checkBlocageAlgo();

			$resultats = $this->_listeOrientables();
			if(!empty($resultats)){
				$pbAdresses = $this->_controleAdresses($resultats);
				Cache::write('orientables', $resultats);
				if(empty($pbAdresses)){
					$this->redirect(['action' => 'affichageOrientables']);
				} else {
					Cache::write('pbAdresses', $pbAdresses);
				}
			}

			$this->set(compact('pbAdresses', 'bloquer'));

		}

		/**
		 * Affiche les statistiques sur les orientables
		 */
		public function affichageOrientables($supprimer_adresses = false){
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
				Cache::delete('depassements');
			}
			if($supprimer_adresses) {
				$resultats = array_diff_key(Cache::read('orientables'), Cache::read('pbAdresses'));
				Cache::write('orientables', $resultats, 'default');
			} else {
				$resultats = Cache::read('orientables');
			}

			$infos_graphiques = $this->_calculsGraphiquesOrientables($resultats);

			$this->set(compact('resultats', 'infos_graphiques'));
			$this->view = 'liste_orientables';
		}

		/**
		 * Récupère la requête générée par le formulaire de recherche et retourne les résultats
		 */
		private function _listeOrientables(){
			$Cohortes = $this->Components->load( 'WebrsaCohortesAlgoorientation' );

			$query = $Cohortes->cohorte(
				[
					'modelName' => 'Personne',
					'modelRechercheName' => 'WebrsaCohortesAlgoorientation',
					'returnQuery' => true,
					'cache_default' => 'default_AlgorithmeorientationOrientationForm'
				]
			);

			if(isset($query)){
				unset($query['limit']);
				return $this->Personne->find('all', $query);
			}
		}

		/**
		 * Retourne les orientables dont l'adresse ne permet pas de les relier à une zone géographique
		 */
		private function _controleAdresses($resultats){
			$pbAdresses = [];
			$listeCodeinsee = $this->Zonegeographique->find('list', ['fields' => ['Zonegeographique.codeinsee']]);
			foreach($resultats as $key => $orientable){
				if(isset($orientable['Adresse']['numcom'])){
					if(!in_array($orientable['Adresse']['numcom'], $listeCodeinsee)){
						$pbAdresses[$key] = $orientable;
					}
				} else {
					$pbAdresses[$key] = $orientable;
				}
			}
			return $pbAdresses;
		}

		/**
		 * Export CSV des orientables dont l'adresse ne permet pas de les relier à une zone géographique
		 */
		public function exportcsv_adresses(){
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}
			$pbAdresses = Cache::read('pbAdresses');

			$this->set('results', $pbAdresses);
			$this->set('options', []);
			$this->view = '/Elements/ConfigurableQuery/exportcsv';
			$this->layout = null;
		}

		/**
		 * Export CSV de tous les orientables
		 */
		public function exportcsv_orientables(){
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}
			$orientables = Cache::read('orientables');

			$this->set('results', $orientables);
			$this->set('options', []);
			$this->view = '/Elements/ConfigurableQuery/exportcsv';
			$this->layout = null;
		}

		/**
		 * Export CSV de tous les orientables et de leurs orientations
		 */
		public function exportcsv_orientations(){
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}

			$orientations = Cache::read('orientations_reformartees');

			$this->set('results', $orientations);
			$this->set('options', []);
			$this->view = '/Elements/ConfigurableQuery/exportcsv';
			$this->layout = null;
		}

		/**
		 * Export CSV des nouveaux orientés dans une structure en dépassement de capacité maximale
		 */
		public function exportcsv_depassement($id_struct){
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}

			$depassements = Cache::read('depassements')[$id_struct]['donnees'];

			$this->set('results', $depassements);
			$this->set('options', []);
			$this->view = '/Elements/ConfigurableQuery/exportcsv';
			$this->layout = null;
		}

		/**
		 * Export csv pour les statistiques des orientables.
		 */
		public function exportcsv_statsorientables() {
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}
			$orientables = Cache::read('orientables');
			$results = $this->_calculsGraphiquesOrientables($orientables);
			$named = Hash::expand( $this->request->named, '__');
			foreach($results as $nom => $r){
				$tranches[$nom] = array_keys($r['value']);
			}
			unset($tranches['parcours']);

			$filename = 'stats_orientables';
			$colonnes = ['value', 'pourcentage'];
			$intitules = ['Valeur', 'Pourcentage'];
			$categories = ['Age', 'Sexe', 'Ville', 'EPT', 'Rôle', 'Dsp'];

			$export = $this->_generationexportcsv($tranches, $results, $colonnes, $intitules, count($orientables), $categories);

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Export csv pour les statistiques des orientations.
		 */
		public function exportcsv_statsorientations() {
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}
			$orientations = Cache::read('orientations_reformartees');
			$results = $this->_calculsGraphiquesOrientables($orientations, true);
			$named = Hash::expand( $this->request->named, '__');
			$tranches['role'] = array_keys($results['role']['value']);
			$tranches['dsp'] = array_keys($results['dsp']['value']);

			$tableau_croise['parcours'] = $results['parcours'];
			$tableau_croise['villes'] =  $results['villes'];

			$filename = 'stats_orientations';
			$colonnes = ['value', 'pourcentage'];
			$intitules = ['Valeur', 'Pourcentage'];
			$categories = ['Rôle', 'Dsp', 'Parcours et ville'];

			$export = $this->_generationexportcsv($tranches, $results, $colonnes, $intitules, count($orientations), $categories, $tableau_croise);

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Génération des csv de statistiques
		 */
		private function _generationexportcsv ($tranches, $results, $colonnes, $intitules, $total, $categories, $tableau_croise = null) {

			$export = array ();
			$i = 0;
			$k = 0;

			// Total et titres
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$export[$i++] = array ('', 'Nombre total d\'orientables', $total, '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$export[$i++] = array_merge (array ('', 'Catégorie'), $intitules);
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

			foreach ($tranches as $categorie => $groupes) {
				$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
				$export[$i++] = array ('', $categories[$k++], '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

				foreach ($groupes as $value) {
					$j = 0;
					$export[$i][$j++] = '';
					$export[$i][$j++] = $value;


					foreach ($colonnes as $colonne) {
						// Ligne (écriture)
						$valeur = 0;
						if (isset ($results[$categorie][$colonne][$value])) {
							$valeur = $results[$categorie][$colonne][$value];
						}
						$export[$i][$j] = $valeur;
						$j++;
					}

					$i++;
				}
				$i++;
			}

			//Tableau à double entrée Villes et Parcours
			if($tableau_croise != null){
				$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
				$export[$i++] = array ('', $categories[$k++], '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
				foreach ($tableau_croise['parcours']['value'] as $nom_parcours => $value){
					$noms_parcours[] = $nom_parcours;
					$tot_parcours[] = $value.' ('.$tableau_croise['parcours']['pourcentage'][$nom_parcours].')';
				}
				$export[$i++] = array_merge (['', ''], $noms_parcours, ['', '', '', '', '', '', '', '', '', '', '', '', '', '']);
				foreach ($tableau_croise['villes']['value'] as $nom_ville => $parcours){
					$ligne = [];
					foreach ($parcours as $nom => $nombre){
						$ligne[] = $nombre.' ('.$tableau_croise['villes']['pourcentage'][$nom_ville][$nom].')';
					}
					$export[$i++] = array_merge (['', $nom_ville], $ligne, ['', '', '', '', '', '', '', '', '', '', '', '', '', '']);
				}
				$export[$i++] = array_merge (['', 'Total'], $tot_parcours, ['', '', '', '', '', '', '', '', '', '', '', '', '', '']);


			}

			return $export;
		}


		public function simulationAlgo(){
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}

			//on supprime les valeurs enregistrées du formulaire
			Cache::delete('default_AlgorithmeorientationOrientationForm');

			//Si les orientations n'ont pas déjà été calculées, on les calcule
			if(Cache::read('orientations') == false){
				$this->_calculToutesOrientations();
			}

			$orientables = Cache::read('orientables');
			$orientations = Cache::read('orientations');


			//on regarde si les capacités d'accueils maximales sont dépassées
			$nb_ajout_struc = array_count_values(array_column(array_column($orientations, 'orientation'), 'structure_id'));
			$capacite_max_struc = $this->Structurereferente->find('list',['fields' => ['id', 'capacite_max']]);
			$lib_struc = $this->Structurereferente->find('list',['fields' => ['id', 'lib_struc']]);
			$nb_orientes_struc = $this->Orientstruct->nombreOrientesParStructureReferente();

			//On calcule le tableau des dépassements
			$depassement = [];
			foreach ($nb_ajout_struc as $key => $ajout){
				$key_nb_orient_struc = array_search($key, array_column(array_column($nb_orientes_struc, 0), 'structurereferente_id'));
				if($capacite_max_struc[$key] != null){
					if(($ajout + $nb_orientes_struc[$key_nb_orient_struc][0]['structurereferente_id']) > $capacite_max_struc[$key]){
						$depassement[$key]['nb_nouveaux'] = $ajout;
						$depassement[$key]['nb_actuel'] = $nb_orientes_struc[$key_nb_orient_struc][0]['structurereferente_id'];
						$depassement[$key]['nb_depassement'] = $ajout + $nb_orientes_struc[$key_nb_orient_struc][0]['structurereferente_id'] - $capacite_max_struc[$key];
						$depassement[$key]['capacite_max'] = $capacite_max_struc[$key];
						$depassement[$key]['lib_struc'] = $lib_struc[$key];
						$depassement[$key]['donnees'] = $orientables;
						$orientation_struct = array_values(array_filter(
							$orientations,
							function($v) use ($key){
								return $v['orientation']['structure_id'] == $key;
							}
						));
						foreach($depassement[$key]['donnees'] as $key2 => $orientable){
							$key_orient = array_search($orientable['Personne']['id'], array_column(array_column($orientation_struct, 0), 'id_personne'));
							if($key_orient !== false){
								$depassement[$key]['donnees'][$key2]['Propositionorientation'] = $orientation_struct[$key_orient]['orientation'];
							} else {
								unset($depassement[$key]['donnees'][$key2]);
							}
						}
					}
				}
			}



			if(!empty($depassement)){
				//on s'oriente sur la page de dépassement puis on revient ici
				Cache::write('depassements', $depassement);
				$this->redirect(['action' => 'depassements']);

			} else {
				//on affiche les résultats
				$this->redirect(['action' => 'affichageSimulationAlgo']);

			}
		}

		/**
		 * Gestion des dépassements
		 */
		public function depassements(){
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}

			if (!empty ($this->request->data)) {
				//récupération des orientables et de leurs orientations
				$orientations = Cache::read('orientations');

				//récupération des types d'orientations
				$id_type_orient_pe = Configure::read('Typeorient.emploi_id');
				$id_type_orient_ss = Configure::read('Typeorient.service_social_id');
				$lib_type_orient_pe = $this->Typeorient->find('first', ['conditions' => ['Typeorient.id' => $id_type_orient_pe]])['Typeorient']['lib_type_orient'];
				$lib_type_orient_ss = $this->Typeorient->find('first', ['conditions' => ['Typeorient.id' => $id_type_orient_ss]])['Typeorient']['lib_type_orient'];

				$actions = $this->request->data;
				unset($actions['valider']);
				foreach($actions as $id_struc => $action){
					switch($action) {
						case 'ignorer':
							//on supprime les orientables du tableau
							$orientations = array_filter(
								$orientations,
								function($v) use ($id_struc){
									return $v['orientation']['structure_id'] != $id_struc;
								}
							);
							break;
						case 'orienter_pe':
							//on remplace le type d'orient et la structure dans la partie orientation
							foreach($orientations as $key => $orientation){
								if($orientation['orientation']['structure_id'] == $id_struc){
									$orientations[$key]['orientation']['structure_id'] = $orientation['orientation']['pe_proximite_id'];
									$orientations[$key]['orientation']['structure_ville'] = $orientation['orientation']['pe_proximite_ville'];
									$orientations[$key]['orientation']['structure_libelle'] = $orientation['orientation']['pe_proximite_libelle'];
									$orientations[$key]['orientation']['type_orient_enfant_id'] = $id_type_orient_pe;
									$orientations[$key]['orientation']['lib_type_orient'] = $lib_type_orient_pe;
								}
							}
							break;
						case 'orienter_ss':
							//on remplace le type d'orient et la strucure dans la partie orientation
							foreach($orientations as $key => $orientation){
								if($orientation['orientation']['structure_id'] == $id_struc){
									$orientations[$key]['orientation']['structure_id'] = $orientation['orientation']['ss_proximite_id'];
									$orientations[$key]['orientation']['structure_ville'] = $orientation['orientation']['ss_proximite_ville'];
									$orientations[$key]['orientation']['structure_libelle'] = $orientation['orientation']['ss_proximite_libelle'];
									$orientations[$key]['orientation']['type_orient_enfant_id'] = $id_type_orient_ss;
									$orientations[$key]['orientation']['lib_type_orient'] = $lib_type_orient_ss;
								}
							}
							break;
					}
				}

				//on enregistre les modifications d'orientations
				Cache::write('orientations_apres_arbitrage', $orientations);

				//on affiche les résultats
				$this->redirect(['action' => 'affichageSimulationAlgo']);
			}

			$depassements = Cache::read('depassements');
			$options_actions = [
				'orienter_struc' =>__m('orienter_struc'),
				'orienter_pe' => __m('orienter_pe'),
				'orienter_ss' => __m('orienter_ss'),
				'ignorer' => __m('ignorer'),
			];

			$this->set(compact('depassements', 'options_actions'));

		}

		/**
		 * Résultats après la simulation de l'algo
		 */
		public function affichageSimulationAlgo(){
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}
			$orientables = Cache::read('orientables');
			//si orientations_apres_arbitrages existe on récupère ça au lieu de orientations
			if(Cache::read('orientations_apres_arbitrage') !== false){
				$orientations = Cache::read('orientations_apres_arbitrage');
			} else {
				$orientations = Cache::read('orientations');
			}

			if(empty($orientations)){
				$this->set('aucunOrientable', true);

			} else {
				$orientations = array_values($orientations);
				//on intègre les orientations aux données des orientables
				foreach($orientables as $key => $orientable){
					$key_orient = array_search($orientable['Personne']['id'], array_column(array_column($orientations, 0), 'id_personne'));
					if($key_orient !== false){
						$orientables[$key]['Propositionorientation'] = $orientations[$key_orient]['orientation'];
					} else {
						unset($orientables[$key]);
					}
				}

				$stats = $this->_calculsGraphiquesOrientables($orientables, true);

				Cache::write('orientations_reformartees', $orientables);
			}
			$this->set(compact('orientables', 'stats'));

		}

		/**
		 * Enregistrement des nouvelles orientations
		 */
		public function validerOrientations(){
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}
			$orientations = Cache::read('orientations_reformartees');

			$date = new DateTime();
			$datejour = $date->format('Y-m-d');

			$user_id = $this->Session->read('Auth.User.id');
			//on enregistre toutes les orientations
			$data = [];
			$delete_ids = [];
			foreach($orientations as $orientation){
				$data[] = [
					'personne_id' => $orientation['Personne']['id'],
					'typeorient_id' => $orientation['Propositionorientation']['type_orient_enfant_id'],
					'structurereferente_id' => $orientation['Propositionorientation']['structure_id'],
					'date_valid' => $datejour,
					'date_propo' => $datejour,
					'statut_orient' => 'Orienté',
					'origine' => 'cohorte',
				];

				$delete_ids[] = $orientation['Orientstruct']['id'];
			}

			$success = $this->Orientstruct->saveAll($data, ['validate' => false]);

			//on supprime les orientations non orienté (sauf si on est en mode debug)
			if(Configure::read('debug') != 2){
				$success = $success && $this->Orientstruct->deleteAll(['Orientstruct.id IN' => $delete_ids]);
			}

			$nb_orientations = count($data);

			$this->set(compact('nb_orientations', 'success'));
		}

		/**
		 * Calcule les orientations pour chaque orientable
		 */
		private function _calculToutesOrientations(){
			//On active le cache si en mode debug, en mode production il est activé par défaut
			if(Configure::read('debug') == 2){
				Configure::write('Cache.disable', false );
			}
			$orientables = Cache::read('orientables');


			//on récupère les réponses à tous les critères pour tous les orientables
			$orientables = $this->_getCriteresParOrientable($orientables);


			$criteres = $this->Criterealgorithmeorientation->find('all', ['order' => 'ordre ASC']);
			$structures = $this->StructurereferenteTypeorientZonegeographique->find(
				'all'
			);
			$zonesgeo = $this->Zonegeographique->find(
				'list',
				[
					'fields' => ['id', 'codeinsee'],
					'order' => 'libelle ASC'
				]
			);
			$id_type_orient_pe = Configure::read('Typeorient.emploi_id');
			$id_type_orient_ss = Configure::read('Typeorient.service_social_id');

			$typesorient = $this->Typeorient->listTypeEnfant();

			//Calcul de l'orientation
			foreach($orientables as $key => $orientable){
				$orientables[$key]['orientation'] = $this->_calculOrientation($orientable, $criteres, $structures, $zonesgeo, $id_type_orient_pe, $id_type_orient_ss, $typesorient);
			}

			Cache::write('orientations', $orientables);
		}

		/**
		 * Calcule l'orientation d'un orientable passé en paramètres
		 */
		private function _calculOrientation($orientable, $criteres, $structures, $zonesgeo, $id_type_orient_pe, $id_type_orient_ss, $typesorient){
			$index_critere = 0;
			$trouve = false;
			while($criteres[$index_critere]['Criterealgorithmeorientation']['code'] != 'FINAL' && !$trouve){
				//on vérifie si le brsa rentre dans le critère et si une structure référente est associée à sa ville
				if($orientable[0][strtolower($criteres[$index_critere]['Criterealgorithmeorientation']['code'])]
				&& !empty(array_filter(
					$structures,
					function($s) use ($criteres, $index_critere, $zonesgeo, $orientable) {
						 return $s['StructurereferenteTypeorientZonegeographique']['typeorient_id'] == $criteres[$index_critere]['Criterealgorithmeorientation']['type_orient_enfant_id']
						 && $zonesgeo[$s['StructurereferenteTypeorientZonegeographique']['zonegeographique_']] == $orientable[0]['numcom'];
					}
					))
				){
					$trouve = true;
				} else {
					$index_critere ++;
				}
			}

			$structure = array_filter(
				$structures,
				function($s) use ($criteres, $index_critere, $zonesgeo, $orientable) {
					 return $s['StructurereferenteTypeorientZonegeographique']['typeorient_id'] == $criteres[$index_critere]['Criterealgorithmeorientation']['type_orient_enfant_id']
					 && $zonesgeo[$s['StructurereferenteTypeorientZonegeographique']['zonegeographique_']] == $orientable[0]['numcom'];
				}
			);
			$poleemploi = array_filter(
				$structures,
				function($s) use ($zonesgeo, $orientable, $id_type_orient_pe) {
					 return $s['StructurereferenteTypeorientZonegeographique']['typeorient_id'] == $id_type_orient_pe
					 && $zonesgeo[$s['StructurereferenteTypeorientZonegeographique']['zonegeographique_']] == $orientable[0]['numcom'];
				}
			);
			$servicesocial = array_filter(
				$structures,
				function($s) use ($zonesgeo, $orientable, $id_type_orient_ss) {
					 return $s['StructurereferenteTypeorientZonegeographique']['typeorient_id'] == $id_type_orient_ss
					 && $zonesgeo[$s['StructurereferenteTypeorientZonegeographique']['zonegeographique_']] == $orientable[0]['numcom'];
				}
			);
			$structure = array_values($structure);
			$poleemploi = array_values($poleemploi);
			$servicesocial = array_values($servicesocial);
			$orientation['critere_id'] = $criteres[$index_critere]['Criterealgorithmeorientation']['id'];
			$orientation['type_orient_enfant_id'] = $structure[0]['Structurereferente']['typeorient_id'];
			$orientation['lib_type_orient'] = $typesorient[$structure[0]['Structurereferente']['typeorient_id']];
			$orientation['structure_id'] = $structure[0]['Structurereferente']['id'];
			$orientation['structure_libelle'] = $structure[0]['Structurereferente']['lib_struc'];
			$orientation['structure_ville'] = $structure[0]['Structurereferente']['ville'];
			$orientation['pe_proximite_id'] = $poleemploi[0]['Structurereferente']['id'];
			$orientation['pe_proximite_libelle'] = $poleemploi[0]['Structurereferente']['lib_struc'];
			$orientation['pe_proximite_ville'] = $poleemploi[0]['Structurereferente']['ville'];
			$orientation['ss_proximite_id'] = $servicesocial[0]['Structurereferente']['id'];
			$orientation['ss_proximite_libelle'] = $servicesocial[0]['Structurereferente']['lib_struc'];
			$orientation['ss_proximite_ville'] = $servicesocial[0]['Structurereferente']['ville'];

			return $orientation;


		}

		/**
		 * Calcule les données pour les graphiques
		 */
		private function _calculsGraphiquesOrientables($resultats, $orientation = false){
			$total = count($resultats);
			$villes = $this->Zonegeographique->find(
				'list',
				 [
					'fields' => ['codeinsee', 'libelle'],
					'conditions' => ['codeinsee like' => '93%'],
					'order' => 'libelle ASC'
				]
			);
			$villes_value = array_count_values(
				array_column(
					array_column(
						$resultats,
						'Adresse'
					),
					'numcom'
				)
			);
			$stats['parcours']['value'] = array_count_values(
				array_column(
					array_column(
						$resultats,
						'Propositionorientation'
					),
					'lib_type_orient'
				)
			);
			$stats['parcours']['value']['Total'] = array_sum($stats['parcours']['value']);
			$stats['parcours']['pourcentage'] = array_map(
				function($v) use ($total) {
					return round($v/$total*100, 2).'%';
				},
				$stats['parcours']['value']
			);
			$stats['parcours']['pourcentage']['Total'] = round($stats['parcours']['value']['Total']/$total*100, 2).'%';


			if($orientation){
				foreach($villes as $num => $nom){
					foreach($stats['parcours']['value'] as $nom_parcours => $valeur){
						$stats['villes']['value'][$nom][$nom_parcours] = count(
							array_filter(
								$resultats,
								function($v) use ($num, $nom_parcours) {
										return $v['Propositionorientation']['lib_type_orient'] == $nom_parcours
										&& $v['Adresse']['numcom'] == $num ;
								}
							)
						);
					}
					$stats['villes']['value'][$nom]['Total'] = count(
						array_filter(
							$resultats,
							function($v) use ($num, $nom_parcours) {
									return $v['Adresse']['numcom'] == $num ;
							}
						)
					);
				}

				$stats['villes']['pourcentage'] = array_map(
					function($v) use ($total) {
						$v = array_map(
							function($v2) use ($total) {
								return round($v2/$total*100, 2).'%';
							},
							$v);
						return $v;
					},
					$stats['villes']['value']
				);



			} else {

				$tranches_ages = Configure::read('Module.Algorithmeorientation.Parametragegraphiques')['tranches_ages'];

				$stats['age']['value']['< '.$tranches_ages[0]] = count(
					array_filter(
						array_column(
							array_column(
								$resultats, 'Personne'
							),
							'age'
						),
						function($v) use ($tranches_ages) {return $tranches_ages[0] > $v; }
					)
				);
				$stats['age']['pourcentage']['< '.$tranches_ages[0]] = round($stats['age']['value']['< '.$tranches_ages[0]]/$total*100, 2).'%';
				$index_age = 1;
				while($index_age < count($tranches_ages)){
					$stats['age']['value'][$tranches_ages[$index_age-1].' - '.$tranches_ages[$index_age]] = count(
						array_filter(
							array_column(
								array_column(
									$resultats,
									'Personne'
								),
								'age'
							),
							function($v) use ($tranches_ages, $index_age) {
								return $tranches_ages[$index_age-1] <= $v && $tranches_ages[$index_age] > $v;
							}
						)
					);
					$stats['age']['pourcentage'][$tranches_ages[$index_age-1].' - '.$tranches_ages[$index_age]] = round(
						$stats['age']['value'][$tranches_ages[$index_age-1].' - '.$tranches_ages[$index_age]]/$total*100,
						2
					).'%';
					$index_age ++;
				}
				$stats['age']['value']['>= '.$tranches_ages[count($tranches_ages)-1]] = count(
					array_filter(
						array_column(
							array_column(
								$resultats,
								'Personne'
							),
							'age'
						),
						function($v) use ($tranches_ages) {
							return $tranches_ages[count($tranches_ages)-1] <= $v;
						}
					)
				);
				$stats['age']['pourcentage']['>= '.$tranches_ages[count($tranches_ages)-1]] = round($stats['age']['value']['>= '.$tranches_ages[count($tranches_ages)-1]]/$total*100, 2).'%';

				$sexe = array_count_values(
					array_column(
						array_column(
							$resultats,
							'Personne'
						),
						'sexe'
					)
				);
				foreach($sexe as $key => $s){
					$stats['sexe']['value'][__m('sexe.'.$key)] = $s;
				}
				$stats['sexe']['pourcentage'] = array_map(
					function($v) use ($total) {
						return round($v/$total*100, 2).'%';
					},
					$stats['sexe']['value']
				);


				$liste_ept = $this->Communautesr->find('list');
				$liste_ept[] = 'Indéfini';
				$sommes_ept = array_map(function ($v) {return 0;}, $liste_ept);

				foreach($villes as $num => $nom){
					if(isset($villes_value[$num])){
						$stats['ville']['value'][$nom] = $villes_value[$num];
						//on ajoute à l'ept relié à la ville
						$ept = $this->CommunautesrStructurereferente->find(
							'first',
							['conditions' => [
									'Structurereferente.code_insee' => $num
								]
							]
						);
						if($ept != false){
							$sommes_ept[$ept['CommunautesrStructurereferente']['communautesr_id']] += $villes_value[$num];
						} else {
							$sommes_ept[count($liste_ept)] += $villes_value[$num];
						}
					} else {
						$stats['ville']['value'][$nom] = 0;
					}
				}
				$stats['ville']['pourcentage'] = array_map(
					function($v) use ($total) {
						return round($v/$total*100, 2).'%';
					},
					$stats['ville']['value']
				);

				if($sommes_ept[count($liste_ept)] == 0){
					unset($sommes_ept[count($liste_ept)]);
					unset($liste_ept[count($liste_ept)]);
				}
				foreach($liste_ept as $key => $nom){
					$stats['ept']['value'][$nom] = $sommes_ept[$key];
				}
				$stats['ept']['pourcentage'] = array_map(function($v) use ($total) {return round($v/$total*100, 2).'%';}, $stats['ept']['value']);

			}

			$role = array_count_values(
				array_column(
					array_column(
						$resultats,
						'Prestation'
					),
					'rolepers'
				)
			);
			foreach($role as $key => $r){
				$stats['role']['value'][__m($key)] = $r;
			}
			$stats['role']['pourcentage'] = array_map(
				function($v) use ($total) {
					return round($v/$total*100, 2).'%';
				},
				$stats['role']['value']
			);


			$dsp['vide'] = array_count_values(
				array_replace(
					array_column(
						array_column(
							$resultats,
							'Dsp'
						),
						'id'
					),
					array_fill_keys(
						array_keys(
							array_column(
								array_column(
									$resultats,
									'Dsp'
								),
								'id'
							),
							null
						),
						'vide'
					)
				)
			)['vide'];
			$dsp['remplie'] = count(
				array_column(
					array_column(
						$resultats,
						'Dsp'
					),
					'id'
				)
			)-$dsp['vide'];
			foreach($dsp as $key => $d){
				$stats['dsp']['value'][__m($key)] = $d;
			}
			$stats['dsp']['pourcentage'] = array_map(
				function($v) use ($total) {
					return round($v/$total*100, 2).'%';
				},
				$stats['dsp']['value']
			);


			return $stats;
		}

		/**
		 * Renvoie pour chaque orientable si il rentre dans chacun des critères ou non
		 */
		private function _getCriteresParOrientable($orientables){

			$liste_ids = array_column(array_column($orientables, 'Personne'), 'id');
			$liste_ids = implode(',', $liste_ids);


			$nb_enfants = Configure::read('Module.AlgorithmeOrientation.seuils.nbenfants')[$this->Criterealgorithmeorientation->findByCode('LOGEMENT_URGENCE')['Criterealgorithmeorientation']['nb_enfants']];

			$nb_mois = Configure::read('Module.AlgorithmeOrientation.seuils.nbmois')[$this->Criterealgorithmeorientation->findByCode('INSCRIT_PE_DERNIERS_MOIS')['Criterealgorithmeorientation']['nb_mois']];

			$date = new DateTime();
			$datefin = $date->format('Y-m-d');
			$datedebut = $date->sub(new DateInterval('P'.$nb_mois.'M'))->format('Y-m-d');

			$critere_jeune = $this->Criterealgorithmeorientation->findByCode('JEUNE')['Criterealgorithmeorientation'];
			$age_min_jeune = Configure::read('Module.AlgorithmeOrientation.seuils.agemin')[$critere_jeune['age_min']];
			$age_max_jeune = Configure::read('Module.AlgorithmeOrientation.seuils.agemax')[$critere_jeune['age_max']];

			$critere_senior = $this->Criterealgorithmeorientation->findByCode('SENIOR')['Criterealgorithmeorientation'];
			$age_min_senior = Configure::read('Module.AlgorithmeOrientation.seuils.agemin')[$critere_senior['age_min']];
			$age_max_senior = Configure::read('Module.AlgorithmeOrientation.seuils.agemax')[$critere_senior['age_max']];

			$variablesRequete = Configure::read('Module.Algorithmeorientation.Parametragerequete');

			$sql = "
			--Dernière version de révision des DSP
			with DernierDspRev as (SELECT id, personne_id, rank() over(partition by personne_id order by modified desc) as rang FROM dsps_revs where personne_id in ({$liste_ids}) order by personne_id) ,
			-- DSP
			DSP as (select
				p.id as personne_id,
				(case when dr.id is not null and dr.hispro = '1904' then true when dr.id is null and d.hispro = '1904' then true else false end) as JAMAIS_TRAVAILLE,
				( CASE WHEN dr.id IS NOT NULL THEN (dr.topengdemarechemploi = '1' and dr.topengdemarechemploi is not null) ELSE (d.topengdemarechemploi = '1' and d.topengdemarechemploi is not null) END ) AS ENGAGEMENT_RAPIDE_EMPLOI,
				bool_or(det.id is not null) or bool_or(detr.id is not null) as DIFFICULTES_SOC,
				(case when dr.id is not null then (array_agg(distinct detr.difsoc::text) = array['0404']) else (array_agg(distinct det.difsoc::text) = array['0404']) end) as DIFFICULTES_FRANCAIS,
				(case when dr.id is not null then (dr.natlog is not null and dr.natlog in ({$variablesRequete['natlog']})) else (d.natlog is not null and d.natlog in ({$variablesRequete['natlog']})) end) as type_logement_urgence
			FROM personnes p
				left join DernierDspRev ddr on ddr.personne_id = p.id and ddr.rang = 1
				LEFT JOIN dsps d on p.id = d.personne_id
				left join detailsdifsocs det on det.dsp_id = d.id
				LEFT JOIN dsps_revs dr on ddr.id = dr.id
				left join detailsdifsocs_revs detr on detr.dsp_rev_id = dr.id
			where p.id in ({$liste_ids})
			group by
				ENGAGEMENT_RAPIDE_EMPLOI,
				dr.id,
				p.id,
				d.hispro,
				d.natlog
			),
			DernierHistoriquePE as (select id, etat, informationpe_id , rank() over(partition by informationpe_id order by date desc) as rang from historiqueetatspe),
			HistoriquePEDerniersMois as (select informationpe_id, (historiqueetatspe.id is not null and array_agg(historiqueetatspe.etat::text) over (partition by historiqueetatspe.informationpe_id) @> array['inscription']) as INSCRIT_PE_DERNIERS_MOIS from historiqueetatspe where historiqueetatspe.\"date\" BETWEEN '{$datedebut}' AND '{$datefin}' group by historiqueetatspe.informationpe_id, historiqueetatspe.id )
			select
				distinct p.id as id_personne,
				f.id,
				p.nom,
				p.prenom,
				da.numvoie,
				da.nomvoie,
				da.codepos,
				da.numcom,
				p2.id is not null as ASSO_REF,
				p2.id as permanence_id,
				--modifier les bornes en fonction des variables de configuration
				extract (year from AGE(p.dtnai) ) between {$age_min_jeune} and {$age_max_jeune} as JEUNE,
				extract (year from AGE(p.dtnai) ) between {$age_min_senior} and {$age_max_senior} as SENIOR,
				dsp.ENGAGEMENT_RAPIDE_EMPLOI,
				dsp.DIFFICULTES_SOC,
				dsp.DIFFICULTES_FRANCAIS,
				dsp.JAMAIS_TRAVAILLE,
				(dsp.type_logement_urgence and d2.nbenfautcha >= {$nb_enfants} and d2.nbenfautcha is not null) as LOGEMENT_URGENCE,
				(hpe.INSCRIT_PE_DERNIERS_MOIS is not null and hpe.INSCRIT_PE_DERNIERS_MOIS) as INSCRIT_PE_DERNIERS_MOIS,
				(dernierhistoriquepe.etat = 'inscription' and dernierhistoriquepe.etat is not null) as INSCRIT_PE,
				(f.sitfam in ({$variablesRequete['sitfam']}) and d2.nbenfautcha >= 3 and d2.nbenfautcha is not null) as FOYER_MONOPARENTAL,
				d3.natpf in ({$variablesRequete['natpf']}) or d3.sousnatpf in ({$variablesRequete['sousnatpf']}) as RSA_MAJORE,
				(dda.hasotherdossier is not null and dda.hasotherdossier) as SECONDE_INSCRIPTION_RSA
			from
				personnes p
			join foyers f on f.id = p.foyer_id
			join detailsdroitsrsa d2 on d2.dossier_id  = f.dossier_id
			join detailscalculsdroitsrsa d3 on d3.detaildroitrsa_id  = d2.id
			join adressesfoyers af on af.foyer_id = f.id and af.rgadr = '01'
			join adresses da on da.id = af.adresse_id
			left join permanences p2 on
				p2.numvoie = da.numvoie
				and p2.typevoie = da.libtypevoie
				and p2.nomvoie = da.nomvoie
				and p2.codepos = da.codepos
				and p2.ville = da.nomcom
			left join DSP on dsp.personne_id = p.id
			left join informationspe i on (
				(i.nir IS NOT null and SUBSTRING( i.nir FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH ' ' FROM p.nir ) FROM 1 FOR 13 ) and i.dtnai = p.dtnai)
				or (
					p.nom IS NOT null and p.prenom IS NOT NULL and p.dtnai IS NOT null and TRIM( BOTH ' ' FROM p.nom ) <> ''
					and TRIM( BOTH ' ' FROM p.prenom ) <> '' and
					TRIM( BOTH ' ' FROM i.nom ) = TRIM( BOTH ' ' FROM p.nom ) and
					TRIM( BOTH ' ' FROM i.prenom ) = TRIM( BOTH ' ' FROM p.prenom ) and
					i.dtnai = p.dtnai
				)
			)
			left join dernierhistoriquepe on dernierhistoriquepe.informationpe_id = i.id and dernierhistoriquepe.rang = 1
			LEFT JOIN HistoriquePEDerniersMois hpe ON hpe.informationpe_id = i.id
			left join derniersdossiersallocataires dda on dda.personne_id = p.id
			where p.id in ({$liste_ids});
			";

			return $this->Personne->query($sql);
		}


		/**
		 * Recherche des nouveaux orientés
		 */
		public function search(){
			$Recherches = $this->Components->load( 'WebrsaRechercheAlgorithmeorientation' );
			$Recherches->search(['modelName' => 'Orientstruct']);
		}

		/**
		 * Recherche des nouveaux orientés
		 */
		public function exportcsv_recherche(){
			$Recherches = $this->Components->load( 'WebrsaRechercheAlgorithmeorientation' );
			$Recherches->exportcsv(['modelName' => 'Orientstruct']);
		}

	}

?>