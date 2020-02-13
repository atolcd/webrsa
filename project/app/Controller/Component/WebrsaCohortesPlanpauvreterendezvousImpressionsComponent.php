<?php
	/**
	 * Code source de la classe WebrsaCohortesPlanpauvreterendezvousImpressionsComponent.
	 *
	 * PHP 5.3
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
	class WebrsaCohortesPlanpauvreterendezvousImpressionsComponent extends WebrsaAbstractCohortesImpressionsComponent
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

			foreach ( $datas as $value ) {
				$pdfs = array();

				$pdfs[] = $Controller->Rendezvous->WebrsaRendezvous->getDefaultPdf( $value['Rendezvous']['id'], $Controller->Session->read( 'Auth.User.id' ) );
				$pdfList[] = count($pdfs) > 1 ? $Controller->Gedooo->concatPdfs($pdfs, 'Rendezvous') : $pdfs[0];

				$results[]['Pdf']['document'] = $pdfList;
			}

			return $results;
		}
	}