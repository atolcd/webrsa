<?php
	/**
	 * Code source de la classe WebrsaAbstractRecherchesComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractMoteursComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaAbstractRecherchesComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaAbstractRecherchesComponent extends WebrsaAbstractMoteursComponent
	{
		/**
		 *
		 * @param array $params
		 */
		final public function search( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$defaults = array( 'keys' => array( 'results.fields', 'results.innerTable' ) );
			$params = $this->_params( $params + $defaults );
			$this->_alwaysDo($params);

			// Si la recherche doit être effectuée
			if( $this->_needsSearch( $params ) ) {
				// Initialisation de la recherche
				$this->_initializeSearch( $params );

				// Récupération des valeurs du formulaire de recherche
				$filters = $this->_filters( $params );

				// Récupération du query
				$query = $this->_query( $filters, $params );

				// Exécution du query et assignation des résultats
				$Controller->{$params['modelName']}->forceVirtualFields = true;
				$query = $this->_fireBeforeSearch( $params, $query );
				$results = $this->Allocataires->paginate( $query, $params['modelName'] );
				$results = $this->_fireAfterSearch( $params, $results );

				$Controller->set( 'results', $results );
			}
			// Sinon
			else {
				// Récupération des valeurs par défaut des filtres
				$defaults = $this->_defaults( $params );

				// Assignation au formulaire
				$Controller->request->data = $defaults;

				// Si on doit automatiquement lancer la recherche, on met les filtres ar défaut dans l'URL
				if( $params['auto'] === true ) {
					return $this->_auto( $defaults, $params );
				}
			}

			// Récupération des options
			$options = $this->_options( $params );

			// Assignation à la vue
			$Controller->set( 'options', $options );
		}
	}
?>