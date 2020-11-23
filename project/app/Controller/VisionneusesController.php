<?php
	/**
     * Code source de la classe VisionneusesController.
	 * Fait par le CG93
     *
     * PHP 7.2
     *
	 * @author Harry ZARKA <hzarka@cg93.fr>, 2010.
     * @package app.Controller
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe VisionneusesController ...
	 *
	 * @package app.Controller
	 */
	class VisionneusesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Visionneuses';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Csv',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Locale',
			'Xform',
			'Search.SearchForm',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Visionneuse',
			'RejetHistorique',
			'Talendsynt'
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(

		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'calculrejetes',
			'view'
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
			'calculrejetes' => 'read',
			'view' => 'read',
		);

		public $paginate = array(
			'limit'=>10,
			'order'=>'Visionneuse.dtdeb DESC'
		);

		public function index() {
			$options = array();
			$options['Visionneuse']['flux'] = array(
				'INSTRUCTION' => 'INSTRUCTION',
				'BENEFICIAIRE' => 'BENEFICIAIRE',
				'FINANCIER' => 'FINANCIER',
			);

			$isFlux = true;
			if(!empty($this->request->data)) {
				$search = $this->request->data['Search'];
				$query = array();
				// Recherche par flux
				if(isset($search['Visionneuse']['searchFlux']) && $search['Visionneuse']['searchFlux'] == 1) {
					if($search['Visionneuse']['flux'] != '') {
						$query['conditions'][] = array('Visionneuse.flux' => $search['Visionneuse']['flux']);
					}

					if($search['Visionneuse']['dtdeb'] == 1) {
						$query['conditions'][] = $this->Visionneuse->conditionsDates($query['conditions'], $search, 'Visionneuse.dtdeb');
					}
					if( isset($query['conditions']) ) {
						$visionneuses = $this->paginate('Visionneuse', $query['conditions']);
					} else {
						$visionneuses = $this->paginate('Visionneuse');
					}
				}
				// Recherche par personne
				else if( isset($search['Visionneuse']['searchPersonne']) && $search['Visionneuse']['searchPersonne'] == 1 ) {
					$isFlux = false;
					$query = array(
						'fields' => array_merge(
							$this->Talendsynt->fields(),
							array(
								'Visionneuse.id',
								'Visionneuse.flux',
								'Visionneuse.nomfic'
							)
						),
						'conditions' => array(),
						'joins' => array(
							array(
								'table' => 'visionneuses',
								'alias' => 'Visionneuse',
								'type' => 'INNER',
								'conditions' => array(
									'Visionneuse.identificationflux_id = Talendsynt.identificationflux_id',
								)
							)
						)
					);

					if($search['Visionneuse']['nom'] != '') {
						$query['conditions'][] = array('Talendsynt.nom' => $search['Visionneuse']['nom']);
					}

					if($search['Visionneuse']['prenom'] != '') {
						$query['conditions'][] = array('Talendsynt.prenom' => $search['Visionneuse']['prenom']);
					}

					if(!empty($search['Visionneuse']['dtnai'])) {
						$search['Talendsynt']['dtnai'] = $search['Visionneuse']['dtnai'];
						$query['conditions'] = $this->Talendsynt->conditionsDate($query['conditions'], $search, 'Talendsynt.dtnai');
					}

					if($search['Visionneuse']['nir'] != '') {
						$query['conditions'][] = array('Talendsynt.nir' => $search['Visionneuse']['nir']);
					}

					$visionneuses = $this->Talendsynt->find('all', $query);
				}
			} else {
				$visionneuses = $this->paginate('Visionneuse');
			}
			// Calcul de la durée et du nombre de dossier présent
			if($isFlux) {
				foreach($visionneuses as $key => $visionneuse) {
					$duree = date("H:i:s", strtotime( $visionneuse['Visionneuse']['dtfin'] ) - strtotime( $visionneuse['Visionneuse']['dtdeb'] ));
					$visionneuses[$key]['Visionneuse']['duree'] = $duree;

					$dossier = $visionneuse['Visionneuse']['nbrejete'] + $visionneuse['Visionneuse']['nbinser'] + $visionneuse['Visionneuse']['nbmaj'];
					$visionneuses[$key]['Visionneuse']['dossier'] = $dossier;
				}
			}

			$this->set(compact('visionneuses', 'options', 'isFlux'));
		}

		/**
		 * Permet de voir le détail des bénéficiaires insérés, modifiés ou
		 * rejetés selon l'identification du flux
		 * @param int $identificationflux_id
		 */
		public function view($identificationflux_id) {
			$nomFlux = $this->Visionneuse->find('first', array(
				'fields' => array('nomfic'),
				'conditions' => array('identificationflux_id' => $identificationflux_id)
			));
			$nomFlux = $nomFlux['Visionneuse']['nomfic'];
			$query = array();
			$conditions = array(
					'Talendsynt.identificationflux_id' => $identificationflux_id
			);

			if(	isset($this->request->data['Search']) &&
				$this->request->data['Search']['Talensynt_choice'] == 1 &&
				$this->request->data['Search']['Talensynt'] != ''
			) {
				$statutSearched = $this->request->data['Search']['Talensynt'];
				if(in_array('RIEN', $statutSearched)) {
					$conditions[] = array(
						'Talendsynt.cree' => false,
						'Talendsynt.maj' => false,
						'Talendsynt.rejet' => false
					);
				} else {
					$conditions['AND'] = array('OR' => array());
					if(in_array('INS', $statutSearched)) {
						$conditions['AND']['OR']['Talendsynt.cree'] = true;
					}

					if(in_array('MAJ', $statutSearched)) {
						$conditions['AND']['OR']['Talendsynt.maj'] = true;
					}

					if(in_array('REJ', $statutSearched)) {
						$conditions['AND']['OR']['Talendsynt.rejet'] = true;
					}
				}
			}
			$query['conditions'] = $conditions;

			$results = $this->Talendsynt->find('all', $query);
			$options = array(
				'statut' => array(
					'INS' => 'Insérés',
					'MAJ' => 'Modifiés',
					'REJ' =>'Rejetés',
					'RIEN' => 'Ni inséré, ni modifié ni rejeté'
				)
			);

			$this->set( compact('results', 'options', 'identificationflux_id', 'nomFlux') );
		}
	}
?>