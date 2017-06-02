<?php
	/**
	 * Code source de la classe Sortiesaccompagnementsd2pdvs93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');

	/**
	 * La classe Sortiesaccompagnementsd2pdvs93Controller ...
	 *
	 * @package app.Controller
	 */
	class Sortiesaccompagnementsd2pdvs93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Sortiesaccompagnementsd2pdvs93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Sortieaccompagnementd2pdv93',
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
		 * Pagination sur les motifs de sortie de l'accompagnement.
		 */
		public function index() {
			$querydata = array(
				'fields' => array(
					'Sortieaccompagnementd2pdv93.id',
					'Sortieaccompagnementd2pdv93.name',
					'Parent.name',
				),
				'joins' => array(
					$this->Sortieaccompagnementd2pdv93->join( 'Parent' )
				),
				'order' => array(
					'( CASE WHEN Parent.name IS NULL THEN \'\' ELSE Parent.name END ) ASC',
					'Sortieaccompagnementd2pdv93.name ASC',
				),
				'limit' => 50
			);
			$querydata = $this->Sortieaccompagnementd2pdv93->qdOccurencesExists( $querydata );
			$this->paginate = array( 'Sortieaccompagnementd2pdv93' => $querydata );

			$varname = Inflector::tableize( 'Sortieaccompagnementd2pdv93' );
			$this->set( $varname, $this->paginate() );
		}

		/**
		 * Formulaire d'ajout d'un motif de sortie de l'accompagnement.
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Formulaire de modification d'un motif de sortie de l'accompagnement.
		 *
		 * @throws NotFoundException
		 */
		public function edit( $id = null ) {
			if( !empty( $this->request->data ) ) {
				// Retour à l'index en cas d'annulation
				if( isset( $this->request->data['Cancel'] ) ) {
					$this->redirect( array( 'action' => 'index' ) );
				}

				$this->Sortieaccompagnementd2pdv93->begin();
				$this->Sortieaccompagnementd2pdv93->create( $this->request->data );

				if( $this->Sortieaccompagnementd2pdv93->save() ) {
					$this->Sortieaccompagnementd2pdv93->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Sortieaccompagnementd2pdv93->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Sortieaccompagnementd2pdv93->find(
					'first',
					array(
						'conditions' => array(
							'Sortieaccompagnementd2pdv93.id' => $id
						),
						'contain' => false
					)
				);

				if( empty( $this->request->data  ) ) {
					throw new NotFoundException();
				}
			}

			// Liste des parents, moins nous-même lorsque l'on fait une modification
			$querydata = array(
				'conditions' => array(
					'Sortieaccompagnementd2pdv93.parent_id IS NULL',
				),
				'order' => array(
					'Sortieaccompagnementd2pdv93.name ASC',
				),
			);

			if( $this->action == 'edit' ) {
				$querydata['conditions'][] = array(
					'NOT' => array( 'Sortieaccompagnementd2pdv93.id' => $id )
				);
			}

			$options = array(
				'Sortieaccompagnementd2pdv93' => array(
					'parent_id' => $this->Sortieaccompagnementd2pdv93->find(
						'list',
						$querydata
					)
				)
			);
			$this->set( compact( 'options' ) );

			$this->render( 'edit' );
		}

		/**
		 * Suppression d'un motif de sortie de l'accompagnement et redirection vers l'index.
		 *
		 * @param integer $id
		 * @throws InternalErrorException
		 */
		public function delete( $id ) {
			$occurences = $this->Sortieaccompagnementd2pdv93->occurencesExists( array( 'Sortieaccompagnementd2pdv93.id' => $id ) );
			if( $occurences[$id] ) {
				$message = sprintf( 'Impossible de supprimer l\'enregistrement %d du modèle %s car d\'autres enregistrements en dépendent.', $id, $this->Sortieaccompagnementd2pdv93->alias );
				throw new InternalErrorException( $message );
			}

			$this->Sortieaccompagnementd2pdv93->begin();

			if( $this->Sortieaccompagnementd2pdv93->delete( $id ) ) {
				$this->Sortieaccompagnementd2pdv93->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->Sortieaccompagnementd2pdv93->rollback();
				$this->Session->setFlash( 'Erreur lors de la suppression', 'flash/error' );
			}

			$this->redirect( array( 'action' => 'index' ) );
		}
	}
?>
