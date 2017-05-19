<?php
	/**
	 * Fichier source de la classe MenuComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe MenuComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class MenuComponent extends Component
	{

		public $components = array( 'Acl', 'Droits' );

		/**
		 *
		 * @param type $varNameMenu
		 * @param type $aro
		 * @return type
		 */
		public function load( $varNameMenu, $aro = null ) {
			// lecture du menu du fichier menu.ini.php
			include(CONFIGS.'menu.ini.php');
			$menuRet = ${$varNameMenu};

			// chargement des droits
			if( !empty( $aro ) )
				$this->_filtreMenuDroits( $menuRet, $aro );

			return $menuRet;
		}

		/**
		 *
		 * @param type $menu
		 * @param type $aro
		 */
		protected function _filtreMenuDroits( &$menu, $aro ) {
			$items = $menu['items'];
			foreach( $items as $title => $menuItem ) {
				// Calcul du aco en fonction du lien
				$aco = $this->_calcAction( $menuItem['link'] );

				// Vérifie les droits
				if( $this->Acl->Check( $aro, $aco, "*" ) ) {
					// sous-menu
					if( array_key_exists( 'subMenu', $menuItem ) and
							is_array( $menuItem['subMenu'] ) and count( $menuItem['subMenu'] ) > 0 ) {
						$this->_filtreMenuDroits( $menu['items'][$title]['subMenu'], $aro );
					};
				} else
					unset( $menu['items'][$title] );
			};
		}

		/**
		 * Retourne le couple Controler:action pour le lien passé en paramétre
		 *
		 * @param type $lien
		 * @return string
		 */
		protected function _calcAction( $lien ) {

			// Traitement du lien
			if( empty( $lien ) or $lien == '/' )
				return 'Pages:home';
			else {
				if( $lien[0] == '/' )
					$lien = substr( $lien, 1 );
				else
					$lien = 'pages/'.$lien;
				$tabAction = explode( '/', $lien );
				$tabAction[0] = ucwords( $tabAction[0] );
				if( count( $tabAction ) == 1 )
					$tabAction[] = 'index';
				return $tabAction[0].':'.$tabAction[1];
			};
		}

		/**
		 * Retourne la valeur $key du menu $menu si elle existe et si elle et non null,
		 * et retourne $default dans le cas contraire.
		 *
		 * @return
		 *
		 * @param array $menu Données du menu
		 * @param str $key Nom de la valeur é traiter
		 * @param str $default Valeur par défaut
		 * @access private
		 */
		protected function _getArrayValue( $menu, $key, $default = null ) {
			if( !array_key_exists( $key, $menu ) )
				return $default;

			if( is_array( $menu[$key] ) ) {
				if( count( $menu[$key] ) < 1 )
					return $default;
				return $menu[$key][0] ? $menu[$key][0] : $default;
			};

			return $menu[$key] ? $menu[$key] : $default;
		}

		/**
		 *
		 * @return type
		 */
		public function listeAliasMenuControlleur() {
			// liste des alias du menu
			$listeAliasMenu = $this->_listeAliasMenu( $this->load( 'menu' ) );

			// Ajout des actions de tous les controllers du projet qui ne sont pas référencées par le menu
			$listeAliasCtrl = array( );

			$controllerList = App::objects( 'controller' );
			sort( $controllerList );

			foreach( $controllerList as $controllerName ) {
				$controllerNameShort = preg_replace( '/Controller$/', '', $controllerName );
				if( !in_array( $controllerNameShort, array( 'App') ) ) {
					$listeActions = $this->Droits->listeActionsControleur( $controllerName );
					// Liste des actions de ce controlleur qui ne sont pas dans le menu
					$actionPlus = array( );
					$ctrl_alias = 'Module:'.$controllerNameShort;
					foreach( $listeActions as $action ) {
						$trouve = false;
						$aliasAction = $controllerNameShort.':'.$action;
						foreach( $listeAliasMenu as $aliasMenu )
							if( $aliasAction === $aliasMenu['alias'] ) {
								$trouve = true;
								break;
							}
						if( !$trouve )
							$actionPlus[] = array( 'alias' => $aliasAction, 'parent_alias' => $ctrl_alias );
					}
					if( !empty( $actionPlus ) ) {
						$listeAliasCtrl[] = array( 'alias' => $ctrl_alias, 'parent_alias' => '' );
						$listeAliasCtrl = array_merge( $listeAliasCtrl, $actionPlus );
					}
				}
			}

			return array_merge( $listeAliasMenu, $listeAliasCtrl );
		}

		/**
		 * Fonction récursive du parcours des entrées du menu
		 *
		 * @param type $menu
		 * @param type $parentAlias
		 * @return type
		 */
		protected function _listeAliasMenu( $menu, $parentAlias = '' ) {
			// Initialisation
			$ret = array( );

			$items = $menu['items'];
			foreach( $items as $title => $menuItem ) {
				// Calcul de l'alias aco en fonction du lien
				$alias = $this->_calcAction( $menuItem['link'] );
				$ret[] = array( 'alias' => $alias, 'parent_alias' => $parentAlias );
				// sous-menu
				if( array_key_exists( 'subMenu', $menuItem ) and
						is_array( $menuItem['subMenu'] ) and
						count( $menuItem['subMenu'] ) > 0 )
					$ret = array_merge( $ret, $this->_listeAliasMenu( $menu['items'][$title]['subMenu'], $alias ) );
			}
			return $ret;
		}

		/**
		 * Returns an array of filenames of PHP files in given directory.
		 *
		 * @param  string $path Path to scan for files
		 * @return array  List of files in directory
		 */
		protected function _listClasses( $path, $filtre = '' ) {
			$dir = opendir( $path );
			$classes = array( );
			while( false !== ($file = readdir( $dir )) ) {
				if( (substr( $file, -3, 3 ) == 'php') && substr( $file, 0, 1 ) != '.' ) {
					if( !empty( $filtre ) ) {
						if( strpos( $file, $filtre ) > 0 )
							$classes[] = $file;
					} else
						$classes[] = $file;
				}
			}
			closedir( $dir );
			sort( $classes );
			return $classes;
		}

		/**
		 * Retourne la liste du menu principal et des controleurs pour l'affichage de l'onglet des droits
		 * Initialise la valeur 'modifiable' en fonction des droits de la collectivité-réle-utilisateur
		 * Intitialise é True le champ 'modifiable' si $cru est vide
		 * @param array $cru ('model'=>string, 'foreign_key'=>integer)
		 * @return array(
		 * 	[title] => affiché de vant la case é cocher
		 * 	[acosAlias] => alias de la table acos
		 * 	[niveau] => 0:menu principal, 1:sous-menu, 2:sous-sous-menu, ...
		 * 	[nbSousElements] => nb de sous éléments du menu
		 * 	[modifiable] => false|true indique si la case é cocher est accessible ou pas.
		 */
		public function menuCtrlActionAffichage( $cru = null ) {
			$ret = array( );

			// Chargement de l'arborescence du menu
			$this->_chargeMenuControllers( $ret, $this->load( 'menu' ), $cru );

			// Chargement des controleurs/Action qui ne sont pas dans le menu
			$this->_chargeControllersActions( $ret, $cru );

			return $ret;
		}

		/**
		 * construit l'arborescence du menu et des controleurs dans le tableau $menuCtrlTree
		 * retourne le nombre d'éléments du menu
		 *
		 * @param  string $path Path to scan for files
		 * @return array  Liste des action du menu
		 */
		protected function _chargeMenuControllers( &$menuCtrlTree, $menu, $cru = null, $niveau = 0 ) {
			// Initialisations
			$nbTotElement = 0;

			// Parcours des items du menu
			$items = $menu['items'];
			foreach( $items as $title => $menuItem ) {
				$menuCtrlTree[] = array( );
				$key = count( $menuCtrlTree ) - 1;
				$acosAlias = $this->_calcAction( $menuItem['link'] );
				$menuCtrlTree[$key]['title'] = ($niveau == 0 ? 'Menu:' : '').$title;
				$menuCtrlTree[$key]['acosAlias'] = $acosAlias;
				$menuCtrlTree[$key]['niveau'] = $niveau;
				$menuCtrlTree[$key]['nbSousElements'] = 0;
				$menuCtrlTree[$key]['modifiable'] = empty( $cru ) ? true : $this->Acl->Check( $cru, $acosAlias );

				// Traitement des sous-menus
				if( array_key_exists( 'subMenu', $menuItem ) and
						is_array( $menuItem['subMenu'] ) and count( $menuItem['subMenu'] ) > 0 ) {
					$menuCtrlTree[$key]['nbSousElements'] += $this->_chargemenuControllers( $menuCtrlTree, $menuItem['subMenu'], $cru, $niveau + 1 );
				}

				$nbTotElement += $menuCtrlTree[$key]['nbSousElements'] + 1;
			}
			return $nbTotElement;
		}

		/**
		 * Ajoute les actions des controller qui ne sont pas liés au menu $menuCtrlTree
		 * retourne le nombre d'éléments ajoutés
		 *
		 * @param type $menuCtrlTree
		 * @param type $cru
		 * @return type
		 */
		protected function _chargeControllersActions( &$menuCtrlTree, $cru = null ) {
			// Initialisation
			$nbElements = 0;

			// Parcours des controleurs
			$controllerList = App::objects( 'controller' );
			sort( $controllerList );

			foreach( $controllerList as $controllerName ) {
				$controllerNameShort = preg_replace( '/Controller$/', '', $controllerName );

				if( !in_array( $controllerNameShort, array( 'App') ) ) {
					$listeActions = $this->Droits->listeActionsControleur( $controllerNameShort );
					// Supprime les actions déjé liées au menu
					foreach( $listeActions as $key => $action ) {
						if( $this->_trouveAction( $controllerNameShort.':'.$action, $menuCtrlTree ) )
							unset( $listeActions[$key] );
					}

					// Ajout à la liste des menus-controleurs
					if( !empty( $listeActions ) ) {
						$menuCtrlTree[] = array( );
						$key = count( $menuCtrlTree ) - 1;
						$acosAlias = 'Module:'.$controllerNameShort;
						$menuCtrlTree[$key]['title'] = 'Module:'.$this->Droits->libelleControleur( $controllerNameShort );
						$menuCtrlTree[$key]['acosAlias'] = $acosAlias;
						$menuCtrlTree[$key]['niveau'] = 0;
						$menuCtrlTree[$key]['nbSousElements'] = count( $listeActions );
						$menuCtrlTree[$key]['modifiable'] = empty( $cru ) ? true : $this->Acl->Check( $cru, $acosAlias );

						// Ajoute les libellés des actions
						$listeLibelles = $this->Droits->libellesActionsControleur( $controllerNameShort, $listeActions );
						$i = 0;
						foreach( $listeActions as $action ) {
							$key++;
							$acosAlias = $controllerNameShort.':'.$action;
							$menuCtrlTree[$key]['title'] = $listeLibelles[$i];
							$menuCtrlTree[$key]['acosAlias'] = $acosAlias;
							$menuCtrlTree[$key]['niveau'] = 1;
							$menuCtrlTree[$key]['nbSousElements'] = 0;
							$menuCtrlTree[$key]['modifiable'] = empty( $cru ) ? true : $this->Acl->Check( $cru, $acosAlias );
							$i++;
						}

						$nbElements += 1 + count( $listeActions );
					}
				}
			}
			return $nbElements;
		}

		/**
		 * Retourne True si l'action $actionName est dans $menuCtrlTree
		 *
		 * @param type $actionName
		 * @param type $menuCtrlTree
		 * @return boolean
		 */
		protected function _trouveAction( $actionName, $menuCtrlTree ) {
			foreach( $menuCtrlTree as $menuCtrl ) {
				if( $menuCtrl['acosAlias'] == $actionName )
					return true;
			}
			return false;
		}
	}
?>