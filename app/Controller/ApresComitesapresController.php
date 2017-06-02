<?php
	/**
	 * Code source de la classe ApresComitesapresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ApresComitesapresController ...
	 *
	 * @package app.Controller
	 */
	class ApresComitesapresController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'ApresComitesapres';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Jetonsfonctions2',
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
			'ApreComiteapre',
			'Apre',
			'Comiteapre',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Actionscandidats:edit',
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
			'edit' => 'update',
		);

		/**
		*
		*/

		public function beforeFilter() {
			$return = parent::beforeFilter();
			$options = (array)Hash::get( $this->ApreComiteapre->enums(), 'ApreComiteapre' );
			$this->set( 'options', $options );
			return $return;
		}

		/**
		*
		*/

		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}


		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		*
		*/

		protected function _add_edit( $id ){
			$this->Jetonsfonctions2->get( array( 'action' => '_add_edit' ) );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetonsfonctions2->release( array( 'action' => '_add_edit' ) );
				$this->redirect( array( 'controller' => 'comitesapres', 'action'     => 'view', $id ) );
			}

			$isRecours = Set::classicExtract( $this->request->params, 'named.recours' );
			$isRapport = Set::classicExtract( $this->request->params, 'named.rapport' );

			$querydata = $this->ApreComiteapre->Apre->WebrsaApre->qdPourComiteapre( $id, $isRecours );
			$this->assert( !empty( $querydata ), 'error404' );

			$this->Apre->deepAfterFind = false;
			$apres = $this->Apre->find( 'all', $querydata );

			$this->set( 'apres', $apres );

			if( $this->action == 'add' ) {
				$comiteapre_id = $id;

				$nbrComites = $this->Comiteapre->find( 'count', array( 'conditions' => array( 'Comiteapre.id' => $comiteapre_id ), 'recursive' => -1 ) );
				$this->assert( ( $nbrComites == 1 ), 'invalidParameter' );
			}
			else if( $this->action == 'edit' ) {
				$comiteapre_id = $id;
				$aprecomite = $this->ApreComiteapre->find(
					'all',
					array(
						'conditions' => array(
							'ApreComiteapre.comiteapre_id' => $comiteapre_id
						)
					)
				);
			}

			// Formulaire renvoyé
			if( !empty( $this->request->data ) ) {
				$this->Comiteapre->begin();

				if( isset( $this->request->data['Apre'] ) && isset( $this->request->data['Apre']['Apre'] ) ) {
					$success = true;
					$apresCochees = array_filter( $this->request->data['Apre']['Apre'] );

					// Suppression des entrées précédemment cochées pour ce comité, en fonction du fait que les APREs soient en recours ou pas
					$conditions = array(
						'ApreComiteapre.comiteapre_id' => $id,
						( $isRecours ? 'ApreComiteapre.comite_pcd_id IS NOT NULL' : 'ApreComiteapre.comite_pcd_id IS NULL' )
					);

					$success = $this->ApreComiteapre->deleteAll( $conditions ) && $success;

					$this->ApreComiteapre->validate = array();

					foreach( $apresCochees as $apreCocheeId ) {
						$aprecomiteapre = array(
							'ApreComiteapre' => array(
								'comiteapre_id' => $id,
								'apre_id' => $apreCocheeId
							)
						);

						// Si ce sont des APREs en recours
						if( $isRecours ) {
							// On recherche l'id du comité pour lesquelles elles passent en recours
							$ancienneDecision = $this->ApreComiteapre->find(
								'first',
								array(
									'fields' => array( 'ApreComiteapre.comiteapre_id' ),
									'conditions' => array(
										'ApreComiteapre.comiteapre_id <>' => $id,
										'ApreComiteapre.apre_id' => $apreCocheeId,
									),
									'joins' => array(
										array(
											'table'      => 'comitesapres',
											'alias'      => 'Comiteapre',
											'type'       => 'INNER',
											'foreignKey' => false,
											'conditions' => array( 'ApreComiteapre.comiteapre_id = Comiteapre.id' )
										),
									),
									'order' => 'Comiteapre.datecomite DESC',
									'recursive' => -1,
								)
							);
							$this->assert( !empty( $ancienneDecision ), 'error500' ); // FIXME -> préciser l'erreur
							// Pour l'enregistrer dans ce passage-ci
							$aprecomiteapre['ApreComiteapre']['comite_pcd_id'] = Set::classicExtract( $ancienneDecision, 'ApreComiteapre.comiteapre_id' );
						}

						$this->ApreComiteapre->create( $aprecomiteapre );
						$success = $this->ApreComiteapre->save() && $success;
					}

					if( $success ) {
						$this->Comiteapre->commit();
						$this->Jetonsfonctions2->release( array( 'action' => '_add_edit' ) );
						if( !$isRapport ){
							$this->redirect( array( 'controller' => 'comitesapres', 'action' => 'view', $id ) );
						}
						else if( $isRapport ){
							$this->redirect( array( 'controller' => 'comitesapres', 'action' => 'rapport', $id ) );
						}
					}
					else {
						$this->Comiteapre->rollback();
					}
				}
			}
			else {
				if( $this->action == 'edit' ) {
					$this->request->data = array(
						'Comiteapre' => array(
							'id' => $comiteapre_id,
						),
						'Apre' => array(
							'Apre' => Set::extract( $aprecomite, '/ApreComiteapre/apre_id' )
						)
					);

				}
				else {
					$this->request->data['Comiteapre']['id'] = $comiteapre_id;
				}
			}

			$this->Comiteapre->commit();
			$this->set( 'comiteapre_id', $comiteapre_id );
			$this->render( 'add_edit' );
		}
	}
?>
