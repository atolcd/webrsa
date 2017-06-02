<?php
	/**
	 * Code source de la classe WebrsaRequestsmanagerComponent.
	 *
	 * @package app.Controller.Component
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Component.php.
	 */

	/**
	 * La classe WebrsaRequestsmanagerComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRequestsmanagerComponent extends Component
	{
		/**
		 * Permet de filtrer les options envoyées à la vue au moyen de la clé
		 * 'filters.accepted' dans le fichier de configuration.
		 *
		 * @param array $options
		 * @param array $params
		 * @return array
		 */
		public function optionsAccepted( array $options, array $params ) {
			$Controller = $this->_Collection->getController();
			
			if (isset($options['Tag']['valeurtag_id']) && !empty($options['Tag']['valeurtag_id'])) {
				$options['filter']['Tag']['valeurtag_id'] = $options['Tag']['valeurtag_id'];

				foreach( $options['Tag']['valeurtag_id'] as $title => $values ) {
					foreach (array_keys($values) as $id) {
						if (!in_array((string)$id, (array)Hash::get($Controller->request->data, 'Search.Tag.valeurtag_id'))) {
							unset($options['Tag']['valeurtag_id'][$title][$id]);
							if ( empty($options['Tag']['valeurtag_id'][$title]) ) {
								unset($options['Tag']['valeurtag_id'][$title]);
							}
						}
					}
				}
			}

			return $options;
		}
		
		/**
		 * Retourne un array avec clés de paramètres suivantes complétées en
		 * fonction du contrôleur:
		 *	- modelName: le nom du modèle sur lequel se fera la pagination
		 *	- modelRechercheName: le nom du modèle de moteur de recherche
		 *	- searchKey: le préfixe des filtres renvoyés par le moteur de recherche
		 *	- searchKeyPrefix: le préfixe des champs configurés
		 *	- configurableQueryFieldsKey: les clés de configuration contenant les
		 *    champs à sélectionner dans la base de données.
		 *  - auto: la recherche doit-elle être lancée (avec les valeurs par défaut
		 *    des filtres de recherche) automatiquement au premier accès à la page,
		 *    lors de l'appel à une méthode search() ou cohorte(). Configurable
		 *    avec Configure::write( 'ConfigurableQuery.<Controller>.<action>.query.auto' )
		 *
		 * @param array $params
		 * @return array
		 */
		public function params( array $params = array() ) {
			$Controller = $this->_Collection->getController();

			if ( Hash::get($Controller->request->data, 'Search.Requestmanager.name') ) {
				$result = ClassRegistry::init('Requestmanager')->find('first', 
					array( 
						'fields' => 'Requestmanager.model', 
						'conditions' => array(
							'Requestmanager.id' => Hash::get($Controller->request->data, 'Search.Requestmanager.name') 
						)
					)
				);
				$params['modelName'] = Hash::get($result, 'Requestmanager.model');
			}
			
			return $params;
		}
		
		/**
		 * Surcharge de _queryConditions permettant de modifier la configuration dans le cas d'une utilisation du Requestmanager
		 * 
		 * @param array $query
		 * @param array $filters
		 * @param array $params
		 * @return type
		 */
		public function queryConditions( array $query, array $filters, array $params ) {
			$Controller = $this->_Collection->getController();
			
			if ( Hash::get($Controller->request->data, 'Search.Requestmanager.name') ) {
				$config = Configure::read('ConfigurableQuery.'.$params['configurableQueryFieldsKey']);
				$actions = array();
				
				foreach ( $config['results']['fields'] as $key => $value ) {
					if (strpos((string)$key, '/') === 0 || (is_string($value) && strpos($value, '/') === 0)) {
						$actions[$key] = $value;
					}
				}
				
				$config['results']['fields'] = array_merge( $query['fields'], $actions );
				$config['query']['order'] = $query['order'];
				unset($config['results']['fields']['Dossier.locked']);
				
				Configure::write('ConfigurableQuery.'.$params['configurableQueryFieldsKey'], $config);
				
				// Force l'ajout de Foyer.id, Dossier.id et Personne.id si ils ne sont pas présent
				$fields = Hash::get($query, 'fields');
				$flippedFields = array_flip($fields);
				if ( !isset($fields['Foyer.id']) && !isset($flippedFields['Foyer.id']) ) {
					$query['fields'][] = 'Foyer.id';
				}
				if ( !isset($fields['Dossier.id']) && !isset($flippedFields['Dossier.id']) ) {
					$query['fields'][] = 'Dossier.id';
				}
				if ( !isset($fields['Personne.id']) && !isset($flippedFields['Personne.id']) ) {
					$query['fields'][] = 'Personne.id';
				}
			}
			
			return $query;
		}
	}
?>