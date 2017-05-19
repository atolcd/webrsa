<?php
	/**
	 * Code source de la classe AbstractWebrsaAllocatairesliesComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'DefaultUrl', 'Default.Utility' );
	App::uses( 'DefaultUtility', 'Default.Utility' );

	/**
	 * La classe AbstractWebrsaAllocatairesliesComponent ...
	 *
	 * @see OrientsstructsController::_getIndexActionsList()
	 * @see OrientsstructsController::_getCompletedIndexResults()
	 * @todo Controller::redirect() ou this::redirect()
	 * @todo les autres component doivent hériter de celui-ci
	 * @todo faire un répertoire en plus dans les controller / component ? (Webrsa/Logic/...) -> fonctions abstraites ici
	 * @todo ici c'est crud, faire un autre pour les recherches, ...
	 * @todo getOptions() par ici ?
	 * @info ici on est dans la partie CRUD
	 * @todo des classes de modèles "Logic" ?
	 *
	 * @package app.Controller.Component
	 */
	abstract class WebrsaAllocatairesliesComponent extends Component
	{
		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array( );

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Gedooo.Gedooo',
			'Jetons2',
			'Session'
		);

		public function ajaxReferent( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$params += array(
				'modelClass' => $Controller->modelClass,
				'view' => 'ajax_referent',
				'layout' => 'ajax'
			);
			$Model = $Controller->{$params['modelClass']};

			$structurereferente_id = suffix( Hash::get( $this->request->data, "{$params['modelClass']}.structurereferente_id" ) );
			$referent_id = suffix( Hash::get( $this->request->data, "{$params['modelClass']}.referent_id" ) );

			$result = array( );
			if( !empty( $referent_id ) ) {
				$query = array(
					'fields' => array(
						'Typeorient.lib_type_orient',
						'Structurereferente.num_voie',
						'Structurereferente.type_voie',
						'Structurereferente.nom_voie',
						'Structurereferente.code_postal',
						'Structurereferente.ville',
						'Referent.fonction',
						'Referent.email',
						'Referent.numero_poste',
					),
					'joins' => array(
						$Model->Referent->join( 'Structurereferente' ),
						$Model->Referent->Structurereferente->join( 'Typeorient' )
					),
					'conditions' => array(
						'Referent.id' => $referent_id
					),
					'recursive' => -1
				);
				$result = $Model->Referent->find( 'first', $query );
			}
			else if( !empty( $structurereferente_id ) ) {
				$query = array(
					'fields' => array(
						'Typeorient.lib_type_orient',
						'Structurereferente.num_voie',
						'Structurereferente.type_voie',
						'Structurereferente.nom_voie',
						'Structurereferente.code_postal',
						'Structurereferente.ville'
					),
					'joins' => array(
						$Model->Referent->Structurereferente->join( 'Typeorient' )
					),
					'conditions' => array(
						'Structurereferente.id' => $structurereferente_id
					),
					'recursive' => -1
				);
				$result = $Model->Referent->Structurereferente->find( 'first', $query );
			}

			$Controller->set( compact( 'result' ) );
			$Controller->view = $params['view'];
			$Controller->layout = $params['layout'];
		}
		
		abstract public function prepareAddEditFormData( $personne_id, $id, $user_id );

		abstract public function saveAddEditFormData( $data, $user_id );

		// Template method
		public function addEdit( $id = null, $params = array() ) {
			$Controller = $this->_Collection->getController();
			$params += array(
				'modelClass' => $Controller->modelClass,
				'redirect' => "/{$Controller->name}/index/#personne_id#",
				'view' => 'edit',
				'urlmenu' => "/{$Controller->name}/index/#personne_id#"
			);
			$Model = $Controller->{$params['modelClass']};

			if( $Controller->action == 'add' ) {
				$personne_id = $id;
				$id = null;
			}
			else {
				$personne_id = $Model->personneId( $id );
			}

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );

			$params['redirect'] = DefaultUrl::toArray( DefaultUtility::evaluate( $dossierMenu, $params['redirect'] ) );
			$params['urlmenu'] = Inflector::underscore( DefaultUtility::evaluate( $dossierMenu, $params['urlmenu'] ) );

			// Retour à l'index en cas d'annulation
			if( isset( $Controller->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
				$Controller->redirect( $params['redirect'] );
			}

			if( !empty( $Controller->request->data ) ) {
				$Model->begin();
				if( $Model->saveAddEditFormData( $Controller->request->data, $this->Session->read( 'Auth.User.id' ) ) ) {
					$Model->commit();
					$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$Controller->redirect( $params['redirect'] );
				}
				else {
					$Model->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else {
				$Controller->request->data = $Model->prepareAddEditFormData( $personne_id, $id, $this->Session->read( 'Auth.User.id' ) );
			}

			$options = $Model->options( array( 'allocataire' => true, 'find' => true, 'autre' => true ) );

			$urlmenu = $params['urlmenu'];

			$Controller->set( compact( 'options', 'personne_id', 'dossierMenu', 'urlmenu' ) );
			$Controller->view = $params['view'];
		}

		public function delete( $id, $params = array() ) {
			$Controller = $this->_Collection->getController();
			$params += array(
				'modelClass' => $Controller->modelClass,
				'redirect' => $Controller->referer()
			);
			$Model = $Controller->{$params['modelClass']};

			$personne_id = $Model->personneId( $id );
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$dossier_id = Hash::get( $dossierMenu, 'Dossier.id' );

			$this->Jetons2->get( $dossier_id );

			$Model->begin();
			if( $Model->delete( $id ) ) {
				$Model->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$Model->rollback();
				$this->Session->setFlash( 'Erreur lors de la suppression', 'flash/error' );
			}

			$this->Jetons2->release( $dossier_id );
			$Controller->redirect( $params['redirect'] );
		}

		public function impression( $id ) {
			$Controller = $this->_Collection->getController();
			$params += array(
				'modelClass' => $Controller->modelClass,
				'method' => 'getDefaultPdf',
				'redirect' => $Controller->referer(),
				'filename' => Inflector::underscore( "{$Controller->name}_{$Controller->action}_%d.pdf" )
			);
			$Model = $Controller->{$params['modelClass']};

			$personne_id = $Model->personneId( $id );
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $personne_id ) );

			$pdf = $Model->{$params['method']}( $id, $this->Session->read( 'Auth.User.id' ) );
			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( $params['filename'], $id ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer l\'impression', 'default', array( 'class' => 'error' ) );
				$Controller->redirect( $params['redirect'] );
			}
		}
	}
?>