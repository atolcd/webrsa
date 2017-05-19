<?php
	/**
	 * Code source de la classe WebrsaSearchesAccessesComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe WebrsaSearchesAccessesComponent ...
	 *
	 * Les clés nécessaires dans params, et dont la valeur par défaut est caclulée
	 * par la méthode completeParams sont:
	 *	- webrsaClassName
	 *	- accessClassName
	 *	- webrsaAccessParentId
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaSearchesAccessesComponent extends Component
	{
		/**
		 * Complète les paramètres en ajoutant les valeurs des clés webrsaClassName,
		 * accessClassName et webrsaAccessParentId à partir de la valeur de la
		 * clé modelName.
		 *
		 * @param array $params
		 * @return array
		 */
		public function completeParams( array $params ) {
			$params += array(
				'webrsaClassName' => 'Webrsa'.$params['modelName'],
				'accessClassName' => 'WebrsaAccess'.Inflector::camelize( Inflector::pluralize( Inflector::underscore( $params['modelName'] ) ) ),
				'webrsaAccessParentId' => $params['modelName'].'.personne_id'
			);

			return $params;
		}

		/**
		 * Complète le querydata afin de pouvoir calculer les droits d'accès aux
		 * enregistrements.
		 *
		 * @param array $params Les paramètres de la recherche
		 * @param array $query Le querydata à compléter
		 * @return array
		 */
		public function completeQuery( array $params, array $query ) {
			$Controller = $this->_Collection->getController();

			// On complète le querydata avec les éléments permettant de calculer les permissions sur les actions
			if( false === isset( $Controller->{$params['webrsaClassName']} ) ) {
				$Controller->loadModel( $params['webrsaClassName'] );
			}

			return $Controller->{$params['webrsaClassName']}->completeVirtualFieldsForAccess( $query );
		}

		/**
		 * Complète les resultats avec les droits d'accès aux enregistrements.
		 *
		 * @param array $params Les paramètres de la recherche
		 * @param array $results Les enregistrements à compléter
		 * @return array
		 */
		public function completeResults( array $params, array $results ) {
			$Controller = $this->_Collection->getController();

			App::uses( $params['accessClassName'], 'Utility' );
			$paramsActions = call_user_func( array( $params['accessClassName'], 'getParamsList' ) );

			foreach( $results as $key => $result ) {
				$parent_id = Hash::get( $result, $params['webrsaAccessParentId'] );
				$paramsAccess = $Controller->{$params['webrsaClassName']}->getParamsForAccess( $parent_id, $paramsActions );

				$results[$key] = call_user_func(
					array( $params['accessClassName'], 'access' ),
					$result,
					$paramsAccess
				);
			}

			return $results;
		}
	}
?>