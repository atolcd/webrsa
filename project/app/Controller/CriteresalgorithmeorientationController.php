<?php
	/**
	 * Code source de la classe CriteresalgorithmeorientationController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );
	/**
	 * La classe CriteresalgorithmeorientationController s'occupe de la gestion des criteres de l'algorithme d'orientation
	 *
	 * @package app.Controller
	 */
	class CriteresalgorithmeorientationController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Criteresalgorithmeorientation';

         /**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Criterealgorithmeorientation', 'Typeorient', 'Valeurtag' );


		/**
		 * Liste des criteres de l'algorithme d'orientation
		 */
		public function index() {

			$results = $this->Criterealgorithmeorientation->find('all', ['order' => 'Criterealgorithmeorientation.actif DESC, ordre ASC']);
			//Pour chaque critère, on regarde s'il il utilise des variables, et si oui on les ajoute à la traduction
			foreach ($results as $key => $result){
				if($result['Criterealgorithmeorientation']['nb_mois'] != 'false') {
					$results[$key]['Criterealgorithmeorientation']['libelle'] = sprintf($result['Criterealgorithmeorientation']['libelle'], Configure::read('Module.AlgorithmeOrientation.seuils.nbmois')[$result['Criterealgorithmeorientation']['nb_mois']]);
				} else if ($result['Criterealgorithmeorientation']['nb_enfants'] != 'false') {
					$results[$key]['Criterealgorithmeorientation']['libelle'] = sprintf($result['Criterealgorithmeorientation']['libelle'], Configure::read('Module.AlgorithmeOrientation.seuils.nbenfants')[$result['Criterealgorithmeorientation']['nb_enfants']]);
				} else if ($result['Criterealgorithmeorientation']['age_min'] != 'false' && $result['Criterealgorithmeorientation']['age_max'] != 'false') {
					$results[$key]['Criterealgorithmeorientation']['libelle'] = sprintf($result['Criterealgorithmeorientation']['libelle'], Configure::read('Module.AlgorithmeOrientation.seuils.agemin')[$result['Criterealgorithmeorientation']['age_min']], Configure::read('Module.AlgorithmeOrientation.seuils.agemax')[$result['Criterealgorithmeorientation']['age_max']]);
				}
			}

			//On récupère les critères actifs pour gérer la modification de l'ordre
			$resultsactifs = $this->Criterealgorithmeorientation->find('all', ['conditions' => ['Criterealgorithmeorientation.actif' => true],'order' => 'Criterealgorithmeorientation.actif DESC, ordre ASC']);
			$premier_id = $resultsactifs[0]['Criterealgorithmeorientation']['id'];;
			$dernier_id = $resultsactifs[count($resultsactifs)-1]['Criterealgorithmeorientation']['id'];

			$this->set( compact('premier_id', 'dernier_id', 'results' ) );
		}

		/**
		 * Modification d'un critère de l'algorithme d'orientation
		 * @param integer id du critère
		 */
		public function edit($id = null){
			if( false === empty( $this->request->data ) ) {
				// Retour à la liste en cas d'annulation
				if( isset( $this->request->data['Cancel'] ) ) {
					$this->redirect( '/Criteresalgorithmeorientation/index' );
				}
				//on enregistre
				$data['Criterealgorithmeorientation'] = $this->request->data['Criterealgorithmeorientation'];
				$data['Criterealgorithmeorientation']['type_orient_parent_id'] = $this->Typeorient->findById($this->request->data['Criterealgorithmeorientation']['type_orient_enfant_id'])['Typeorient']['parentid'];
				$data['Criterealgorithmeorientation']['id'] = $id;
				$this->Criterealgorithmeorientation->begin();
				$success = $this->Criterealgorithmeorientation->save($data, array( 'atomic' => false ));
				if($success){
					$this->Criterealgorithmeorientation->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( '/Criteresalgorithmeorientation/index' );
				} else {
					$this->Criterealgorithmeorientation->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}

			}

			//options pour les select
			$options['typesorients'] = $this->Typeorient->listOptions();
			$options['tags'] = $this->Valeurtag->find('list');
			$options = array_merge(
				$options,
				Configure::read('Module.AlgorithmeOrientation.seuils')
			);

			$this->request->data = $this->Criterealgorithmeorientation->findById($id);

			$this->set( compact( 'options') );
		}

		/**
		 * Modifie l'ordre d'un critère en le faisant remonter d'une place dans la liste
		 * @param integer id du critère
		 */
		public function monter($id){
			$retour = $this->Criterealgorithmeorientation->getLePlusProche($id, 'monter');
			$this->inverserOrdre($id, $retour['idAutre'], $retour['ordre'], $retour['ordreAutre']);
			$this->redirect( '/Criteresalgorithmeorientation/index/');
		}

		/**
		 * Modifie l'ordre d'un critère en le faisant descendre d'une place dans la liste
		 * @param integer id du critère
		 */
		public function descendre($id){
			$retour = $this->Criterealgorithmeorientation->getLePlusProche($id, 'descendre');
			$this->inverserOrdre($id, $retour['idAutre'], $retour['ordre'], $retour['ordreAutre']);
			$this->redirect( '/Criteresalgorithmeorientation/index/');

		}

		/**
		 * Inverse l'ordre de 2 critères passés en paramètres
		 * @param integer id du premier critère
		 * @param integer id du deuxième critère
		 * @param integer ordre actuel du premier critère
		 * @param integer ordre actuel du deuxième critère
		 */
		private function inverserOrdre($id1, $id2, $ordre1, $ordre2){
			$this->Criterealgorithmeorientation->begin();
			$data = ['id' => $id1, 'ordre' => $ordre2];
			$data2 = ['id' => $id2, 'ordre' => $ordre1];
			$this->Criterealgorithmeorientation->saveAll( $data, array( 'atomic' => false ) );
			$this->Criterealgorithmeorientation->clear();
			$this->Criterealgorithmeorientation->saveAll( $data2, array( 'atomic' => false ) );
			$this->Criterealgorithmeorientation->commit();
		}

	}
?>