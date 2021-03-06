<?php
	/**
	 * Code source de la classe WebrsaCohortesPlanpauvreterendezvousComponent.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesPlanpauvreterendezvousComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesPlanpauvreterendezvousComponent extends WebrsaAbstractCohortesComponent
	{

        /*
        * Modèles utilisés par ce modèle.
        *
        * @var array
        */
       public $uses = array(
           'Personne',
           'Historiqueetatpe',
           'Allocataire',
           'Canton',
       );

		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$options = parent::_optionsEnums( $params );
			$Controller = $this->_Collection->getController();

			if( !isset( $Controller->Planpauvreterendezvous ) ) {
				$Controller->loadModel( 'Planpauvreterendezvous' );
			}

			$options = array_merge(
				$options,
				$Controller->Planpauvreterendezvous->enums()
            );

			return $options;
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
				$options['Rendezvous']['typerdv_id'] = $Controller->Rendezvous->Typerdv->find('first', array(
					'recursive' => -1,
					'conditions' => array(
							'Typerdv.code_type' => $config['cohorte']['config']['save']['Typerdv.code_type']
						)
				) );
			}

			if( isset($config['cohorte']['config']['save']['Statutrdv.code_statut']) ) {
				$options['Rendezvous']['statutrdv_id'] = $Controller->Rendezvous->Statutrdv->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'Statutrdv.code_statut' => $config['cohorte']['config']['save']['Statutrdv.code_statut']
					)
				) );
			}

			return $options;
		}

		/**
		 * Retourne les noms des modèles dont des enregistrements seront mis en
		 * cache après l'appel à la méthode _optionsRecords() afin que la clé de
		 * cache générée par la méthode _options() se trouve associée dans
		 * ModelCache.
		 *
		 * @see _optionsRecords(), _options()
		 *
		 * @return array
		 */
		protected function _optionsRecordsModels( array $params ) {
			$result = array_merge(
				parent::_optionsRecordsModels( $params ),
				array(
					'Structurereferente',
					'Referent',
				)
			);

			return $result;
		}

		/**
		 * Surcharge de la méthode afterSearch pour du code spécifique
		 *
		 * @param type $params
		 * @param type $results
		 * @return array
		 */
		public function afterSearch( array $params, array $results ) {
			$results = parent::afterSearch( $params, $results );
			$Controller = $this->_Collection->getController();
			$Controller->loadModel('Modecontact');

			foreach ($results as $key => $result) {
				// AJout des numéros de téléphone de la CAF
				$modecontacts = $Controller->Modecontact->find (
					'all',
					array (
						'fields' => array( 'Modecontact.numtel' ),
						'recursive' => -1,
						'conditions' => array (
							'Modecontact.foyer_id' => $result['Foyer']['id'],
							'OR' => array(
								array('Modecontact.numtel LIKE \'06%\''),
								array('Modecontact.numtel LIKE \'07%\'')
							)
						)
					)
				);
				$results[$key]['Modecontact'] = array();
				foreach($modecontacts as $modecontact) {
					if( isset($modecontact['Modecontact']['numtel']) && !empty($modecontact['Modecontact']['numtel'])) {
						if( !isset($results[$key]['Modecontact']['numtel']) ) {
							$results[$key]['Modecontact']['numtel'] = '';
						}
						$results[$key]['Modecontact']['numtel'] .= $modecontact['Modecontact']['numtel'] .' ';
					}
				}
			}

			return $results;
		}
	}

?>