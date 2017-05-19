<?php
	/**
	 * Code source de la classe DefaultHtmlHelper.
	 *
	 * PHP 5.4
	 *
	 * @package Default
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'HtmlHelper', 'View/Helper' );
	App::uses( 'Router', 'Routing' );

	/**
	 * La classe DefaultHtmlHelper étend la classe HtmlHelper de CakePHP dans le
	 * cadre de son utilisation dans le plugin Default.
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class DefaultHtmlHelper extends HtmlHelper
	{
		/**
		 * Helpers utilisés par ce helper.
		 *
		 * @var array
		 */
		public $helpers = array( 'Permissions' );

		/**
		 * Complète les options passées à la méthode link() lorsque l'$url est un
		 * array: ...
		 *
		 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
		 * @param array $options Array of HTML attributes.

		 * @return array
		 */
		protected function _linkOptions( $url = null, $options = array() ) {
			if( is_array( $url ) ) {
				$urlParams = $url + array(
					'plugin' => $this->request->params['plugin'],
					'controller' => $this->request->params['controller'],
					'action' => $this->request->params['action']
				);

				$disabled = Hash::get( $options, 'disabled' );
				$type = gettype( $disabled );

				$permission = $this->Permissions->check( $urlParams['controller'], $urlParams['action'] );
				if( $disabled === null ) {
					$disabled = !$permission;
				}
				else if( $type === 'boolean' ) {
					$disabled = ( $disabled || !$permission );
				}
				else {
					// TODO; throw new Exception
					debug( $type );
					die( __FILE__.':'.__LINE__ );
				}

				$options['disabled'] = $disabled;
			}

			return $options;
		}

		/**
		 * Surcharge de la méthode link de HtmlHelper afin d'ajouter des classes
		 * concernant le plugin éventuel, le prefix éventuel, le contrôleur et
		 * l'action dans le paramètre $options lorsque le paramètre $url est un
		 * array.
		 *
		 * @todo: vérifier automatiquement les droits, paramétrer, etc...
		 *
		 * @see _linkOptions()
		 *
		 * @param string $title The content to be wrapped by <a> tags.
		 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
		 * @param array $options Array of HTML attributes.
		 * @param string $confirmMessage JavaScript confirmation message.
		 * @return string An `<a />` element.
		 */
		public function link( $title, $url = null, $options = array( ), $confirmMessage = false ) {
			$options = $this->_linkOptions( $url, $options );

			if( is_array( $url ) ) {
				$tmp = $url;

				$default = array(
					'plugin' => $this->request->params['plugin'],
					'controller' => $this->request->params['controller'],
					'action' => $this->request->params['action'],
				);
				$tmp = Hash::merge( $default, $tmp );

				// Plugin, controller
				$options = $this->addClass( $options, $tmp['plugin'] );
				$options = $this->addClass( $options, $tmp['controller'] );

				// Action + prefix ?
				$prefix = Hash::get( $url, 'prefix' );
				if( !is_null( $prefix ) ) {
					$isPrefix = ( isset( $url[$prefix] ) ? $url[$prefix] : Hash::get( $this->request->params, $prefix ) );
					if( $isPrefix ) {
						$tmp['action'] = "{$prefix}_{$tmp['action']}";
					}
				}
				$options = $this->addClass( $options, $tmp['action'] );
			}

			$disabled = ( isset( $options['disabled'] ) ? $options['disabled'] : false );
			unset( $options['disabled'] );

			unset( $options['domain'] );

			if( !$disabled ) {
				return parent::link( $title, $url, $options, $confirmMessage );
			}
			else {
				$options = $this->addClass( $options, 'link disabled' );
				return $this->tag( 'span', $title, $options );
			}
		}
	}
?>