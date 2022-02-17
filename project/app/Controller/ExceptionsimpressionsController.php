<?php
	/**
	 * Code source de la classe CategoriesutilisateursController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe CategoriesutilisateursController s'occupe du paramétrage des
	 * fonctions des membres des EP.
	 *
	 * @package app.Controller
	 */
	class ExceptionsimpressionsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Exceptionsimpressions';

         /**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Exceptionsimpression', 'Activite' );

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
            if( false === empty( $this->request->data ) ) {
				// Retour à la liste en cas d'annulation
				if( isset( $this->request->data['Cancel'] ) ) {
					$this->redirect( '/Typesorients/edit/'.$typeorient_id );
				}

				// Tentative de sauvegarde du formulaire
				$this->Exceptionsimpression->begin();
					$this->Exceptionsimpression->create( $this->request->data );
					$success = false !== $this->Exceptionsimpression->save( null, array( 'atomic' => false ) );

				if( true === $success ) {
					$this->Exceptionsimpression->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( '/Typesorients/edit/'.$typeorient_id );
				}
				else {
					$this->Exceptionsimpression->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
            $dernierOrdre = $this->Exceptionsimpression->findByTypeorientId($typeorient_id, null, ['ordre' =>  'desc']);
            $options['ordre'] = $dernierOrdre == [] ? 1 : $dernierOrdre['Exceptionsimpression']['ordre'] + 1;
            $options['Activite']['act'] = ['' => '', $this->Activite->enum( 'act' )];
            $options['origines'] = $this->Exceptionsimpression->getOrigines();
            $options['porteurprojet'] = $this->Exceptionsimpression->getPorteurprojet();
            $this->set( compact('options', 'typeorient_id') );
            $this->render( 'add_edit' );
        }

        public function edit($id = null, array $params = array()){
            $exception = $this->Exceptionsimpression->findById($id);
            $typeorient_id = $exception['Exceptionsimpression']['typeorient_id'];
            $options['ordre'] = $exception['Exceptionsimpression']['ordre'];
            if( false === empty( $this->request->data ) ) {
                // Retour à la liste en cas d'annulation
				if( isset( $this->request->data['Cancel'] ) ) {
                    $this->redirect( '/Typesorients/edit/'.$typeorient_id );
				}

				// Tentative de sauvegarde du formulaire
				$this->Exceptionsimpression->begin();
                $data = Hash::extract( $this->request->data, 'Exceptionsimpression' );
                $this->Exceptionsimpression->id = $data['id'];
				$success = $this->Exceptionsimpression->saveAll( $data, array( 'atomic' => false ) );

				if( true === $success ) {
                    $this->Exceptionsimpression->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( '/Typesorients/edit/'.$typeorient_id );
				}
				else {
                    $this->Exceptionsimpression->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

            $this->request->data = $exception;

            if( true === empty( $this->request->data ) ) {
                throw new NotFoundException();
            }

            $options['Activite']['act'] = ['' => '', $this->Activite->enum( 'act' )];
            $options['origines'] = $this->Exceptionsimpression->getOrigines();
            $options['porteurprojet'] = $this->Exceptionsimpression->getPorteurprojet();
            $this->set( compact('options', 'typeorient_id') );
            $this->render( 'add_edit' );
        }


        public function delete($id){
            $exception = $this->Exceptionsimpression->findById($id);
            $this->WebrsaParametrages->delete( $id, array( 'blacklist' => $this->blacklist, 'redirect' => '/Typesorients/edit/'.$exception['Exceptionsimpression']['typeorient_id'] ) );
        }

        public function monter($id, $typeorient_id){
            $retour = $this->Exceptionsimpression->getLePlusProche($id, 'monter', $typeorient_id);
            $this->inverserOrdre($id, $retour['idAutre'], $retour['ordre'], $retour['ordreAutre']);
            $this->redirect( '/Typesorients/edit/'.$typeorient_id );
        }

        public function descendre($id, $typeorient_id){
            $retour = $this->Exceptionsimpression->getLePlusProche($id, 'descendre', $typeorient_id);
            $this->inverserOrdre($id, $retour['idAutre'], $retour['ordre'], $retour['ordreAutre']);
            $this->redirect( '/Typesorients/edit/'.$typeorient_id );
        }

        private function inverserOrdre($id1, $id2, $ordre1, $ordre2){
            $this->Exceptionsimpression->begin();
            $data = ['id' => $id1, 'ordre' => $ordre2];
            $data2 = ['id' => $id2, 'ordre' => $ordre1];
            $this->Exceptionsimpression->saveAll( $data, array( 'atomic' => false ) );
            $this->Exceptionsimpression->clear();
            $this->Exceptionsimpression->saveAll( $data2, array( 'atomic' => false ) );
            $this->Exceptionsimpression->commit();
        }

	}

?>