<?php
	/**
	 * Code source de la classe WebrsaCohortesApres66Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesApres66Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesApres66Component extends WebrsaAbstractCohortesComponent
	{
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			
			if( !isset( $Controller->Apre66 ) ) {
				$Controller->loadModel( 'Apre66' );
			}
			
			$options = parent::_optionsEnums( $params );
			$options = array_merge(
				$options,
				$Controller->Apre66->Aideapre66->enums()
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
			
			if( !isset( $Controller->Apre66 ) ) {
				$Controller->loadModel( 'Apre66' );
			}
			
			$options = parent::_optionsRecords( $params );
			$options['Aideapre66']['themeapre66_id'] = $Controller->Apre66->Aideapre66->Themeapre66->find( 'list' );
			$options['Aideapre66']['typeaideapre66_id'] = $Controller->Apre66->Aideapre66->Typeaideapre66->listOptions();
			$options['Apre66']['referent_id'] = $Controller->Apre66->Referent->find( 'list' );
			$options['Dernierreferent']['dernierreferent_id'] =
					$Controller->Apre66->Referent->Dernierreferent->listOptions();
			
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
					'Aideapre66',
					'Themeapre66',
					'Typeaideapre66',
					'Referent'
				)
			);

			return $result;
		}
		
		/**
		 * Surcharge de la fonction afin de permetre le préremplissage des champs montantaccorde malgrès que ceux-ci n'ont pas été envoyé
		 * 
		 * Ajoute des valeurs dans request->params, dans request->data et dans la session pour
		 * prendre en compte le changement de pages et le changement d'ordre d'affichage des résultats
		 * 
		 * @param array $params
		 * @param array $paramsSave paramètres à envoyer à saveCohorte()
		 * @return boolean
		 */
		protected function _traitementCohorte( array $params, array $paramsSave ) {
			$Controller = $this->_Collection->getController();
			$params = parent::_params( $params );
			
			if ( $Controller->action === 'cohorte_validation' && isset($Controller->request->data[$params['cohorteKey']]) ) {
				foreach ($Controller->request->data[$params['cohorteKey']] as $key => $value) {
					if ( !isset($value['Aideapre66']['montantaccorde']) ) {
						$Controller->request->data[$params['cohorteKey']][$key]['Aideapre66']['montantaccorde'] = $value['Aideapre66']['montantpropose'];
					}
				}
			}
			
			return parent::_traitementCohorte($params, $paramsSave);
		}
	}
?>