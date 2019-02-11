<?php
	/**
	 * Code source de la classe WebrsaRecherchesDossierspcgs66Component.
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesDossierspcgs66Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesDossierspcgs66Component extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$options = parent::_optionsEnums( $params );

			$options['Traitementpcg66']['courriersansmodele'] = array(
				0 => 'Non',
				1 => 'Oui'
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

			$options = parent::_optionsRecords( $params );

			if( !isset( $Controller->Catalogueromev3 ) ) {
				$Controller->loadModel( 'Catalogueromev3' );
			}

			$catalogueromev3 = $Controller->Catalogueromev3->dependantSelects();
			$options['Categorieromev3'] = $catalogueromev3['Catalogueromev3'];
			$options['Dossierpcg66']['originepdo_id'] = $Controller->Dossierpcg66->Originepdo->find('list');
			$options['Dossierpcg66']['typepdo_id'] = $Controller->Dossierpcg66->Typepdo->find('list');

			// Poles et gestionnaires PCG, en consultation
			$options['Dossierpcg66']['poledossierpcg66_id'] = $Controller->Dossierpcg66->User->Poledossierpcg66->WebrsaPoledossierpcg66->polesdossierspcgs66( false );
			$options['Dossierpcg66']['user_id'] = $Controller->Dossierpcg66->User->WebrsaUser->gestionnaires( false );

			$options['Decisiondossierpcg66']['org_id'] = $Controller->Dossierpcg66->Decisiondossierpcg66->Orgtransmisdossierpcg66->find(
				'list',
				array(
					'conditions' => array('Orgtransmisdossierpcg66.isactif' => '1'),
					'order' => array('Orgtransmisdossierpcg66.name ASC')
				)
			);
			$options['Traitementpcg66']['situationpdo_id'] = $Controller->Dossierpcg66->Personnepcg66->Situationpdo->find(
				'list',
				array(
					'order' => array('Situationpdo.libelle ASC'),
					'conditions' => array('Situationpdo.isactif' => '1')
				)
			);
			$options['Traitementpcg66']['statutpdo_id'] = $Controller->Dossierpcg66->Personnepcg66->Statutpdo->find(
				'list',
				array(
					'order' => array('Statutpdo.libelle ASC'),
					'conditions' => array('Statutpdo.isactif' => '1')
				)
			);
			$options['Decisiondossierpcg66']['decisionpdo_id'] = $Controller->Dossierpcg66->Decisiondossierpcg66->Decisionpdo->findForRecherche( 'list' );

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
					'Familleromev3',
					'Domaineromev3',
					'Metierromev3',
					'Appellationromev3',
					'Originepdo',
					'Typepdo',
					'Poledossierpcg66',
					'User',
					'Orgtransmisdossierpcg66',
					'Situationpdo',
					'Statutpdo',
					'Decisionpdo'
				)
			);

			return $result;
		}

		/**
		 * Surcharge en cas d'export csv pour transformer les listes en retour à la ligne
		 *
		 * @param array $params
		 * @param array $results
		 */
		public function afterSearch(array $params, array $results) {
			$results = parent::afterSearch($params, $results);

			if (strpos($this->_Collection->getController()->action, 'exportcsv') === 0) {
				foreach ($results as $key => $values) {
					foreach ($values as $modelName => $values) {
						foreach ($values as $fieldName => $value) {
							if (strpos($value, '<ul>') === 0) {
								$value = preg_replace('/\<\/?ul\>|\<li\>/', '', $value);
								$results[$key][$modelName][$fieldName] = trim(preg_replace('/\<\/li\>/', " - ", $value), ' -');
							}
						}
					}
				}
			}

			return $results;
		}
	}
?>