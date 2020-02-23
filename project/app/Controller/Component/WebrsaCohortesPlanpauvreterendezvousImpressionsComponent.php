<?php
	/**
	 * Code source de la classe WebrsaCohortesPlanpauvreterendezvousImpressionsComponent.
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
			if( !isset($query['fields']['Personne.id']) ) {
				array_unshift($query['fields'], 'DISTINCT ON ("Personne"."id") "Personne"."id" as "Personne__id"');
			}

			if (is_numeric ($Controller->params->named['Search__limit'])) {
				$query['limit'] = $Controller->params->named['Search__limit'];
			}

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

		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsRecords( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel('WebrsaOptionTag');

			$options = $Controller->WebrsaOptionTag->optionsRecords( parent::_optionsRecords( $params ) );
			if( !isset( $Controller->Planpauvreterendezvous ) ) {
				$Controller->loadModel( 'Planpauvreterendezvous' );
			}

			$config = Configure::read('ConfigurableQuery.Planpauvreterendezvous.'.$params['nom_cohorte']);
			$options['Rendezvous']['structurereferente_id'] = $Controller->Orientstruct->Structurereferente->listOptions();
			$options['Rendezvous']['permanence_id'] = $Controller->Orientstruct->Structurereferente->Permanence->listOptions();
			$options['Rendezvous']['referent_id'] = $Controller->InsertionsBeneficiaires->referents();

			if( isset($config['cohorte']['config']['save']['Typerdv.code_type']) ) {
				$options['Rendezvous']['typerdv_id'] = $Controller->Rendezvous->Typerdv->find(
					'first',
					array(
						'recursive' => -1,
						'conditions' => array(
							'Typerdv.code_type' => $config['cohorte']['config']['save']['Typerdv.code_type']
						)
					)
				);
			}

			if( isset($config['cohorte']['config']['save']['Statutrdv.code_statut']) ) {
				$options['Rendezvous']['statutrdv_id'] = $Controller->Rendezvous->Statutrdv->find(
					'first',
					array(
						'recursive' => -1,
						'conditions' => array(
							'Statutrdv.code_statut' => $config['cohorte']['config']['save']['Statutrdv.code_statut']
						)
					)
				);
			}

			return $options;
		}
	}