<?php
	/**
	 * Code source de la classe ActionrolesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ActionrolesController s'occupe du paramétrage des actions de
	 * rôles.
	 *
	 * @package app.Controller
	 */
	class ActionrolesController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Actionroles';

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default',
			'Default2',
			'Theme',
			'Xform',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Actionrole' );

		/**
		 * Liste des éléments.
		 *
		 * @todo final
		 */
		public function index() {
			$query = array(
				'contain' => array(
					'Role.name',
					'Categorieactionrole.name'
				)
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Modification d'une action de rôles.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
            // Retour à la liste en cas d'annulation
            if( isset( $this->request->data['Cancel'] ) ) {
                $this->redirect( array( 'controller' => 'actionroles', 'action' => 'index' ) );
            }

			if( !empty( $this->request->data ) ) {
				$this->Actionrole->create( $this->request->data );
				$success = $this->Actionrole->save( null, array( 'atomic' => false ) );

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
				$this->request->data = $this->Actionrole->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array( 'Actionrole.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );
			}
			else{
				$this->request->data['Actionrole']['actif'] = true;
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
			$options['Actionrole']['role_id'] = $this->Actionrole->Role->find('list', array('order' => 'name', 'conditions' => array('actif' => 1)));
			$options['Actionrole']['categorieactionrole_id'] = $this->Actionrole->Categorieactionrole->find('list', array('order' => 'name'));

			return $options;
		}
	}
?>