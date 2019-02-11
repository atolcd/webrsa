<?php
	/**
	 * Fichier source de la classe XPaginator2Helper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'PaginatorHelper', 'View/Helper' );
	App::uses( 'SearchProgressivePagination', 'Search.Utility' );

	/**
	 * La classe XPaginator2Helper permet la traduction automatique des titres et
	 * l'ajout de classes aux liens de tris, et fournit une méthode permettant
	 * d'obtenir un bloc de pagination.
	 *
	 * @package app.View.Helper
	 */
	class XPaginator2Helper extends PaginatorHelper
	{
		/**
		 * Helpers utilisés par ce helper.
		 *
		 * @var array
		 */
		public $helpers = array( 'Xhtml', 'Html' );

		/**
		 * Generates a sorting link
		 *
		 * @param  string $title Title for the link.
		 * @param  string $key The name of the key that the recordset should be sorted.
		 * @param  array $options Options for sorting link. See #options for list of keys.
		 * @return string A link sorting default by 'asc'. If the resultset is sorted 'asc' by the specified
		 *  key the returned link will sort by 'desc'.
		 */
		public function sort( $title, $key = null, $options = array( ) ) {
			if( empty( $options ) ) {
				$options = array();
			}
			$options = array_merge( array( 'url' => array(), 'model' => null ), $options );
			$url = $options['url'];
			unset($options['url']);

			if (empty($key)) {
				$key = $title;
				$title = __( Inflector::humanize(preg_replace('/_id$/', '', $title)) );
			}
			$dir = 'asc';
			$sortKey = $this->sortKey($options['model']);
			$isSorted = ($sortKey === $key || $sortKey === $this->defaultModel() . '.' . $key);

			if ($isSorted && $this->sortDir($options['model']) === 'asc') {
				$dir = 'desc';
			}

			if (is_array($title) && array_key_exists($dir, $title)) {
				$title = $title[$dir];
			}

			// Add a sort class and a direction class (asc, desc) on the sorting link
			if( $isSorted ) {
				$options = $this->addClass( $options, "sort {$dir}" );
			}

			// Keep named params in url
			$params = Set::merge( Set::extract( $this->request->params, 'pass' ), Set::extract( $this->request->params, 'named' ) );
			foreach( array( 'page', 'sort', 'direction' ) as $unwanted ) {
				unset( $params[$unwanted] );
			}

			$url = array_merge(
				array( 'sort' => $key, 'direction' => $dir),
				$url,
				array( 'order' => null ),
				$params
			);

			return $this->link( $title, $url, $options );
		}

		/**
		 * Génère un bloc de pagination (nombre de résultats, liens).
		 *
		 * @param type $classname L'alias du modèle
		 * @param type $urlOptions Les options supplémentaires à passer dans l'url
		 * @param string $format Le format du texte concernant le nombre de résultats
		 * @return string
		 */
		public function paginationBlock( $classname, $urlOptions, $format = 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%' ) {
			$progressivePaginate = SearchProgressivePagination::enabled( $this->request->params['controller'], $this->request->params['action'] );
			$format = SearchProgressivePagination::paginatorHelperFormat( $this->request, $classname, $format );
			$options = array( 'model' => $classname );

			$this->options( array( 'url' => $urlOptions ) );
			$pagination = null;
			$pageCount = Set::classicExtract( $this->request->params, "paging.{$classname}.pageCount" );

			if( $pageCount >= 1 ) {
				$pagination = $this->Xhtml->tag ( 'p', $this->counter( array_merge( $options, array( 'format' => __( $format ) ) ) ), array( 'class' => 'pagination counter' ) );

				if( $pageCount > 1 ) {
					$links = array(
						$this->first( __( '<<' ), $options ),
						$this->prev( __( '<' ), $options ),
						$this->numbers( $options ),
						$this->next( __( '>' ), $options )
					);

					if( !$progressivePaginate ) {
						$links[] = $this->last( __( '>>' ), $options );
					}

					$links = implode( ' ', $links );
					$pagination .= $this->Xhtml->tag( 'p', $links, array( 'class' => 'pagination links' ) );
				}
			}

			return $pagination;
		}
	}
?>