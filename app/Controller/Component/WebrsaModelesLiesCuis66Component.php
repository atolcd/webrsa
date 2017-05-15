<?php
	/**
	 * Code source de la classe WebrsaModelesLiesCuis66Component.
	 *
	 * @package app.Controller.Component
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Component.php.
	 */
	App::uses( 'DefaultUrl', 'Default.Utility' );
	App::uses( 'DefaultUtility', 'Default.Utility' );
	App::uses( 'WebrsaModelUtility', 'Utility' );

	/**
	 * La classe WebrsaModelesLiesCuis66Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaModelesLiesCuis66Component extends Component
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
			'Session',
			'WebrsaAccesses',
		);
		
		public function initAccess($mainModelName = 'Cui', $webrsaModelName = 'WebrsaCui66') {
			App::uses('WebrsaAccess'.Inflector::camelize($this->_Collection->getController()->name), 'Utility');
			$this->WebrsaAccesses->settings += compact('mainModelName', 'webrsaModelName');
			return $this->WebrsaAccesses->init();
		}

		public function index( $cui_id, $params = array(), $customQuery = array() ){
			$this->initAccess();
			$this->WebrsaAccesses->check($cui_id);
			$Controller = $this->_Collection->getController();

			$params += array(
				'modelClass' => $Controller->modelClass,
				'redirect' => "/{$Controller->name}/index/#personne_id#",
				'view' => 'index',
				'urlmenu' => "/{$Controller->name}/index/#personne_id#"
			);
			$Model = $Controller->{$params['modelClass']};
			
			$personne_id = $Model->Cui66->Cui->personneId( $cui_id );
			
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			
			$Model->_setEntriesAncienDossier( $personne_id, 'Cui' );

			$query = array(
				'fields' => array_merge(
					$Model->fields(),
					array(
						'Cui.id',
						'Cui.personne_id',
						'Cui66.id',
						'Cui66.etatdossiercui66',
						$params['modelClass'] . '.id'
					)
				),
				'conditions' => array(
					'Cui.id' => $cui_id,
				),
				'joins' => array(
					$Model->Cui66->join( 'Cui', array( 'conditions' => array( 'Cui.id' => $cui_id, ), 'type' => 'INNER' ) ),
					$Model->Cui66->join( $params['modelClass'], array( 'type' => 'INNER' ) ),
				),
				'order' => array( $params['modelClass'] . '.created DESC' )
			);
			$query = WebrsaModelUtility::unsetJoin(
				'Cui66', $Controller->WebrsaCui66->completeVirtualfieldsForAccess(
					Hash::merge($query, $customQuery), array('controller' => $Controller->name)
				)
			);
			$accessClassName = 'WebrsaAccess'.Inflector::camelize($Controller->name);
			
			$paramsAccess = $Controller->WebrsaCui66->getParamsForAccess(
				$cui_id, call_user_func(array($accessClassName, 'getParamsList'), $params)
			);
			$ajoutPossible = Hash::get($paramsAccess, 'ajoutPossible') !== false;
			$results = call_user_func(array($accessClassName, 'accesses'), $Model->Cui66->find('all', $query), $paramsAccess);
			
			$messages = $Model->messages( $personne_id );
			$addEnabled = $Model->addEnabled( $messages );
			
			$query = array(
				'fields' => array( 'Cui66.etatdossiercui66' ),
				'conditions' => array( 'Cui66.cui_id' => $cui_id )
			);
			$etatdossiercui66 = $Model->Cui66->find( 'first', $query );
			
			$params['redirect'] = DefaultUrl::toArray( DefaultUtility::evaluate( $results, $params['redirect'] ) );
			$params['urlmenu'] = Inflector::underscore( DefaultUtility::evaluate( $results, $params['urlmenu'] ) );

			// Options
			$options = $Model->options( array( 'allocataire' => false, 'find' => false, 'autre' => false ) );
			
			$urlmenu = $params['urlmenu'];

			$Controller->set( compact( 'results', 'dossierMenu', 'messages', 'addEnabled', 'personne_id', 'options', 'cui_id', 'urlmenu', 'etatdossiercui66', 'ajoutPossible' ) );
			$Controller->view = $params['view'];
		}
		
		public function view( $id = null, $params = array() ) {
			$Controller = $this->_Collection->getController();

			$params += array(
				'modelClass' => $Controller->modelClass,
				'view' => 'view',
				'urlmenu' => "/{$Controller->name}/index/#personne_id#"
			);
			$Model = $Controller->{$params['modelClass']};

			$data = $Model->find( 'first', 
				array(
					'fields' => array( 'Cui.personne_id', 'Cui66.id', 'Cui.id', 'Cui.personne_id' ),
					'conditions' => array( "{$params['modelClass']}.id" => $id ),
					'joins' => array(
						$Model->join( 'Cui66' ),
						$Model->Cui66->join( 'Cui' )
					)
				)
			);
			$personne_id = Hash::get( $data, 'Cui.personne_id' );
			$cui66_id = Hash::get( $data, 'Cui66.id' );
			$cui_id = Hash::get( $data, 'Cui.id' );

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );

			$params['urlmenu'] = Inflector::underscore( DefaultUtility::evaluate( $data, $params['urlmenu'] ) );

			$query = $Model->queryView( $id );
			$result = $Model->find( 'first', $query );
			if ( isset($result['Immersionromev3']) ){
				foreach( array_keys( $Model->Immersioncui66->Immersionromev3->belongsTo ) as $romev3Alias ) {
					$result['Immersionromev3'][$romev3Alias] = $result[$romev3Alias];
					unset( $result[$romev3Alias] );
				}
			}
			$Controller->request->data = $result;

			$options = $Model->options( array( 'allocataire' => true, 'find' => false, 'autre' => true ) );

			$urlmenu = $params['urlmenu'];

			$Controller->set( compact( 'options', 'personne_id', 'dossierMenu', 'urlmenu', 'cui_id' ) );
			$Controller->view = $params['view'];
		}
		
		public function addEdit( $id = null, $params = array() ) {
			$this->initAccess();
			$Controller = $this->_Collection->getController();

			$params += array(
				'modelClass' => $Controller->modelClass,
				'redirect' => "/{$Controller->name}/index/#personne_id#",
				'view' => 'edit',
				'urlmenu' => "/{$Controller->name}/index/#personne_id#"
			);
			$Model = $Controller->{$params['modelClass']};

			if( $Controller->action == 'add' ) {
				$cui_id = $id;				
				$id = null;
				$data = $Model->Cui66->find(
					'first', 
					array(
						'fields' => array( 'Cui.personne_id', 'Cui66.id', 'Cui.id' ),
						'conditions' => array( 'Cui.id' => $cui_id ),
						'joins' => array(
							$Model->Cui66->join( 'Cui' )
						)
					)
				);
				$personne_id = Hash::get( $data, 'Cui.personne_id' );
				$cui66_id = Hash::get( $data, 'Cui66.id' );
				$this->WebrsaAccesses->setMainModel('Cui')->check(null, $personne_id);
			}
			else {
				$data = $Model->find( 'first', 
					array(
						'fields' => array( 'Cui.personne_id', 'Cui66.id', 'Cui.id' ),
						'conditions' => array( "{$params['modelClass']}.id" => $id ),
						'joins' => array(
							$Model->join( 'Cui66' ),
							$Model->Cui66->join( 'Cui' )
						)
					)
				);
				$personne_id = Hash::get( $data, 'Cui.personne_id' );
				$cui66_id = Hash::get( $data, 'Cui66.id' );
				$cui_id = Hash::get( $data, 'Cui.id' );
				$this->WebrsaAccesses->setMainModel($params['modelClass'])->check($id, $personne_id);
			}

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );

			$params['redirect'] = DefaultUrl::toArray( DefaultUtility::evaluate( $data, $params['redirect'] ) );
			$params['urlmenu'] = Inflector::underscore( DefaultUtility::evaluate( $data, $params['urlmenu'] ) );

			// Retour à l'index en cas d'annulation
			if( isset( $Controller->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
				$Controller->redirect( $params['redirect'] );
			}

			if( !empty( $Controller->request->data ) ) {
				$Model->begin();
				if( $Model->saveAddEditFormData( $Controller->request->data, $this->Session->read( 'Auth.User.id' ) ) ) {
					$Model->commit();
					$Model->Cui66->WebrsaCui66->updatePositionsCuisById( $cui_id );
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
				$Controller->request->data = $Model->prepareAddEditFormData( $cui66_id, $id, $this->Session->read( 'Auth.User.id' ) );
			}

			$options = $Model->options( array( 'allocataire' => true, 'find' => true, 'autre' => true ) );

			$urlmenu = $params['urlmenu'];

			$Controller->set( compact( 'options', 'personne_id', 'dossierMenu', 'urlmenu' ) );
			$Controller->view = $params['view'];
		}
		
		public function delete( $id = null, $params = array() ) {
			$this->initAccess();
			$Controller = $this->_Collection->getController();

			$params += array(
				'modelClass' => $Controller->modelClass,
			);
			$Model = $Controller->{$params['modelClass']};
			
			$data = $Model->find( 'first',
				array(
					'fields' => array( 'Cui.personne_id', 'Cui.id' ),
					'recursive' => -1,
					'conditions' => array( $params['modelClass'] . '.id' => $id ),
					'joins' => array( 
						$Model->join( 'Cui66' ),
						$Model->Cui66->join( 'Cui' ),
					)
				)
			);
			
			$personne_id = Hash::get( $data, 'Cui.personne_id' );
			$cui_id = Hash::get( $data, 'Cui.id' );
			$this->WebrsaAccesses->setMainModel($params['modelClass'])->check($id, $personne_id);
			
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );

			$Model->begin();
			$success = $Model->delete($id);
			$Model->_setFlashResult('Delete', $success);

			if ($success) {
				$Model->commit();
				$Model->Cui66->WebrsaCui66->updatePositionsCuisById( $cui_id );
			} else {
				$Model->rollback();
			}
			$this->Jetons2->release($dossierMenu['Dossier']['id']);
			$Controller->redirect($Controller->referer());
		}
		
		
		/**
		 * On lui donne l'id du Modèle lié au CUI et il renvoi le PDF
		 * 
		 * @param integer $id
		 * @param string $modeleOdt
		 * @return PDF
		 */
		protected function _getCuiPdf( $id, $modeleOdt = null, $options = null ){
			$Controller = $this->_Collection->getController();
			$Model = $Controller->{$Controller->modelClass};
			
			$path = 
				$modeleOdt === null || !isset($Model->modelesOdt[$modeleOdt]) 
				? sprintf( $Model->modelesOdt['default'], $Model->alias )
				: sprintf( $Model->modelesOdt[$modeleOdt], $Model->alias )
			;
			
			$Model->forceVirtualFields = true;
			$Model->Cui66->forceVirtualFields = true;
			
			$queryImpressionCui66 = $Model->Cui66->WebrsaCui66->queryImpression();

			$queryImpressionCui66['fields'] = array_merge( $queryImpressionCui66['fields'], $Model->fields() );
			$queryImpressionCui66['joins'][] = $Model->Cui66->join( $Model->alias, array( 'type' => 'INNER' ) );
			$queryImpressionCui66['conditions']["{$Model->alias}.{$Model->primaryKey}"] = $id;

			$Model->Cui66->Cui->forceVirtualFields = true;
			$dataCui66 = $Model->Cui66->Cui->find( 'first', $queryImpressionCui66 );

			$data = $Model->Cui66->WebrsaCui66->completeDataImpression( $dataCui66 );
			
			$options = array_merge(
				$Model->options(),
				$Model->Cui66->WebrsaCui66->options()
			);

			$result = $Model->ged(
				$data,
				$path,
				true,
				$options
			);
			
			return $result;
		}
		
		/**
		 * Méthode générique d'impression d'un Modèle lié au CUI.
		 * 
		 * @param integer $id
		 * @param string $modeleOdt
		 */
		public function impression( $id, $modeleOdt = null ){
			$this->initAccess();
			$Controller = $this->_Collection->getController();
			$Model = $Controller->{$Controller->modelClass};
			
			// On vérifi que les méthodes et les propriétés sont bien défini et que le modele demandé existe bien (null == 'default')
			if ( 
				!property_exists($Model, 'modelesOdt') 
				|| !isset($Model->modelesOdt['default']) 
				|| ($modeleOdt !== null && !isset($Model->modelesOdt[$modeleOdt])) 
			){
				$this->Session->setFlash('modelesOdt n\'existe pas dans ' . $Model->alias);
				throw new NotImplementedException('modelesOdt n\'existe pas dans ' . $Model->alias );
			}
			
			// On vérifi que Gedooo est bien dans le model
			if( $Model->Behaviors->attached( 'Gedooo.Gedooo' ) === false ) {
				$Model->Behaviors->attach( 'Gedooo.Gedooo' );
			}
			
			$query = array(
				'fields' => array(
					'Cui.personne_id',
					'Cui.id'
				),
				'joins' => array(
					$Model->join( 'Cui66' ),
					$Model->Cui66->join( 'Cui' )
				),
				'conditions' => array(
					$Model->alias . '.id' => $id
				)
			);
			$result = $Model->find( 'first', $query );
			
			$personne_id = Hash::get( $result, 'Cui.personne_id' );
			$cui_id = Hash::get( $result, 'Cui.id' );
			$this->WebrsaAccesses->setMainModel($Controller->modelClass)->check($id, $personne_id);
			
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $personne_id ) );

			$pdf = $this->_getCuiPdf( $id, $modeleOdt );

			if( !empty( $pdf ) ) {
				$pdfSuffix = $modeleOdt === null ? $Model->alias : $Model->alias . '-' . $modeleOdt;
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'cui_%s_%d_%d-%s.pdf', $pdfSuffix, $cui_id, $id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer le PDF.', 'default', array( 'class' => 'error' ) );
				$Controller->redirect( $Controller->referer() );
			}
		}
		
		/**
		 * On lui donne l'id d'un modèle lié au CUI et il retourne l'id du CUI
		 * 
		 * @param integer $id
		 * @return integer
		 */
		public function getCuiId( $id ){
			$Controller = $this->_Collection->getController();
			$Model = $Controller->{$Controller->modelClass};
			
			$query = array(
				'fields' => array(
					'Cui66.cui_id'
				),
				'joins' => array(
					$Model->join( 'Cui66' )
				),
				'conditions' => array(
					$Model->alias . '.id' => $id
				),
			);
			$result = $Model->find( 'first', $query );
			
			return $result['Cui66']['cui_id'];
		}
	}
?>