<?php
	/**
	 * Code source de la classe Rapportsechangesali.
	 *
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Rapportsechangesali ...
	 *
	 * @package app.Controller
	 */
	class RapportsechangesaliController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Rapportsechangesali';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			//'WebrsaAccesses'
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Paginator',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Xform',
			'Search.SearchForm'
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'ErreurEchangeALI',
			'PersonneEchangeALI',
			'RapportEchangeALI'
		);

		/**
		 * Pagination sur les <éléments> de la table.
		 */
		public function index() {

			$options['RapportEchangeALI']['flux'] = [
				'referentiel' => __m('referentiel'),
				'import' => __m('import'),
				'export' => __m('export')
			];

			$conditions = [];

			if(isset($this->request->data['Search'])){
				$data = $this->request->data['Search'];
				//Filtre par flux
				if(isset($data['RapportEchangeALI']['searchFlux']) && $data['RapportEchangeALI']['searchFlux']){
					//Flux
					if($data['RapportEchangeALI']['flux'] != ''){
						$conditions['type'] = $data['RapportEchangeALI']['flux'];
					}
					//Date
					if($data['RapportEchangeALI']['date']){
						$from = $data['RapportEchangeALI']['date_from']['year'].$data['RapportEchangeALI']['date_from']['month'].$data['RapportEchangeALI']['date_from']['day'];
						$to = $data['RapportEchangeALI']['date_to']['year'].$data['RapportEchangeALI']['date_to']['month'].$data['RapportEchangeALI']['date_to']['day'];
						//on est obligé de préciser les horaires sinon le dernier jour n'est pas inclus
						$conditions[] = "created BETWEEN '{$from} 00:00:00' AND '{$to} 23:59:59'";
					}
				}
			}

			if(
				!isset($data['RapportEchangeALI']['searchPersonne']) 
				|| (
					!$data['RapportEchangeALI']['nom']
					&& !$data['RapportEchangeALI']['prenom']
					&& !$data['RapportEchangeALI']['nir']
					&& (!$data['RapportEchangeALI']['dtnai']['day'] ||! $data['RapportEchangeALI']['dtnai']['month'] || !$data['RapportEchangeALI']['dtnai']['year'])
				)
			){
				$query =  array(
						'order' => array(
							'RapportEchangeALI.created DESC',
						),
						'contain' => ['Structurereferente', 'ErreurEchangeALI'],
						'limit' => 20,
						'conditions' => $conditions
					) ;
				$this->paginate = $query;
				$Rapports = $this->paginate( 'RapportEchangeALI' );

				foreach($Rapports as $key => $rapport){
					$Rapports[$key]['RapportEchangeALI']['code'] = $rapport['RapportEchangeALI']['type'];
					$Rapports[$key]['RapportEchangeALI']['type'] = __m($rapport['RapportEchangeALI']['type']);
					$rapport['RapportEchangeALI']['stock'] = $rapport['RapportEchangeALI']['stock'] == true ? 'stock' : 'diff';
					$Rapports[$key]['RapportEchangeALI']['periode'] = __m($rapport['RapportEchangeALI']['stock']);
					$rapport['RapportEchangeALI']['statut'] = count($rapport['ErreurEchangeALI']) > 0 ? 'nok' : 'ok';
					$Rapports[$key]['RapportEchangeALI']['statut'] = __m($rapport['RapportEchangeALI']['statut']);
					$Rapports[$key]['RapportEchangeALI']['nb_pers'] = $this->PersonneEchangeALI->find('count', ['conditions' => ['rapport_id' => $rapport['RapportEchangeALI']['id']]]);

				}

				$this->set( 'Rapports', $Rapports );

			}

			else if (isset($data['RapportEchangeALI']['searchPersonne']) && $data['RapportEchangeALI']['searchPersonne']){
				//On récupère la liste des rapports qui correspondent aux critères sur les personnes et on ajoute une condition
				$where = [];
				if($data['RapportEchangeALI']['nom']){
					$where[] = "p.nom ilike  '{$data['RapportEchangeALI']['nom']}'";
				}
				if($data['RapportEchangeALI']['prenom']){
					$where[] = "p.prenom ilike  '{$data['RapportEchangeALI']['prenom']}'";
				}
				if($data['RapportEchangeALI']['nir']){
					$where[] = "p.nir = '{$data['RapportEchangeALI']['nir']}'";
				}
				if($data['RapportEchangeALI']['dtnai']['day'] && $data['RapportEchangeALI']['dtnai']['month'] && $data['RapportEchangeALI']['dtnai']['year']){
					$dtnai = $data['RapportEchangeALI']['dtnai']['year'].'-'.$data['RapportEchangeALI']['dtnai']['month'].'-'.$data['RapportEchangeALI']['dtnai']['day'];
					$where[] = "p.dtnai = '{$dtnai}'";
				}

				$where = implode(' AND ', $where);
				$liste_ids = [];
				if(!empty($where)){
					$ids = $this->RapportEchangeALI->query(
						"
						select
						distinct p.id
						from administration.rapportsechangesali r
						left join administration.personnesechangesali pe on pe.rapport_id = r.id
						left join public.personnes p on p.id = pe.personne_id
						where $where
						"
					);

					foreach($ids as $id){
						$liste_ids[] = $id[0]['id'];
					}

					$liste_ids = '('.implode(',', $liste_ids).')';

					if(!empty($liste_ids)){
					$query =  array(
						'order' => array(
							'RapportEchangeALI.created DESC',
						),
						'limit' => 20,
						'conditions' => "personne_id in $liste_ids"
					) ;
					$this->paginate = $query;
					$personnes = $this->paginate( 'PersonneEchangeALI' );

					} else {
						$personnes = [];
					}
				}
				$this->set(compact('personnes'));
			}



			$this->set(compact('options'));

		}

		public function details($type, $rapport_id, $erreurs = false){
			$rapport = $this->RapportEchangeALI->find(
				'first',
				[
					'conditions' => [
						'RapportEchangeALI.id' => $rapport_id
					],
				]
			);

			$erreursglobales = $this->ErreurEchangeALI->query(
				"
				select code, string_agg(commentaire, ',') as commentaire
				from administration.erreursechangesali
				where bloc = 'global' and rapport_id = $rapport_id
				group by code
				"
			);

			if (in_array($type, ['import', 'export'])){
				if($erreurs == true){
					$personnes = $this->paginate(
						'PersonneEchangeALI',
						[
							'rapport_id' => $rapport_id,
							'OR' => [
								'referentparcours' => false,
								'rendezvous' => false,
								'dsp' => false,
								'cer' => false,
								'orient' => false,
								'd1' => false,
								'd2' => false,
								'b7' => false,
							]
						],
						['Personne.nom']
					);
				} else {
					$personnes = $this->paginate( 'PersonneEchangeALI', ['rapport_id' => $rapport_id], ['Personne.nom']);
				}
				$blocs = [
					'referentparcours' => 'referent',
					'rendezvous' => 'rdv',
					'dsp' => 'dsp',
					'cer' => 'cer',
					'orient' => 'orient',
					'd1' => 'd1',
					'd2' => 'd2',
					'b7' => 'b7'
				];
				foreach($personnes as $key => $personne){
					foreach($blocs as $colonne => $bloc){
						if($personne['PersonneEchangeALI'][$colonne] === false){
							$err = $this->ErreurEchangeALI->find(
								'first', [
									'conditions' => [
										'rapport_id' => $rapport_id,
										'personne_id' => $personne['Personne']['id'],
										'bloc' => $bloc
									]
								]
							)['ErreurEchangeALI']['code'];

							$personnes[$key]['Erreur'][$bloc] = __m($err);
						} else if($personne['PersonneEchangeALI'][$colonne] === null){
							$personnes[$key]['Erreur'][$bloc] = '-';
						} else {
							$personnes[$key]['Erreur'][$bloc] = 'Ok';
						}
					}


				}
				$nbPersonnes = $this->PersonneEchangeALI->find('count', ['conditions' => ['rapport_id' => $rapport_id]]);
				$nb_personnes_inconnues = $this->ErreurEchangeALI->find('count', ['conditions' => ['rapport_id' => $rapport_id, 'code' => 'personne_inconnue']]);
			}

			if($erreurs == true){
				$titre = sprintf(__d('rapportsechangesali', 'titre_detail_erreur'), $rapport['RapportEchangeALI']['nom_fichier']);
			} else {
				$titre = sprintf(__d('rapportsechangesali','titre_detail'), $rapport['RapportEchangeALI']['nom_fichier']);
			}

			$this->set(compact('rapport', 'personnes', 'nbPersonnes', 'erreursglobales', 'tab_erreurs', 'type', 'nb_personnes_inconnues', 'titre'));
		}
	}
?>
