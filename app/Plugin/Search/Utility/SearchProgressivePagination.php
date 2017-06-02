<?php
	/**
	 * Code source de la classe SearchProgressivePagination.
	 *
	 * PHP 5.3
	 *
	 * @package Search
	 * @subpackage Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe SearchProgressivePagination fournit des méthodes basiques pour
	 * la gestion de la pagination progressive.
	 *
	 * @package Search
	 * @subpackage Utility
	 */
	abstract class SearchProgressivePagination
	{
		/**
		 * Retourne le nom de la clé sous laquelle la configuration pour la pagination
		 * progressive est stockée.
		 *
		 * @param string $controller Le nom du contrôleur
		 * @param string $action Le nom de l'action
		 * @return string
		 */
		public static function configureKey( $controller = null, $action = null ) {
			// Pour ce contrôleur
			if( !is_null( $controller ) ) {
				// Pour ce contrôleur et cette action
				if( !is_null( $action ) ) {
					return 'Optimisations.'.Inflector::camelize( $controller ).'_'.$action.'.progressivePaginate';
				}

				return 'Optimisations.'.Inflector::camelize( $controller ).'.progressivePaginate';
			}

			// De manière générale
			return 'Optimisations.progressivePaginate';
		}

		/**
		 * Permet de savoir si la pagination progressive est activée.
		 *
		 * @see Xpaginator(2)Helper::paginationBlock(), AppController::_hasSearchProgressivePagination()
		 *
		 * @param string $controller
		 * @param string $action
		 * @return boolean
		 */
		public static function enabled( $controller = null, $action = null ) {
			$progressivePaginate = null;

			// Pagination progressive pour ce contrôleur et cette action ?
			if( !is_null( $controller ) && !is_null( $action ) ) {
				$progressivePaginate = Configure::read( self::configureKey( $controller, $action ) );
			}

			// Pagination progressive pour ce contrôleur ?
			if( is_null( $progressivePaginate ) && !is_null( $controller ) ) {
				$progressivePaginate = Configure::read( self::configureKey( $controller ) );
			}

			// Pagination progressive en général ?
			if( is_null( $progressivePaginate ) ) {
				$progressivePaginate = Configure::read( self::configureKey() );
			}

			return ( $progressivePaginate == true );
		}

		/**
		 * Active la pagination progressive.
		 *
		 * @param string $controller
		 * @param string $action
		 */
		public static function enable( $controller = null, $action = null ) {
			$configureKey = self::configureKey( $controller, $action );
			Configure::write( $configureKey, true );
		}

		/**
		 * Désactive la pagination progressive.
		 *
		 * @param string $controller
		 * @param string $action
		 */
		public static function disable( $controller = null, $action = null ) {
			$configureKey = self::configureKey( $controller, $action );
			Configure::write( $configureKey, false );
		}

		/**
		 * Retourne la pagination à utiliser dans une vue, pour une request donnée
		 * et pour un nom de modèle donné.
		 *
		 * @param CakeRequest $request
		 * @param string $classname
		 * @param string $format
		 * @return string
		 */
		public static function paginatorHelperFormat( CakeRequest $request, $classname, $format = 'Results %start% - %end% out of %count%.' ) {
			$page = Hash::get( $request->params, "paging.{$classname}.page" );
			$count = Hash::get( $request->params, "paging.{$classname}.count" );
			$limit = Hash::get( $request->params, "paging.{$classname}.limit" );

			$progressivePaginate = self::enabled( $request->params['controller'], $request->params['action'] );

			$longFormats = array(
				'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%',
				'Results %start% - %end% out of %count%.'
			);

			if( in_array( $format, $longFormats ) ) {
				if( ( $count > ( $limit * $page ) ) && $progressivePaginate ) {
					return self::format( true );
				}

				return self::format( false );
			}

			return $format;
		}

		/**
		 * Retourne le format de pagination par défaut à utiliser dans les vues,
		 * suivant que l'on utilise la pagination par défaut ou pas.
		 *
		 * @param boolean $progressive
		 * @return string
		 */
		public static function format( $progressive = false ) {
			if( $progressive ) {
				return 'Résultats %start% - %end% sur au moins %count% résultats.';
			}

			return __( 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%' );
		}
	}
?>