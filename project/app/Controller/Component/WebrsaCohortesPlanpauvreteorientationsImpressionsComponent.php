<?php
	/**
	 * Code source de la classe WebrsaCohortesPlanpauvreteorientationsImpressionsComponent.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesImpressionsComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesPlanpauvreterendezvousImpressionsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesPlanpauvreteorientationsImpressionsComponent extends WebrsaAbstractCohortesImpressionsComponent
	{

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
			if( !isset($query['fields']['Personne.id']) ) {
				array_unshift($query['fields'], 'DISTINCT ON ("Personne"."id") "Personne"."id" as "Personne__id"');
				$query['order'] = array('Personne.id');
			}

			if (isset($Controller->params->named['Search__limit']) && is_numeric ($Controller->params->named['Search__limit'])) {
				$query['limit'] = $Controller->params->named['Search__limit'];
			}

			// Ajout des champs virtuels pour le CD58
			if( Configure::read('Cg.departement') == 58 ) {
				// Suppression de l'ancien champs
				$key = array_search('Personne.etat_dossier_orientation', $query['fields']);
				unset($query['fields'][$key]);

				// Ajout du nouveau champs
				$Personne = ClassRegistry::init( 'Personne' );
				$sql = $Personne->WebrsaPersonne->vfEtapeDossierOrientation58();
				$query['fields']['Personne.etat_dossier_orientation'] = "{$sql} AS \"Personne__etat_dossier_orientation\"";
			}


			$datas = $Controller->{$params['modelName']}->find( 'all', $query );
			$pdfList = array();

			foreach ( $datas as $value ) {
				$pdfs = array();

				$pdfs[] = $Controller->Orientstruct->WebrsaOrientstruct->getDefaultPdf( $value['Orientstruct']['id'], $Controller->Session->read( 'Auth.User.id' ) );
				$pdfList[] = count($pdfs) > 1 ? $Controller->Gedooo->concatPdfs($pdfs, 'Orientstruct') : $pdfs[0];
				$results[]['Pdf']['document'] = $pdfList;
			}

			return $results;
		}
	}