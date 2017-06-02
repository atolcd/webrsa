<?php
	/**
	 * Code source de la classe DefaultActionHelper.
	 *
	 * PHP 5.3
	 *
	 * @package Default
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'DefaultUrl', 'Default.Utility' );

	/**
	 * La classe DefaultActionHelper fournit des actions à utiliser avec la méthode
	 * DefaultDefaultHelper::actions().
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class DefaultActionHelper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Permissions'
		);

		/**
		 * Retourne un array à passer en paramètre de DefaultDefaultHelper::actions().
		 * Cette array comprend un lien de retour à la page précédente, permissions
		 * vérifiées.
		 *
		 * @param string $referer
		 * @param array $params
		 * @return array
		 */
		public function back( $referer = null, array $params = array() ) {
			$referer = ( !is_null( $referer ) ? $referer : $this->request->referer( true ) );
			$slashes = substr_count( $referer, '/' );
			if( $referer != '/' && $slashes == 1 ) {
				$referer = "{$referer}/index";
			}

			$enabled = ( !empty( $referer ) && ( $referer != '/' ) );

			if( $enabled ) {
				$referer = DefaultUrl::toArray( $referer );
				$path = DefaultUrl::toString( $referer );
				$enabled = $this->Permissions->check( $referer['controller'], $referer['action'] );
			}
			else {
				$path = '/Users/login/';
			}

			$default = array(
				'text' => 'Retour',
				'msgid' => 'Retour à la page précédente',
				'enabled' => $enabled,
				'class' => 'back'
			);

			return array(
				$path => $default + $params
			);
		}
	}
?>