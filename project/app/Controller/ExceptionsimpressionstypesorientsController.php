<?php
	/**
	 * Code source de la classe ExceptionsimpressionstypesorientsController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ExceptionsimpressionstypesorientsController
	 *
	 * @package app.Controller
	 */
	class ExceptionsimpressionstypesorientsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Exceptionsimpressionstypesorients';

		 /**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = [
			'Exceptionimpressiontypeorient',
			'Activite',
			'Orientstruct',
			'ExceptionimpressiontypeorientOrigine',
			'ExcepimprtypeorientZonegeo',
			'Structurereferente',
			'Zonegeographique'
		];

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		public function add($typeorient_id = null){
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		public function edit($id = null){
			if( $this->action == 'add' ) {
				$typeorient_id = $id;
				$dernierOrdre = $this->Exceptionimpressiontypeorient->findByTypeorientId($typeorient_id, null, ['ordre' =>  'desc']);
				$options['ordre'] = $dernierOrdre == [] ? 1 : $dernierOrdre['Exceptionimpressiontypeorient']['ordre'] + 1;
			}
			else if( $this->action == 'edit' ) {
				$exception = $this->Exceptionimpressiontypeorient->find('first', ['conditions' => ['Exceptionimpressiontypeorient.id' => $id]]);
				$typeorient_id = $exception['Exceptionimpressiontypeorient']['typeorient_id'];
				$options['ordre'] = $exception['Exceptionimpressiontypeorient']['ordre'];
				$exception['Exceptionimpressiontypeorient']['filtre_zone_geo'] = empty($exception['Zonegeographique']) ? false : true;
			}
			if( false === empty( $this->request->data ) ) {
				// Retour à la liste en cas d'annulation
				if( isset( $this->request->data['Cancel'] ) ) {
					$this->redirect( '/Typesorients/edit/'.$typeorient_id );
				}

				// Manipulation des origines récupérées
				$this->request->data = $this->_adaptTabOrigine($this->request->data);

				$data = $this->request->data;
				unset($data['ExceptionimpressiontypeorientOrigine']);
				unset($data['Exceptionimpressiontypeorient']['ExceptionimpressiontypeorientOrigineC']);
				unset($data['Exceptionimpressiontypeorient']['ExceptionimpressiontypeorientOrigineHC']);
				if($data['Exceptionimpressiontypeorient']['filtre_zone_geo'] == '0'){
					$data['Zonegeographique']['Zonegeographique'] = '';
				}
				$this->Exceptionimpressiontypeorient->id = $data['Exceptionimpressiontypeorient']['id'];
				$data['Zonegeographique']['excepimprtypeorient_id'] = $data['Exceptionimpressiontypeorient']['id'];
				$this->Exceptionimpressiontypeorient->begin();

				$success = $this->Exceptionimpressiontypeorient->saveAll( $data, array( 'atomic' => false ) );

				$id = $this->Exceptionimpressiontypeorient->id;

				$aucuneOrigine = true;
				//Sauvegarde des origines
				//Obligé de le faire à la main car il n'existe pas de table origin et cake ne sait pas faire le lien automatiquement
				foreach ($this->request->data['ExceptionimpressiontypeorientOrigine'] as $nom => $valeur){
					//On compare avec la liste de ce qui existe déjà - on supprime / on ajoute
					if($valeur != '0'){
						$aucuneOrigine = false;
						//Si la valeur n'est pas déjà enregistrée en BDD on l'ajoute
						$success = $this->_enregistrerOrigine($nom, $id, $success);
					} else {
						//Si la ligne existe en BDD, on la supprime
						$origine = $this->ExceptionimpressiontypeorientOrigine->findByExcepimprtypeorientIdAndOrigine($id, $nom);
						if($origine){
							$this->ExceptionimpressiontypeorientOrigine->delete($origine['ExceptionimpressiontypeorientOrigine']['id']);
						}
					}
				}

				if($aucuneOrigine == true){
					//on enregistre toutes les origines
					foreach ($this->request->data['ExceptionimpressiontypeorientOrigine'] as $nom => $valeur){
						$success = $this->_enregistrerOrigine($nom, $id, $success);
					}
				}

				if( true === $success ) {
					$this->Exceptionimpressiontypeorient->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( '/Typesorients/edit/'.$typeorient_id );
				}
				else {
					$this->Exceptionimpressiontypeorient->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else if ($this->action == 'edit' ) {
				$this->request->data = $exception;
				if( true === empty( $this->request->data ) ) {
					throw new NotFoundException();
				}
			}


			$options['Activite']['act'] = [$this->Activite->enum( 'act' )];
			$options['origines'] = $this->Exceptionimpressiontypeorient->getOrigines($id);
			$options['porteurprojet'] = $this->Exceptionimpressiontypeorient->getPorteurprojet();
			$options['Structurereferente'] = $this->Structurereferente->listByTypeOrient($typeorient_id);
			$options['Zonegeographique']= $this->Zonegeographique->find( 'list' );

			$this->set( compact('options', 'typeorient_id') );
			$this->render( 'add_edit' );
		}

		public function delete($id){
			//On supprime les origines liées
			$this->ExceptionimpressiontypeorientOrigine->deleteAll(['excepimprtypeorient_id' => $id]);

			//On supprime les zones géographiques liées
			$this->ExcepimprtypeorientZonegeo->deleteAll(['excepimprtypeorient_id' => $id]);


			$exception = $this->Exceptionimpressiontypeorient->findById($id);
			$this->WebrsaParametrages->delete( $id, array( 'blacklist' => $this->blacklist, 'redirect' => '/Typesorients/edit/'.$exception['Exceptionimpressiontypeorient']['typeorient_id']) );
		}

		public function monter($id, $typeorient_id){
			$retour = $this->Exceptionimpressiontypeorient->getLePlusProche($id, 'monter', $typeorient_id);
			$this->inverserOrdre($id, $retour['idAutre'], $retour['ordre'], $retour['ordreAutre']);
			$this->redirect( '/Typesorients/edit/'.$typeorient_id );
		}

		public function descendre($id, $typeorient_id){
			$retour = $this->Exceptionimpressiontypeorient->getLePlusProche($id, 'descendre', $typeorient_id);
			$this->inverserOrdre($id, $retour['idAutre'], $retour['ordre'], $retour['ordreAutre']);
			$this->redirect( '/Typesorients/edit/'.$typeorient_id );
		}

		private function inverserOrdre($id1, $id2, $ordre1, $ordre2){
			$this->Exceptionimpressiontypeorient->begin();
			$data = ['id' => $id1, 'ordre' => $ordre2];
			$data2 = ['id' => $id2, 'ordre' => $ordre1];
			$this->Exceptionimpressiontypeorient->saveAll( $data, array( 'atomic' => false ) );
			$this->Exceptionimpressiontypeorient->clear();
			$this->Exceptionimpressiontypeorient->saveAll( $data2, array( 'atomic' => false ) );
			$this->Exceptionimpressiontypeorient->commit();
		}

		/**
		 *
		 *  Permet l'enregistrement de l'origine
		 * @param string nom
		 * @param $strin valeur
		 * @param bool success
		 *
		 * @return bool
		*/
		protected function _enregistrerOrigine($nom, $id, $success){
			if(!$this->ExceptionimpressiontypeorientOrigine->findByExcepimprtypeorientIdAndOrigine($id, $nom)){
				$data = ['ExceptionimpressiontypeorientOrigine' => [
					'excepimprtypeorient_id' => $id,
					'origine' => $nom,
				]];
				$this->ExceptionimpressiontypeorientOrigine->begin();
				$this->ExceptionimpressiontypeorientOrigine->create($data);
				$success = $success && $this->ExceptionimpressiontypeorientOrigine->save(null, array( 'atomic' => false ));

				if($success){
					$this->ExceptionimpressiontypeorientOrigine->commit();
					$this->ExceptionimpressiontypeorientOrigine->clear();
				} else {
					$this->ExceptionimpressiontypeorientOrigine->rollback();
						$this->Flash->error( __( 'Save->error' ) );
				}
			}
			return $success;
		}

		protected function _adaptTabOrigine($data) {
			$origines = $this->Exceptionimpressiontypeorient->getOrigines();
			$excepOriginesC = $data['Exceptionimpressiontypeorient']['ExceptionimpressiontypeorientOrigineC'] != '' ? $data['Exceptionimpressiontypeorient']['ExceptionimpressiontypeorientOrigineC'] : [];
			$excepOriginesHC = $data['Exceptionimpressiontypeorient']['ExceptionimpressiontypeorientOrigineHC'] != '' ? $data['Exceptionimpressiontypeorient']['ExceptionimpressiontypeorientOrigineHC'] : [];
			$excepOrigines = array_merge(
				$excepOriginesC,
				$excepOriginesHC
			);
			unset($data['Exceptionimpressiontypeorient']['ExceptionimpressiontypeorientOrigine']);

			foreach($origines as $name => $value) {
				if(in_array($name, $excepOrigines) == false) {
					$excepOrigines[$name] = '0';
				} else {
					$key = array_search($name, $excepOrigines);
					$excepOrigines[$name] = $name;
					unset($excepOrigines[$key]);
				}
			}
			$data['ExceptionimpressiontypeorientOrigine'] = $excepOrigines;

			return $data;
		}
	}