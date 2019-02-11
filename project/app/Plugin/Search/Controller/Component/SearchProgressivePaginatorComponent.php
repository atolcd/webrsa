<?php
	/**
	 * Code source de la classe SearchProgressivePaginatorComponent.
	 *
	 * PHP 5.3
	 *
	 * CakePHP 2.9.7
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 */
	App::uses( 'SearchBasicPaginatorComponent', 'Search.Controller/Component' );

	/**
	 * La classe Search.SearchProgressivePaginatorComponent permet d'optimiser la pagination en ne comptant pas le
	 * nombre total de résultats, mais en regardant si on a au moins un élément sur la page suivante.
	 *
	 * @todo rename SearchProgressivePaginator -> SearchSearchProgressivePaginator
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 */
	class SearchProgressivePaginatorComponent extends SearchBasicPaginatorComponent
	{
		/**
		 * Surcharge de la méthode paginate pour obtenir la pagination progressive
		 *
		 * @see CakePHP 2.9.7
		 *
		 * @param Model|string $object Model to paginate (e.g: model instance, or 'Model', or 'Model.InnerModel')
		 * @param string|array $scope Additional find conditions to use while paginating
		 * @param array $whitelist List of allowed fields for ordering. This allows you to prevent ordering
		 *   on non-indexed, or undesirable columns. See PaginatorComponent::validateSort() for additional details
		 *   on how the whitelisting and sort field validation works.
		 * @return array Model query results
		 * @throws MissingModelException
		 * @throws NotFoundException
		 */
		public function paginate( $object = null, $scope = array( ), $whitelist = array( ) ) {
			if( is_array( $object ) ) {
				$whitelist = $scope;
				$scope = $object;
				$object = null;
			}

			$object = $this->_getObject( $object );

			if( !is_object( $object ) ) {
				throw new MissingModelException( $object );
			}

			$options = $this->mergeOptions( $object->alias );
			$options = $this->validateSort( $object, $options, $whitelist );
			$options = $this->checkLimit( $options );

			$conditions = $fields = $order = $limit = $page = $recursive = null;

			if( !isset( $options['conditions'] ) ) {
				$options['conditions'] = array( );
			}

			$type = 'all';

			if( isset( $options[0] ) ) {
				$type = $options[0];
				unset( $options[0] );
			}

			extract( $options );

			if( is_array( $scope ) && !empty( $scope ) ) {
				$conditions = array_merge( $conditions, $scope );
			} elseif ( is_string( $scope ) ) {
				$conditions = array( $conditions, $scope );
			}
			if( $recursive === null ) {
				$recursive = $object->recursive;
			}

			$extra = array_diff_key( $options, compact(
							'conditions', 'fields', 'order', 'limit', 'page', 'recursive'
					) );
			if (!empty($extra['findType'])) {
				$type = $extra['findType'];
				unset($extra['findType']);
			}
			if( $type !== 'all' ) {
				$extra['type'] = $type;
			}

			if( (int)$page < 1 ) {
				$page = 1;
			}
			$page = $options['page'] = (int)$page;

			if( $object->hasMethod( 'paginate' ) ) {
				$results = $object->paginate(
						$conditions, $fields, $order, $limit, $page, $recursive, $extra
				);
			} else {
				$parameters = compact( 'conditions', 'fields', 'order', 'limit', 'page' );
				if( $recursive != $object->recursive ) {
					$parameters['recursive'] = $recursive;
				}

				// Début pagination progressive
				$query = array_merge( $parameters, $extra );
				$query['offset'] = ( max( 0, $page - 1 ) * $query['limit'] );
				$query['limit'] = ( $query['limit'] + 1 );
				$query['page'] = 1; // INFO: CakePHP 2.0
				$results = $object->find( $type, $query );

				$count = count( $results ) + ( ( $page - 1 ) * $limit );
				unset( $results[$query['limit'] - 1] );
				// Fin pagination progressive
			}
			$defaults = $this->getDefaults( $object->alias );
			unset( $defaults[0] );

			$pageCount = (int)ceil( $count / $limit );
			$requestedPage = $page;
			$page = max( min( $page, $pageCount ), 1 );

			$paging = array(
				'page' => $page,
				'current' => count( $results ),
				'count' => $count,
				'prevPage' => ($page > 1),
				'nextPage' => ($count > ($page * $limit)),
				'pageCount' => $pageCount,
				'order' => $order,
				'limit' => $limit,
				'options' => Hash::diff( $options, $defaults ),
				'paramType' => $options['paramType'],
				// Début pagination progressive
				'progressive' => true
				// Fin pagination progressive
			);
			if( !isset( $this->Controller->request['paging'] ) ) {
				$this->Controller->request['paging'] = array( );
			}
			$this->Controller->request['paging'] = array_merge(
					(array)$this->Controller->request['paging'],
					array( $object->alias => $paging )
			);

			if ($requestedPage > $page) {
				throw new NotFoundException();
			}

			if(!in_array( 'Paginator', $this->Controller->helpers ) &&
					!array_key_exists( 'Paginator', $this->Controller->helpers )
			) {
				$this->Controller->helpers[] = 'Paginator';
			}
			return $results;
		}
	}
?>