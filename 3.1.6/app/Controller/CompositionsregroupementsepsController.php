<?php	
	/**
	 * Code source de la classe CompositionsregroupementsepsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CompositionsregroupementsepsController ...
	 *
	 * @package app.Controller
	 */
	class CompositionsregroupementsepsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Compositionsregroupementseps';

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
			'Default',
			'Default2',
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
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read',
		);

		protected function _setOptions() {
			$options = $this->Compositionregroupementep->enums();
			$this->set( compact( 'options' ) );
		}

		public function index() {
			$this->paginate = array(
				'limit' => 10
			);

			$this->_setOptions();
			$this->set( 'regroupementseps', $this->paginate( $this->Compositionregroupementep->Regroupementep ) );
			$compteurs = array(
				'Regroupementep' => $this->Compositionregroupementep->Regroupementep->find( 'count' ),
				'Fonctionmembreep' => $this->Compositionregroupementep->Fonctionmembreep->find( 'count' )
			);
			$this->set( compact( 'compteurs' ) );
		}

		/**
		*
		*/

		public function edit( $id = null ) {
			if( !empty( $this->request->data ) ) {
				$success = true;
				$this->Compositionregroupementep->begin();
				$prioritaireExist = false;
				foreach( $this->request->data['Compositionregroupementep'] as $functionmembreep_id => $fields ) {
					if ( $this->request->data['Compositionregroupementep'][$functionmembreep_id]['prioritaire'] == 1 ) {
						$prioritaireExist = true;
					}
					$compositionregroupementep['Compositionregroupementep'] = $fields;
					$compositionregroupementep['Compositionregroupementep']['regroupementep_id'] = $id;
					$compositionregroupementep['Compositionregroupementep']['fonctionmembreep_id'] = $functionmembreep_id;
					$this->Compositionregroupementep->create( $compositionregroupementep );
					$success = $this->Compositionregroupementep->save() && $success;
				}
				$success = $prioritaireExist && $success;
				if ( !$prioritaireExist ) {
					$this->set( 'prioritaireExist', 'error' );
				}

				$this->_setFlashResult( 'Save', $success );
				if( $success ) {
					$this->Compositionregroupementep->commit();
					$this->redirect( array( 'action' => 'index' ) );
				}
				else{
					$this->Compositionregroupementep->rollback();
				}
			}
			else {
				$regroupementep = $this->Compositionregroupementep->Regroupementep->find(
					'first',
					array(
						'conditions' => array( 'Regroupementep.id' => $id ),
						'contain' => array(
							'Compositionregroupementep'
						)
					)
				);
				$this->assert( !empty( $regroupementep ), 'error404' );
				$this->request->data['Regroupementep'] = $regroupementep['Regroupementep'];
				foreach( $regroupementep['Compositionregroupementep'] as $compo ) {
					$this->request->data['Compositionregroupementep'][$compo['fonctionmembreep_id']] = $compo;
				}
			}
			$fonctionsmembreseps = $this->Compositionregroupementep->Fonctionmembreep->find( 'list' );
			$this->set( compact( 'fonctionsmembreseps' ) );
			$this->_setOptions();
		}

		/**
		*
		*/

		public function delete( $id ) {
			$success = $this->Fonctionmembreep->delete( $id );
			$this->_setFlashResult( 'Delete', $success );
			$this->redirect( array( 'action' => 'index' ) );
		}
	}
?>