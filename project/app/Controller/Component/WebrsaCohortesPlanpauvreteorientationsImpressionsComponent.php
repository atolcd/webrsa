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

			$datas = $Controller->Personne->find( 'all', $query );
			$pdfList = array();
			$success = true;

			foreach ( $datas as $value ) {
				$pdfs = array();

				$pdfs[] = $Controller->Orientstruct->WebrsaOrientstruct->getDefaultPdf( $value['Orientstruct']['id'], $Controller->Session->read( 'Auth.User.id' ) );
				$pdfList[] = count($pdfs) > 1 ? $Controller->Gedooo->concatPdfs($pdfs, 'Orientstruct') : $pdfs[0];
			}

			if ( $success ) {
				$results = $datas;
				$results[]['Pdf']['document'] = $pdfList;
            }

			return $results;
		}
    }