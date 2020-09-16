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

			if(!empty($this->request->data)) {
				$search = $this->request->data['Search'];
				$query = array();
				if($search['Visionneuse']['flux'] != '') {
					$query['conditions'][] = array('Visionneuse.flux' => $search['Visionneuse']['flux']);
				}

				if($search['Visionneuse']['dtdeb'] == 1) {
					$query['conditions'] = $this->Visionneuse->conditionsDates($query['conditions'], $search, 'Visionneuse.dtdeb');
				}
				$visionneuses = $this->paginate('Visionneuse', $query['conditions']);
			} else {
				$visionneuses = $this->paginate('Visionneuse');
			}
			// Calcul de la durée et du nombre de dossier présent
			foreach($visionneuses as $key => $visionneuse) {
				$duree = date("H:i:s", strtotime( $visionneuse['Visionneuse']['dtfin'] ) - strtotime( $visionneuse['Visionneuse']['dtdeb'] ));
				$visionneuses[$key]['Visionneuse']['duree'] = $duree;

				$dossier = $visionneuse['Visionneuse']['nbrejete'] + $visionneuse['Visionneuse']['nbinser'] + $visionneuse['Visionneuse']['nbmaj'];
				$visionneuses[$key]['Visionneuse']['dossier'] = $dossier;
			}

			$this->set(compact('visionneuses', 'options'));
		}

		/**
		 * Permet de voir le détail des bénéficiaires insérés, modifiés ou
		 * rejetés selon l'identification du flux
		 * @param int $identificationflux_id
		 */
		public function view($identificationflux_id) {
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

			$this->set( compact('results', 'options', 'identificationflux_id') );
		}
	}
?>