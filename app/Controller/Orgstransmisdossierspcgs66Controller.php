<?php
	/**
	 * Code source de la classe Orgstransmisdossierspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::import( 'Behaviors', 'Occurences' );

	/**
	 * La classe Orgstransmisdossierspcgs66Controller ...
	 *
	 * @package app.Controller
	 */
	class Orgstransmisdossierspcgs66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Orgstransmisdossierspcgs66';

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
			
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Orgstransmisdossierspcgs66:edit',
			'view' => 'Orgstransmisdossierspcgs66:index',
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read',
		);

        /**
         *  Liste des options envoyées à la vue
         */
        protected function _setOptions() {
            $options = array();
            $options = $this->Orgtransmisdossierpcg66->enums();
            $polesdossierspcgs66 = $this->Orgtransmisdossierpcg66->Poledossierpcg66->find( 'list' );

            $this->set( compact( 'options', 'polesdossierspcgs66' ) );
        }
		/**
		 * Pagination sur les <élément>s de la table.
		 *
		 * @return void
		 */
		public function index() {
			$this->Orgtransmisdossierpcg66->Behaviors->attach( 'Occurences' );
  
            $querydata = $this->Orgtransmisdossierpcg66->qdOccurencesExists(
                array(
                    'fields' => array_merge(
                        $this->Orgtransmisdossierpcg66->fields(),
                        $this->Orgtransmisdossierpcg66->Poledossierpcg66->fields()
                    ),
                    'joins' => array(
                        $this->Orgtransmisdossierpcg66->join( 'Poledossierpcg66', array( 'type' => 'LEFT OUTER') )
                    ),
                    'order' => array( 'Orgtransmisdossierpcg66.name ASC' )
                )
            );

            $this->paginate = $querydata;
            $orgstransmisdossierspcgs66 = $this->paginate('Orgtransmisdossierpcg66');
            $this->set( compact('orgstransmisdossierspcgs66'));
            $this->_setOptions();
		}

		/**
		 * Formulaire d'ajout d'un élémént.
		 *
		 * @return void
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Formulaire de modification d'un <élément>.
		 *
		 * @throws NotFoundException
		 */
		public function edit( $id = null ) {
		 // Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'orgstransmisdossierspcgs66', 'action' => 'index' ) );
			}
			if( !empty( $this->request->data ) ) {
				$this->{$this->modelClass}->begin();
				$this->{$this->modelClass}->create( $this->request->data );

				if( $this->{$this->modelClass}->save() ) {
					$this->{$this->modelClass}->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->{$this->modelClass}->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->{$this->modelClass}->find(
					'first',
					array(
						'conditions' => array(
							"{$this->modelClass}.id" => $id
						),
						'contain' => false
					)
				);

				if( empty( $this->request->data  ) ) {
					throw new NotFoundException();
				}
			}

            $this->_setOptions();
			$this->render( 'edit' );
		}

		/**
		 * Suppression d'un <élément> et redirection vers l'index.
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->{$this->modelClass}->begin();

			if( $this->{$this->modelClass}->delete( $id ) ) {
				$this->{$this->modelClass}->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->{$this->modelClass}->rollback();
				$this->Session->setFlash( 'Erreur lors de la suppression', 'flash/error' );
			}

			$this->redirect( array( 'action' => 'index' ) );
		}
	}
?>
