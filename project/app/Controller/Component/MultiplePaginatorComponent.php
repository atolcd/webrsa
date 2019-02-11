<?php
	/**
	 * Code source de la classe MultiplePaginatorComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe MultiplePaginatorComponent permet de paginer sur plusieurs modèles
	 * dans une même page, avec l'aide de l'objet JavaScript CakeTabbedPaginator
	 * (app/webroot/js/webrsa.cake.tabbed.paginator.js).
	 *
	 * L'idée est de paginer par modèle, donc si on pagine sur les modèles Foo et
	 * Bar, l'URL ressemblera à .../sort[Foo]:Personne.prenom/direction[Foo]:asc/sort[Bar]:Personne.nom/page[Bar]:2/direction[Bar]:desc
	 *
	 * @package app.Controller.Component
	 */
	class MultiplePaginatorComponent extends Component
	{
		/**
		 * Complète le querydata avec les param-tres nommés concernant le modèle
		 * sur lequel on souhaite paginer.
		 *
		 * Les paramètres de la requête concerneant ce modèle seront effacés (sort,
		 * direction et page).
		 *
		 * @param string $className
		 * @param array $query
		 * @return array
		 */
		protected function _querydata( $className, array $query ) {
			$Controller = $this->_Collection->getController();

			$query += array(
				'sort' => null,
				'direction' => null,
				'page' => null
			);

			foreach(array('sort', 'direction', 'page') as $key) {
				$value = Hash::get( $Controller->request->params, "named.{$key}.{$className}" );
				$query[$key] = $value;
				unset( $Controller->request->params['named'][$key][$className] );
			}

			return $query;
		}

		/**
		 * Pour chacun des querydata se trouvant dans $paginate sous la clé de
		 * leur modèle, on complète celui-ci avec les paramètres de pagination
		 * se trouvant dans l'URL et on nettoie l'URL.
		 *
		 * @param array $paginate
		 * @return array
		 */
		public function prepare( array $paginate ) {
			$Controller = $this->_Collection->getController();

			foreach( $paginate as $className => $query ) {
				$paginate[$className] = $this->_querydata( $className, $query );
			}

			unset(
				$Controller->request->params['named']['sort'],
				$Controller->request->params['named']['direction'],
				$Controller->request->params['named']['page']
			);

			return $paginate;
		}
	}
?>