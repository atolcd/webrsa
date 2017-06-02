<?php
	/**
	 * Code source de la classe WebrsaPermissionsHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * Compare les clés "label" (au moyen de la fonction strcmp) des array passées
	 * en paramètres.
	 * Retourne &lt; 0 si $a['label'] est plus petit que $b['label'], &gt; 0
	 * $a['label'] est plus grand que $b['label'] et 0 if si $a['label'] égal
	 * $b['label'].
	 *
	 * @param array $a
	 * @param array $b
	 * @return integer
	 */
	function strcmp_label_subkey( array $a, array $b ) {
		return strcmp( $a['label'], $b['label'] );
	}

	/**
	 * La classe WebrsaPermissionsHelper ...
	 *
	 * @package app.View.Helper
	 */
	class WebrsaPermissionsHelper extends AppHelper
	{

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array( 'Form', 'Html' );

		public $modelKey = 'Permission';

		protected $_images = null;

		protected function _insert( array $permissions, array $path, array $permission ) {
			if( 1 === count( $path ) ) {
				$permissions[$path[0]] = $permission;
			}
			else {
				if(false === isset($permissions[$path[0]]['children'])) {
					$permissions[$path[0]]['children'] = array();
				}
				$permissions[$path[0]]['children'] = $this->_insert(
					$permissions[$path[0]]['children'],
					array_slice( $path, 1 ),
					$permission
				);
			}

			return $permissions;
		}

		protected function _recursiveSort( array $data ) {
			uasort( $data, 'strcmp_label_subkey');

			foreach( $data as $key => $value ) {
				if( true === isset( $value['children'] ) ) {
					$data[$key]['children'] = $this->_recursiveSort( $value['children'] );
				}
			}

			return $data;
		}

		/**
		 * Retourne le com de la clé de cache qui sera utilisée par ce helper.
		 *
		 * @return string
		 */
		public function cacheKey() {
			return implode(
				'_',
				Hash::filter(
					array(
						Inflector::camelize( __class__ ),
						Inflector::camelize( $this->request->params['plugin'] ),
						Inflector::camelize( $this->request->params['controller'] ),
						$this->request->params['action'],
					)
				)
			);
		}

		/**
		 * Arbre remis sur un niveau
		 *
		 * @param array $data
		 * @return array
		 */
		protected function _flatAcosTree( array $data ) {
			$permissions = array();

			foreach( $data as $value ) {
				$children = (array)Hash::get( $value, 'children' );
				unset( $value['children'] );
				$permissions[] = $value;
				if( false === empty( $children ) ) {
					$permissions = array_merge( $permissions, $this->_flatAcosTree( $children ) );
				}
			}

			return $permissions;
		}

		/**
		 * Retourne un arbre des Acos, trié par libellé, chaque niveau pouvant
		 * posséder une clé "children".
		 *
		 * @param array $acos
		 * @return array
		 */
		public function acosTree( array $acos ) {
			$cacheKey = $this->cacheKey();
			$result = Cache::read( $cacheKey );

			if(false === $result) {
				$tree = array();

				foreach ($acos as $aco) {
					$tokens = explode( '/', $aco );

					$class = 'level'.( count($tokens) - 1 );
					foreach($tokens as $token) {
						$class .= ' aco_'.$token;
					}

					$label = __d( 'droit', $aco );

					$accu = array();
					foreach($tokens as $token) {
						$accu[] = $token;
					}

					$permission = array(
						'label' => $label,
						'path' => $aco,
						'class' => $class,
					);
					$tree = $this->_insert( $tree, $accu, $permission );
				}

				$sorted = $this->_recursiveSort( $tree );
				$result = $this->_flatAcosTree( $sorted );
			}

			return $result;
		}

		public function images() {
			if(null === $this->_images) {
				$this->_images = array(
					'yes' => $this->Html->image('icons/add.png', array('alt' => 'Oui')),
					'inherit_yes' => $this->Html->image('icons/add_disabled.png', array('alt' => 'Oui (hérité)')),
					'no' => $this->Html->image('icons/delete.png', array('alt' => 'Non')),
					'inherit_no' => $this->Html->image('icons/delete_disabled.png', array('alt' => 'Non (hérité)')),
					'inherit' => $this->Html->image('empty.png', array('alt' => 'Vide')),
					'plus' => $this->Html->image('icons/bullet_toggle_plus2.png', array('alt' => 'Étendre', 'style' => 'width: 12px; cursor: pointer;', 'onclick' => 'return toggleChildren(this);', 'class' => 'plus')),
					'minus' => $this->Html->image('icons/bullet_toggle_minus2.png', array('alt' => 'Réduire', 'style' => 'width: 12px; cursor: pointer;', 'onclick' => 'return toggleChildren(this);', 'class' => 'minus'))
				);
			}

			return $this->_images;
		}

		public function image( $key = null ) {
			$images = $this->images();
			return isset( $images[$key] )
				? $images[$key]
				: null;
		}

		protected function _actualClass( $value ) {
			switch($value) {
				case WebrsaPermissions::ACCES_OUI:
					return 'yes';
				case WebrsaPermissions::ACCES_NON:
					return 'no';
				case WebrsaPermissions::HERITE_OUI:
					return 'inherit_yes';
				case WebrsaPermissions::HERITE_NON:
					return 'inherit_no';
				default:
					return 'inherit';
			}
		}

		protected function _parentClass( $value ) {
			switch($value) {
				case WebrsaPermissions::ACCES_OUI:
				case WebrsaPermissions::HERITE_OUI:
					return 'yes';
				case WebrsaPermissions::ACCES_NON:
				case WebrsaPermissions::HERITE_NON:
					return 'no';
				default:
					return 'inherit';
			}
		}

		protected function _tbody( array $acos, array $params = array() ) {
			$params += array( 'parentPermissions' => array(), 'options' => array() );
			$result = '';

			foreach( $acos as $aco ) {
				$domId = $this->Html->domId(str_replace('/', '.', "{$this->modelKey}.{$aco['path']}"));

				$result .= '<tr class="'.$aco['class'].'">';
				// Intitulé
				$result .= $this->Html->tag(
					'th',
					$this->Html->tag( 'label', $aco['label'], array( 'for' => $domId ) ),
					array( 'class' => 'module' )
				);

				$value = Hash::get( $this->request->data, "{$this->modelKey}.{$aco['path']}" );

				// Permission du parent
				$parentValue = (int)Hash::get($params['parentPermissions'], $aco['path']);
				$parentClass = $this->_parentClass( $parentValue );
				$result .= $this->Html->tag( 'td', $this->image($parentClass), array( 'class' => "parent {$parentClass}" ) );

				// Permission actuelle
				$actualClass = $this->_actualClass( $value );
				$result .= $this->Html->tag( 'td', $this->image($actualClass), array( 'class' => "actual {$actualClass}" ) );

				// Permission demandée
				$select = $this->Form->input(
					"{$this->modelKey}.{$aco['path']}",
					array(
						'options' => (array)$params['options']['Permissions'],
						'empty' => false,
						'label' => false,
						'div' => false,
						'id' => $domId
					)
				);
				$result .= $this->Html->tag( 'td', $select );

				$result .= $this->Html->tag( 'td', ' ', array( 'class' => 'real' ) );
				$result .= '</tr>';
			}

			return $result;
		}

		public function table( array $acos, array $params = array() ) {
			return $this->Html->tag(
				'table',
				$this->Html->tag(
					'thead',
					$this->Html->tableHeaders(
						array(
							'Module',
							'Parent',
							'Actuelle',
							'Choix',
							'Effective',
						)
					)
				)
				.$this->Html->tag(
					'tbody',
					$this->_tbody( $acos, $params )
				),
				array( 'class' => 'permissions' )
			);
		}
	}
?>