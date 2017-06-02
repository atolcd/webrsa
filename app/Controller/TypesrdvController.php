<?php
	/**
	 * Code source de la classe TypesrdvController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe TypesrdvController ...
	 *
	 * @package app.Controller
	 */
	class TypesrdvController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Typesrdv';

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
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Rendezvous',
			'Option',
			'Typerdv',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Typesrdv:edit',
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
		 * Liste des types de RDV
		 */
		public function index() {
			$typesrdv = $this->Typerdv->find(
				'all',
				array(
					'recursive' => -1,
					'order' => 'Typerdv.libelle ASC'
				)
			);

			$this->set( 'typesrdv', $typesrdv );
		}

		public function add() {
			if( !empty( $this->request->data ) ) {
				$this->Typerdv->begin();
				if( $this->Typerdv->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) ) ) {
					$this->Typerdv->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'typesrdv', 'action' => 'index' ) );
				}
				else {
					$this->Typerdv->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}

			$this->render( 'add_edit' );
		}

		public function edit( $typerdv_id = null ){
			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $typerdv_id ), 'invalidParameter' );

			$typerdv = $this->Typerdv->find(
				'first',
				array(
					'conditions' => array(
						'Typerdv.id' => $typerdv_id
					),
					'recursive' => -1
				)
			);
			// Si action n'existe pas -> 404
			if( empty( $typerdv ) ) {
				$this->cakeError( 'error404' );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Typerdv->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'typesrdv', 'action' => 'index', $typerdv['Typerdv']['id']) );
				}
			}
			else {
				$this->request->data = $typerdv;
			}
			$this->render( 'add_edit' );
		}

		/** ********************************************************************
		*
		*** *******************************************************************/

		public function delete( $typerdv_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $typerdv_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$typerdv = $this->Typerdv->find(
				'first',
				array( 'conditions' => array( 'Typerdv.id' => $typerdv_id )
				)
			);

			// Mauvais paramètre
			if( empty( $typerdv ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			if( $this->Typerdv->deleteAll( array( 'Typerdv.id' => $typerdv_id ), true ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'typesrdv', 'action' => 'index' ) );
			}
		}
	}

?>