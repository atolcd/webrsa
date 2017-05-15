<?php
	/**
	 * Code source de la classe SearchPaginatorComponent.
	 *
	 * PHP 5.3
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'SearchProgressivePagination', 'Search.Utility' );

	/**
	 * La classe SearchPaginatorComponent fournit des méthodes utilitaires de
	 * pagination, notamment la pagination progressive.
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 */
	class SearchPaginatorComponent extends Component
	{
		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array( );

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array( );

		/**
		 * Pagination standard ou progressive.
		 *
		 * @param array|string|Model $object
		 * @param array $scope
		 * @param array $whitelist
		 * @param boolean $progressivePaginate Permet de forcer la pagination progressive
		 * @return array
		 */
		public function paginate( $object = null, $scope = array( ), $whitelist = array( ), $progressivePaginate = null ) {
			$Controller = $this->_Collection->getController();

			$Paginator = $this->getPaginator( $progressivePaginate );

			return $Paginator->paginate( $object, $scope, $whitelist );
			/*if( is_null( $progressivePaginate ) ) {
				$progressivePaginate = SearchProgressivePagination::enabled( $Controller->name, $Controller->action );
			}

			if( $progressivePaginate ) {
				SearchProgressivePagination::enable( $Controller->name, $Controller->action );
				return $this->_Collection->load( 'Search.ProgressivePaginator', $Controller->paginate )->paginate( $object, $scope, $whitelist );
			}
			else {
				SearchProgressivePagination::disable( $Controller->name, $Controller->action );
				return $this->_Collection->load( 'Paginator', $Controller->paginate )->paginate( $object, $scope, $whitelist );
			}*/
		}

		// TODO: tests
		public function getPaginator( $progressivePaginate = null ) {
			$Controller = $this->_Collection->getController();

			if( is_null( $progressivePaginate ) ) {
				$progressivePaginate = SearchProgressivePagination::enabled( $Controller->name, $Controller->action );
			}

			if( $progressivePaginate ) {
				SearchProgressivePagination::enable( $Controller->name, $Controller->action );
				return $this->_Collection->load( 'Search.ProgressivePaginator', $Controller->paginate );
			}
			else {
				SearchProgressivePagination::disable( $Controller->name, $Controller->action );
				return $this->_Collection->load( 'Search.BasicPaginator', $Controller->paginate );
			}
		}

		/**
		 * Permet de remplir l'attribut order d'un querydata, en l'absence de
		 * pagination (pour utiliser sur un simple find), grâce aux paramètres
		 * de pagination passés dans l'URL.
		 *
		 * @param array $querydata
		 * @return array
		 */
		public function setPaginationOrder( $querydata ) {
			$Controller = $this->_Collection->getController();

			if( isset( $Controller->request->params['named']['sort'] ) && isset( $Controller->request->params['named']['direction'] ) ) {
				$querydata['order'] = array( "{$Controller->request->params['named']['sort']} {$Controller->request->params['named']['direction']}" );
			}

			return $querydata;
		}
	}
?>