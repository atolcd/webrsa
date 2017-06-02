<?php
	/**
	 * Code source de la classe WebrsaCohortesDossierspcgs66ImpressionsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesImpressionsComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesDossierspcgs66ImpressionsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesDossierspcgs66ImpressionsComponent extends WebrsaAbstractCohortesImpressionsComponent
	{
		/**
		 * Components utilisés par ce component
		 *
		 * @var array
		 */
		public $components = array( 'Allocataires', 'Gedooo.Gedooo', 'WebrsaRecherchesDossierspcgs66' );
		
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params ) {
			return $this->WebrsaRecherchesDossierspcgs66->{__FUNCTION__}($params);
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
			return $this->WebrsaRecherchesDossierspcgs66->{__FUNCTION__}($params);
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
			return $this->WebrsaRecherchesDossierspcgs66->{__FUNCTION__}($params);
		}

		/**
		 * Modifie la requête pour ramener la clé primaire de l'enregistrement,
		 * le document PDF et le chemin cmspath dans les résultats.
		 *
		 * @param array $query
		 * @param array $params
		 * @return array
		 */
		protected function _queryPdfs( array $query, array $params ) {
			$Controller = $this->_Collection->getController();
			
			// Champs nécessaires
			$query['fields'] = array(
				'Dossierpcg66.id',
			);
			
			$query['limit'] = Configure::read($this->_configureKey('limit', $params));
			
			$dossierspcgs66_id = Hash::extract($Controller->{$params['modelName']}->find( 'all', $query ), '{n}.Dossierpcg66.id');
			$query = $Controller->Dossierpcg66->WebrsaDossierpcg66->getImpressionBaseQuery( $dossierspcgs66_id );

			return $query;
		}

		/**
		 * Retourne un array de PDF
		 *
		 * @param array $query
		 * @param array $params
		 * @return array
		 * @throws CakeException si $decisionPdf === null && empty($courriers)
		 */
		protected function _pdfs( array $query, array $params ) {
			$Controller = $this->_Collection->getController();

			$query = $this->_queryPdfs( $query, $params );

			$Controller->{$params['modelName']}->forceVirtualFields = true;
			$datas = $Controller->{$params['modelName']}->find( 'all', $query );
			$PdfUtility = new WebrsaPdfUtility();
			$pdfList = array();
			
			$success = true;
			
			foreach ( $datas as $value ) {
				$pdfs = array();
				
				$decisionsdossierspcgs66_id = Hash::get($value, 'Decisiondossierpcg66.id');
				$dossierpcg_id = Hash::get($value, 'Dossierpcg66.id');
				
				$decisionPdf = $decisionsdossierspcgs66_id !== null 
					? $Controller->Dossierpcg66->Decisiondossierpcg66->WebrsaDecisiondossierpcg66->getPdfDecision( $decisionsdossierspcgs66_id )
					: null
				;
				
				$courriers = $Controller->Dossierpcg66->Personnepcg66->Traitementpcg66->WebrsaTraitementpcg66->getPdfsByDossierpcg66Id( 
					$dossierpcg_id, $Controller->Session->read('Auth.User.id')
				);
				
				// Il faut au moins 1 PDF sinon il y a un problême
				if ( $decisionPdf === null && empty($courriers) ) {
					throw new CakeException(
						sprintf(
							"Il n'y a ni décisions ni courriers à imprimer pour la décision n°%s et le dossier PCG n°%s",
							$decisionsdossierspcgs66_id,
							$dossierpcg_id
						)
					);
				}
				
				if ( $decisionPdf !== null ) {
					$pdfs[] = $decisionPdf;
				}

				foreach ( $courriers as $i => $courrier ) {
					$pdfs[] = $courrier;
				}
				
				if ( Configure::read('Dossierspcgs66.imprimer_cohorte.Impression.RectoVerso') ) {
					$pdfs = $PdfUtility->preparePdfListForRectoVerso($pdfs);
				}
				
				$pdfList[] = count($pdfs) > 1 ? $Controller->Gedooo->concatPdfs($pdfs, 'Dossierpcg66') : $pdfs[0];
			}
			
			if ( $success ) {
				$results = $datas;
				$results['pdfs'] = $pdfList;
			}
			
			return $results;
		}
		
		/**
		 * Retourne la concaténation des pdfs contenus dans les résultats ou
		 * message d'erreur éventuel.
		 *
		 * @param boolean|array $results
		 * @param array $params
		 * @return string
		 */
		protected function _concat( $results, array $params ) {
			$Controller = $this->_Collection->getController();
			$PdfUtility = new WebrsaPdfUtility();
			
			if( !is_array( $results ) ) {
				$msgstr = "Erreur lors de l'impression en cohorte.";
				$Controller->Session->setFlash( $msgstr, 'flash/error' );
				$Controller->redirect( $Controller->referer() );
			}
			
			if ( Configure::read('Dossierspcgs66.imprimer_cohorte.Impression.RectoVerso') ) {
				$results['pdfs'] = $PdfUtility->preparePdfListForRectoVerso( $results['pdfs'] );
			}
			
			return $this->Gedooo->concatPdfs($results['pdfs'], 'Dossierpcg66');
		}

		/**
		 * Post-traitement des résultats de la requête (par exemple pour la mise
		 * à jour d'une date d'impression).
		 * Cette fonction doit retourner vrai pour que l'envoi se fasse.
		 *
		 * @param array $results
		 * @param array $params
		 * @return boolean
		 */
		protected function _postProcess( array $results, array $params ) {
			$Controller = $this->_Collection->getController();

			$Controller->{$params['modelName']}->begin();
			
			foreach ($results as $data) {
				// Si l'etat du dossier est decisionvalid on le passe en atttransmiop avec une date d'impression
				if ( Hash::get( $data, 'Dossierpcg66.etatdossierpcg' ) === 'decisionvalid' ) {
					$data['Dossierpcg66']['dateimpression'] = date('Y-m-d');
					$data['Dossierpcg66']['etatdossierpcg'] = 'atttransmisop';
					$Controller->Dossierpcg66->create($data['Dossierpcg66']);
					$success = $Controller->Dossierpcg66->save();
				}
			}
			
			$traitementspcgs66_ids = array();
			foreach ((array)Hash::extract($results, '{n}.Dossierpcg66.id') as $dossierpcg_id) {
				$query = $Controller->Dossierpcg66->Personnepcg66->Traitementpcg66->WebrsaTraitementpcg66->getPdfsQuery( $dossierpcg_id );
				$traitementspcgs66_ids = array_merge(
					$traitementspcgs66_ids,
					Hash::extract($Controller->Dossierpcg66->Foyer->find('all', $query), '{n}.Traitementpcg66.id')
				);
			}
			
			if (!empty($traitementspcgs66_ids)) {
				$success = $Controller->Dossierpcg66->Personnepcg66->Traitementpcg66->updateAllUnbound(
					array( 'etattraitementpcg' => "'attente'" ),
					array( 'id' => $traitementspcgs66_ids )
				);
			}
			
			if( $success ) {
				$Controller->{$params['modelName']}->commit();
			}
			else {
				$Controller->{$params['modelName']}->rollback();
			}

			return $success;
		}
	}
?>