<?php
	/**
	 * Code source de la classe SignalementsepsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe SignalementsepsController ...
	 *
	 * @package app.Controller
	 */
	class SignalementsepsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Signalementseps';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses' => array(
				'mainModelName' => 'Signalementep93',
				'webrsaModelName' => 'WebrsaSignalementep',
				'webrsaAccessName' => 'WebrsaAccessSignalementseps',
				'parentModelName' => 'Contratinsertion',
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Signalementep93',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Signalementseps:edit',
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
		);

		/**
		 *
		 */
		public function beforeFilter() {
			$this->modelClass = 'Signalementep'.Configure::read( 'Cg.departement' );
			parent::beforeFilter();
		}

		/**
		 *
		 * @param integer $contratinsertion_id
		 */
		public function add( $contratinsertion_id ) {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		*
		*/

		public function edit( $id ) {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 * @param integer $id
		 */
		protected function _add_edit( $id = null ) {
			if( $this->action == 'add' ) {
				$contratinsertion_id = $id;
				$this->WebrsaAccesses->check( null, $contratinsertion_id );
			}
			else {
				$this->WebrsaAccesses->check( $id );

				$signalementep_id = $id;
				$signalementep = $this->{$this->modelClass}->find(
					'first',
					array(
						'conditions' => array(
							$this->modelClass.'.id' => $signalementep_id
						),
						'contain' => false
					)
				);
				$this->assert( !empty( $signalementep ), 'invalidParameter' );
				$contratinsertion_id = $signalementep[$this->modelClass]['contratinsertion_id'];
			}

			// Recherche du CER et vérifications
			$contratinsertion = $this->{$this->modelClass}->Contratinsertion->find(
				'first',
				array(
					'conditions' => array(
						'Contratinsertion.id' => $contratinsertion_id
					),
					'contain' => false
				)
			);
			$this->assert( !empty( $contratinsertion ), 'invalidParameter' );

			$personne_id = $contratinsertion['Contratinsertion']['personne_id'];
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$erreursCandidatePassage = $this->{$this->modelClass}->Dossierep->getErreursCandidatePassage( $personne_id );

			$dureeTolerance = Configure::read( $this->modelClass.'.dureeTolerance' );

			$traitable = (
				( $contratinsertion['Contratinsertion']['decision_ci'] == 'V' )
				&& ( strtotime( $contratinsertion['Contratinsertion']['dd_ci'] ) <= time() )
				&& ( strtotime( $contratinsertion['Contratinsertion']['df_ci'] ) + ( $dureeTolerance * 24 * 60 * 60 ) >= time() )
				&& empty( $erreursCandidatePassage )
			);
			$this->assert( $traitable, 'error500' );

			if( !empty( $this->request->data ) ) {
				if( Configure::read( 'Cg.departement' ) == 93 ) {
					$redirectUrl = array( 'controller' => 'cers93', 'action' => 'index', $personne_id );
				}
				else {
					$redirectUrl = array( 'controller' => 'contratsinsertion', 'action' => 'index', $personne_id );
				}

				if ( isset( $this->request->data['Cancel'] ) ) {
					$this->redirect( $redirectUrl );
				}

				$this->{$this->modelClass}->Dossierep->begin();

				if( $this->action == 'add' ) {
					$rangpcd = $this->{$this->modelClass}->field( 'rang', array( $this->modelClass.'.contratinsertion_id' => $contratinsertion_id ), array( $this->modelClass.'.rang DESC' ) );
					$this->request->data[$this->modelClass]['contratinsertion_id'] = $contratinsertion_id;
					$this->request->data[$this->modelClass]['rang'] = ( empty( $rangpcd ) ? 1 : $rangpcd + 1 );

					$this->request->data['Dossierep']['personne_id'] = $personne_id;
					$this->request->data['Dossierep']['themeep'] = Inflector::tableize( $this->modelClass );

					$success = $this->{$this->modelClass}->Dossierep->saveAll( $this->request->data, array( 'atomic' => false ) );
				}
				else {
					$success = $this->{$this->modelClass}->create( $this->request->data );
					$success = $this->{$this->modelClass}->save();
				}

				$this->_setFlashResult( 'Save', $success );
				if( $success ) {
					$this->{$this->modelClass}->commit();
					$this->redirect( $redirectUrl );
				}
				else {
					$this->{$this->modelClass}->Dossierep->rollback();
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $signalementep;
			}

			$this->set( 'personne_id', $personne_id );
			$this->set( 'urlmenu', '/cers93/index/'.$personne_id );
			$this->render( 'add_edit' );
		}

		/**
		* Permet de supprimer un signalement SSI celui-ci:
		*	- existe
		*	- n'est pas associé à une commission d'EP
		 *
		 * @param integer $id L'id du signalement
		 */
		public function delete( $id ) {
			$this->WebrsaAccesses->check( $id );

			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->{$this->modelClass}->personneId( $id ) ) );

			$signalementep = $this->{$this->modelClass}->Dossierep->find(
				'first',
				array(
					'fields' => array(
						$this->modelClass.'.id',
						'Dossierep.id',
						'Passagecommissionep.etatdossierep',
					),
					'conditions' => array(
						$this->modelClass.'.id' => $id,
						'Dossierep.themeep' => Inflector::tableize( $this->modelClass ),
						'Dossierep.id NOT IN ( '.$this->{$this->modelClass}->Dossierep->Passagecommissionep->sq(
							array(
								'alias' => 'passagescommissionseps',
								'fields' => array(
									'passagescommissionseps.dossierep_id'
								),
								'conditions' => array(
									'passagescommissionseps.dossierep_id = Dossierep.id'
								)
							)
						).' )'
					),
					'joins' => array(
						array(
							'table'      => Inflector::tableize( $this->modelClass ),
							'alias'      => $this->modelClass,
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( "Dossierep.id = {$this->modelClass}.dossierep_id" )
						),
						array(
							'table'      => 'contratsinsertion',
							'alias'      => 'Contratinsertion',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( "Contratinsertion.id = {$this->modelClass}.contratinsertion_id" )
						),
						array(
							'table'      => 'passagescommissionseps',
							'alias'      => 'Passagecommissionep',
							'type'       => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array( 'Dossierep.id = Passagecommissionep.dossierep_id' )
						),
					),
				)
			);

			$this->assert( !empty( $signalementep ), 'invalidParameter' );

			$this->{$this->modelClass}->Dossierep->begin();
			$success = $this->{$this->modelClass}->Dossierep->delete( $signalementep['Dossierep']['id'] );
			$this->_setFlashResult( 'Delete', $success );

			if( $success ) {
				$this->{$this->modelClass}->Dossierep->commit();
			}
			else {
				$this->{$this->modelClass}->Dossierep->rollback();
			}

			$this->redirect( $this->referer() );
		}
	}
?>