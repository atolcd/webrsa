<?php
	/**
	 * Code source de la classe TyposcontratsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe TyposcontratsController ...
	 *
	 * @package app.Controller
	 */
	class TyposcontratsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Typoscontrats';

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
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Typocontrat',
			'Contratinsertion',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Typoscontrats:edit',
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

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'parametrages', 'action' => 'index' ) );
			}
			$typoscontrats = $this->Typocontrat->find(
				'all',
				array(
					'recursive' => -1
				)
			);
			$this->set('typoscontrats', $typoscontrats);
		}

		public function add() {
			if( !empty( $this->request->data ) ) {
				if( $this->Typocontrat->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'typoscontrats', 'action' => 'index' ) );
				}
			}
			$this->render( 'add_edit' );
		}

		public function edit( $typocontrat_id = null ) {
			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $typocontrat_id ), 'error404' );

			if( !empty( $this->request->data ) ) {
				if( $this->Typocontrat->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'typoscontrats', 'action' => 'index' ) );
				}
			}
			else {
				$typocontrat = $this->Typocontrat->find(
					'first',
					array(
						'conditions' => array(
							'Typocontrat.id' => $typocontrat_id,
						),
						'recursive' => -1
					)
				);
				$this->request->data = $typocontrat;
			}

			$this->render( 'add_edit' );
		}

		public function delete( $typocontrat_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $typocontrat_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$typocontrat = $this->Typocontrat->find(
				'first',
				array( 'conditions' => array( 'Typocontrat.id' => $typocontrat_id )
				)
			);
			// Mauvais paramètre
			if( empty( $typocontrat_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			if( $this->Typocontrat->delete( array( 'Typocontrat.id' => $typocontrat_id ) ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'typoscontrats', 'action' => 'index' ) );
			}
		}
	}
?>