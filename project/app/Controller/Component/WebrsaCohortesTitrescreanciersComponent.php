<?php
	/**
	 * Code source de la classe WebrsaCohortesTitrescreanciersComponent.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );
	App::uses( 'ZipUtility', 'Utility' );

	/**
	 * La classe WebrsaCohortesTitrescreanciersComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesTitrescreanciersComponent extends WebrsaAbstractCohortesComponent
	{
		/**
		 * Components utilisés par ce component
		 *
		 * @var array
		 */
		public $components = array( 'Allocataires', 'Gedooo.Gedooo', 'WebrsaRecherchesTitrescreanciers' );

		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params ) {
			return $this->WebrsaRecherchesTitrescreanciers->{__FUNCTION__}($params);
		}

		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 *
		 * @return array
		 */
		protected function _optionsRecords( array $params ) {
			return $this->WebrsaRecherchesTitrescreanciers->{__FUNCTION__}($params);
		}

		/**
		 * Retourne les noms des modèles dont des enregistrements seront mis en
		 * cache après l'appel à la méthode _optionsRecords() afin que la clé de
		 * cache générée par la méthode _options() se trouve associée dans
		 * ModelCache.
		 *
		 * @return array
		 */
		protected function _optionsRecordsModels( array $params ) {
			return $this->WebrsaRecherchesTitrescreanciers->{__FUNCTION__}($params);
		}

		public function exportFICA(array $params = array()){

			$Controller = $this->_Collection->getController();
			$defaults = array( 'limit' => false );
			$params = $this->_params( $params + $defaults );

			// Initialisation de la recherche
			$this->_initializeSearch( $params );

			// Récupération des valeurs du formulaire de recherche
			$filters = $this->_filters( $params );

			// Récupération du query
			$query = $this->_query( $filters, $params );
			$query = $this->_fireBeforeSearch( $params, $query );

			$Controller->{$params['modelName']}->forceVirtualFields = true;
			$results = $Controller->{$params['modelName']}->find( 'all', $query );

			if(true === is_array($results)) {
				$results = $this->_fireAfterSearch( $params, $results );

				//Initialisation
				$success = false;
				$Controller->Titrecreancier->begin();
				foreach( $results as $element  ) {
					$titrecreanciers_ids[] = $element['Titrecreancier']['id'];

					$data['Titrecreancier']['id'] = $element['Titrecreancier']['id'];
					$data['Titrecreancier']['etat'] = 'ATTRETOURCOMPTA';

					//Validation de la sauvegarde
					if( $Controller->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) ) {
						if( $Controller->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) ) {
							if($Controller->Historiqueetat->setHisto(
								$Controller->Titrecreancier->name,
								$data['Titrecreancier']['id'],
								$Controller->Titrecreancier->creanceId($data['Titrecreancier']['id']),
								__FUNCTION__,
								$data['Titrecreancier']['etat'],
								$Controller->Titrecreancier->foyerId($Controller->Titrecreancier->creanceId($data['Titrecreancier']['id']))
							)) {
								$success = true;
							} else {
								$success = false;
								break;
							}
						} else {
							$success = false;
							break;
						}
					} else {
						$success = false;
						break;
					}
				}

				if ( !$success ) {
					$Controller->Titrecreancier->rollback();
				}else{
					$infosFICA = $Controller->Titrecreancier->buildfica($titrecreanciers_ids);
					if( !empty($infosFICA[1]) ) {
						$csvfile = 'FICA'.Configure::read('Creances.FICA.NumAppliTiers').'.csv';
						$options = $Controller->Titrecreancier->options();
						$Controller->Titrecreancier->commit();
						$this->Flash->success( __( 'Save->success' ) );
						$Controller->set( compact( 'options','infosFICA','csvfile' ) );
						$Controller->layout = null;
						$Controller->render( "exportfica" );
					}
				}
			}

			if ( !$success ) {
				$this->Flash->error( __( 'Save->error' ) );
				$Controller->redirect( array( 'action' => 'cohorte_transmissioncompta', null ) );
			}
		}

		public function exportZIP(array $params = array()){

			$Controller = $this->_Collection->getController();
			$defaults = array( 'limit' => false );
			$params = $this->_params( $params + $defaults );

			// Initialisation de la recherche
			$this->_initializeSearch( $params );

			// Récupération des valeurs du formulaire de recherche
			$filters = $this->_filters( $params );

			// Récupération du query
			$query = $this->_query( $filters, $params );
			$query = $this->_fireBeforeSearch( $params, $query );

			$Controller->{$params['modelName']}->forceVirtualFields = true;
			$results = $Controller->{$params['modelName']}->find( 'all', $query );

			if(true === is_array($results)) {
				$results = $this->_fireAfterSearch( $params, $results );

				//Initialisation
				$success = false;
				$Controller->Titrecreancier->begin();
				foreach( $results as $element  ) {
					$titrecreanciers_ids[] = $element['Titrecreancier']['id'];

					$data['Titrecreancier']['id'] = $element['Titrecreancier']['id'];
					$data['Titrecreancier']['etat'] = 'ATTRETOURCOMPTA';

					//Validation de la sauvegarde
					if( $Controller->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) ) {
						if( $Controller->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) ) {
							if($Controller->Historiqueetat->setHisto(
								$Controller->Titrecreancier->name,
								$data['Titrecreancier']['id'],
								$Controller->Titrecreancier->creanceId($data['Titrecreancier']['id']),
								__FUNCTION__,
								$data['Titrecreancier']['etat'],
								$Controller->Titrecreancier->foyerId($Controller->Titrecreancier->creanceId($data['Titrecreancier']['id']))
							)) {
								$success = true;
							} else {
								$success = false;
								break;
							}
						} else {
							$success = false;
							break;
						}
					} else {
						$success = false;
						break;
					}
				}

				if ( !$success ) {
					$Controller->Titrecreancier->rollback();
				}else{
					$infosFICA = $Controller->Titrecreancier->buildfica($titrecreanciers_ids);
					if( !empty($infosFICA[1]) ) {
						$csvfile = 'FICA'.Configure::read('Creances.FICA.NumAppliTiers').'.csv';
						$csvpath = Configure::read('Cohorte.dossierTmpPdfs');

						//Build CSV fica sans .ctp :
						//Ouverture du fichier
						$handle = fopen ( $csvpath.'/'.$csvfile, 'x+' );
						if( $handle != false ){
							$written = true;
							//Remplissage du fichier
							foreach ($infosFICA as $key => $ligneFica){
								$written = fputcsv ( $handle, $ligneFica, ';');
								if (!$written){break;}
							}
							//Si le fichier Fica est bien crée
							if ( $written != false ){

								//Ensuite on initialise le ZIP
								$ZipUtility = new ZipUtility();
								$zipFileName = 'tmpZIP.zip';

								//On ajoute le CSV FICA au zip
								$contenu = file_get_contents($csvpath.'/'.$csvfile);
								$ZipUtility->add($contenu, $csvfile);

								//On ferme et on supprime le fichier FICA.
								fclose($handle);
								unlink($csvpath.'/'.$csvfile);

								//Pour chaque fichier à ajouter on fait: (donc pour le fica et tout les PJ)*/
								$filesFICA = $Controller->Titrecreancier->findfilesfica($titrecreanciers_ids);
								foreach ( $filesFICA as $key => $file){
									if ( is_null($file['Fichiermodule']['cmspath']) ){
										//Get contents from Fichiermodule
										$contents = $Controller->Titrecreancier->Fichiermodule->find(
											'first',
											array(
												'fields'=>'document',
												'recursive' => -1,
												'conditions' => array('id' => $file['Fichiermodule']['id'])
											)
										);
										if( !empty( $contents['Fichiermodule']['document']) ){
											$ZipUtility->add(
												$contents['Fichiermodule']['document'],
												$file['Fichiermodule']['name']
											);
										}
									}else{
										//Get contents from CMS
										$cmisFile = Cmis::read( $file['Fichiermodule']['cmspath'], true );
										if( !empty( $cmisFile['content'] ) ) {
											$ZipUtility->add(
												$cmisFile['content'],
												$file['Fichiermodule']['name']
											);
										}
									}
								}

								$Controller->Titrecreancier->commit();
								$this->Flash->success( __( 'Save->success' ) );

								//Génération finale du ZIP
								$zipFilePath = $ZipUtility->zip( $zipFileName );
								//On envoit le Zip à la vue
								$ZipUtility->sendZipToClient( $zipFilePath );

							}else{
								$Controller->Titrecreancier->rollback();
								$success = false;
							}
						}else{
							$Controller->Titrecreancier->rollback();
							$success = false;
						}
					}
				}
			}

			if ( !$success ) {
				$this->Flash->error( __( 'Save->error' ) );
				$Controller->redirect( array( 'action' => 'cohorte_transmissioncompta', null ) );
			}
		}

	}
