<?php
	/**
	 * Code source de la classe CategoriesactionrolesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
    App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe CategoriesactionrolesController s'occupe du paramétrage des
	 * catégories de roles.
	 *
	 * @package app.Controller
	 */
	class CategoriesactionrolesController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Categoriesactionroles';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Categorieactionrole' );

		/**
		 * Modification d'une catégorie de role.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
            // Retour à la liste en cas d'annulation
            if( isset( $this->request->data['Cancel'] ) ) {
                $this->redirect( array( 'controller' => 'categoriesactionroles', 'action' => 'index' ) );
            }

			if( !empty( $this->request->data ) ) {
				$this->Categorieactionrole->create( $this->request->data );
				$success = $this->Categorieactionrole->save( null, array( 'atomic' => false ) );

				if( $success ) {
					$this->Flash->success( __( 'Save->success' ) );
					Cache::config('one day', array(
						'engine' => 'File',
						'duration' => '+1 day',
						'path' => CACHE,
						'prefix' => 'cake_oneday_'
					));
					Cache::clear(false, 'one day');
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Categorieactionrole->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array( 'Categorieactionrole.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );
			}
			else{
				$this->request->data['Categorieactionrole']['actif'] = true;
			}

			$options = $this->_options();

			$this->set( compact( 'options' ) );

			$this->view = 'add_edit';
		}

		/**
		 * Options pour la vue
		 *
		 * @return array
		 */
		protected function _options() {
			return array();
		}
	}
?>