<?php
	/**
	 * Code source de la classe MenuHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe MenuHelper fournit des méthodes facilitant la construction de
	 * menus sous forme de liste non ordonnées (ul) imbriquées tout en vérifiant
	 * les permissions des différentes URLs grâce à la classe PermissionsHelper.
	 *
	 * @package app.View.Helper
	 */
	class MenuHelper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array( 'Html', 'Permissions' );

		/**
		 * Permet de construire un menu à plusieurs niveaux en tenant compte des
		 * permissions vérifiées grâce au PermissionsHelper.
		 *
		 * <pre>
		 * Si les permissions sont:
		 * array(
		 *	'Personnes:index' => true,
		 *	'Personnes:view' => false,
		 *	'Memos:index' => true,
		 * );
		 *
		 * $items = array(
		 *	'Composition du foyer' => array(
		 *		'url' => array( 'controller' => 'personnes', 'action' => 'index', 1 ),
		 *		'M. BUFFIN Christian' => array(
		 *			'url' => array( 'controller' => 'personnes', 'action' => 'view', 2 ),
		 *			'Mémos' => array(
		 *				'url' => array( 'controller' => 'memos', 'action' => 'index', 2 )
		 *			)
		 *		)
		 *	)
		 * );
		 *
		 * $this->Menu->make( $items );
		 *
		 * <ul>
		 *	<li class="branch">
		 *		<a href="/personnes/index/1">Composition du foyer</a>
		 *		<ul>
		 *			<li class="branch">
		 *				<span>M. BUFFIN Christian</span>
		 *				<ul>
		 *					<li class="leaf">
		 *						<a href="/memos/index/2">Mémos</a>
		 *					</li>
		 *				</ul>
		 *			</li>
		 *		</ul>
		 *	</li>
		 * </ul>
		 * </pre>
		 *
		 * @param array $items Les éléments du menu
		 * @return string
		 */
		public function make( $items ) {
			$return = '';

			foreach( $items as $key => $item ) {
				$sub = $item;
				unset( $sub['url'] );

				$sub = $this->make( $sub );

				$content = '';
				if( isset( $item['url'] ) && $this->Permissions->check( $item['url']['controller'], $item['url']['action'] ) ) {
					$content .= $this->Html->link( $key, $item['url'] ).$sub;
				}
				else if( !empty( $sub ) ) {
					$content .= $this->Html->tag( 'span', $key ).$sub;
				}

				$return .= empty( $content ) ? '' : $this->Html->tag( 'li', $content, array( 'class' => ( empty( $sub ) ? 'leaf' : 'branch' ) ) );
			}
			return empty( $return ) ? '' : $this->Html->tag( 'ul', $return );
		}

		/**
		 * Permet de construire un menu à plusieurs niveaux en tenant compte des
		 * permissions vérifiées grâce au PermissionsHelper.
		 *
		 * Cette méthode ajoute la possibilité de:spécifier la balise qui sera
		 * utilisée pour construire les éléments inactifs "parents" d'éléments
		 * actifs, d'ajouter un attribut title aux éléments et de désactiver un
		 * élément (et ses sous-éléments) en plus des permissions.
		 *
		 * <pre>
		 * Si les permissions sont:
		 * array(
		 *	'Personnes:index' => true,
		 *	'Personnes:view' => false,
		 *	'Memos:index' => true,
		 * );
		 *
		 * $items = array(
		 *	'Composition du foyer' => array(
		 *		'url' => array( 'controller' => 'personnes', 'action' => 'index', 1 ),
		 *		'M. BUFFIN Christian' => array(
		 *			'url' => array( 'controller' => 'personnes', 'action' => 'view', 2 ),
		 *			'Mémos' => array(
		 *				'url' => array( 'controller' => 'memos', 'action' => 'index', 2 ),
		 *				'title' => 'Mémos de M. BUFFIN Christian'
		 *			),
		 *			'Contrats' => array(
		 *				'url' => array( 'controller' => 'cers', 'action' => 'index', 2 ),
		 *				'disabled' => true,
		 *				'title' => 'CERs de M. BUFFIN Christian'
		 *			),
		 *		)
		 *	)
		 * );
		 *
		 * $this->Menu->make2( $items, 'a' );
		 *
		 * <ul>
		 *	<li class="branch">
		 *		<a href="/personnes/index/1">Composition du foyer</a>
		 *		<ul>
		 *			<li class="branch">
		 *				<a href="#">M. BUFFIN Christian</a>
		 *				<ul>
		 *					<li class="leaf">
		 *						<a href="/memos/index/2" title="Mémos de M. BUFFIN Christian">Mémos</a>
		 *					</li>
		 *				</ul>
		 *			</li>
		 *		</ul>
		 *	</li>
		 * </ul>
		 * </pre>
		 *
		 * @param array $items Les éléments du menu
		 * @param string $disabledTag La balise à utiliser pour les éléments parents inactifs
		 * @return string
		 */
		public function make2( $items, $disabledTag = 'span' ) {
			$return = '';
			foreach( $items as $key => $item ) {
				if( !isset( $item['disabled'] ) || !$item['disabled'] ) {
					$sub = $item;
					$title = ( isset( $sub['title'] ) ? $sub['title'] : false );
					$class = ( isset( $sub['class'] ) ? $sub['class'] : false );
					unset( $sub['url'], $sub['disabled'], $sub['title'], $sub['class'] );

					$sub = $this->make2( $sub, $disabledTag );

					$content = '';
					$htmlOptions = array( 'title' => $title );

					// Cas array('url' => array('controller' => 'moncontroller', 'action' => 'monaction'))
					if (isset($item['url']['controller']) && $item['url']['controller'] !== '/') {
						$controllerName = $item['url']['controller'];
						$actionName = isset($item['url']['action']) ? $item['url']['action'] : 'index';
					}

					// Cas array('url' => '/moncontroller/monaction')
					elseif (isset($item['url']) && is_string($item['url']) && preg_match('/^\/(?:([\w]+)\/([\w]+)|([\w]+))/', $item['url'], $matches)) {
						$controllerName = $matches[1];
						$actionName = isset($matches[2]) ? $matches[2] : 'index';
					}

					else {
						$controllerName = '/';
						$actionName = '/';
					}

					$permission = $controllerName === 'fluxcnaf' 
						|| $controllerName === '/'
						|| $actionName === '/'			// NOTE : envoyer des / à Permission->check pose problème
						|| $this->Permissions->check( $controllerName, $actionName );
					if( isset( $item['url'] ) && ( $item['url'] != '#' ) && $permission ) {
						$url = $item['url'];
						if( is_array( $url ) ) {
							$url += array( 'plugin' => null );
						}
						$content .= $this->Html->link( $key, $url, $htmlOptions + array( 'class' => $class ) ).$sub;
					}
					else if( !empty( $sub ) ) {
						$htmlOptions = array();
						if( $disabledTag == 'a' ) {
							$htmlOptions['href'] = '#';
						}
						$content .= $this->Html->tag( $disabledTag, $key, $htmlOptions ).$sub;
					}

					$return .= empty( $content ) ? '' : $this->Html->tag( 'li', $content, array( 'class' => ( empty( $sub ) ? 'leaf' : 'branch' ) ) );
				}
			}
			return empty( $return ) ? '' : $this->Html->tag( 'ul', $return );
		}
	}
?>