<?php
	/**
	 * Code source de la classe DroitsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe DroitsController ...
	 *
	 * @package app.Controller
	 */
	class DroitsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Droits';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Acl',
			'Droits',
			'Menu',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Droits',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Groups',
			'User',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'edit',
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'edit' => 'update',
		);
		
		public $tabDroits = array( );
		public $iProfil = -1;
		public $iMenu = -1;

		public function beforeFilter() {
			ini_set( 'max_execution_time', 0 );
			ini_set( 'memory_limit', '1024M' );
			parent::beforeFilter();
		}

		/* Gestion des droits des utilisateurs sur les menus et les actions des controleurs */

		public function edit() {
			// fonction devenue inutilisable avec la gestion des droits par utilisateur et par groupe
			/* $this->Session->setFlash( 'Cette fonction n\'est plus utilisée, merci d\'utiliser la gestion des droits par utilisateur et par groupe', 'flash/error' );
			  $this->redirect('/');
			  exit(); */

			// Initialisation de la progressBar
			include ('Vendor/progressbar.php');
			Initialize( 200, 100, 200, 30, '#000000', '#FFCC00', '#006699' );

			// Initialisations
			$profilsUsersTree = array( );
			$menuControllersTree = array( );
			$filtreProfils = array( );
			$filtreMenu = array( );
			$nbProfilsUsers = 0;
			$nbMenuControllers = 0;
			$tabDroits = array( );
			$structColonnes = array( );

			// Chargement de l'arborescence des profils et des users associés
			$nbProfilsUsers = $this->_chargeProfilsUsers( $profilsUsersTree );
			// Chargement des utilisateurs sans profils
			$nbProfilsUsers += $this->_chargeUsersSansProfil( $profilsUsersTree );

			// Chargement de l'arborescence du menu et des controleurs liés
			$nbMenuControllers = $this->_chargeMenuControllers( $menuControllersTree, $this->Menu->load( 'menu' ) );
			// Chargement des controleurs/Action qui ne sont pas dans le menu
			$nbMenuControllers += $this->_chargeControllersActions( $menuControllersTree );

			ProgressBar( 100, 'Chargement de chaque profil' );

			if( empty( $this->request->data ) ) {
				// Chargement des droits pour les couples Profils/users-Menu/controleurs
				$this->_chargeDroits( $profilsUsersTree, $menuControllersTree );

				// Chargement de la liste pour le filtre sur les profils
				$filtreProfils = $this->_chargeFiltreProfils( $profilsUsersTree );

				// Chargement de la liste pour le filtre sur le menu
				$filtreMenu = $this->_chargeFiltreMenu( $menuControllersTree );

				// Cargement de la structure des colonnes pour l'affichage des celulles
				$this->_chargeStructureColonnes( $menuControllersTree, $structColonnes );

				// Définition des variables pour la vue
				$this->set( 'filtreProfils', $filtreProfils );
				$this->set( 'profilsUsersTree', $profilsUsersTree );
				$this->set( 'nbProfilsUsers', $nbProfilsUsers );
				// Menu trié sur la valeur (chaine de caractères) en gardant la correspondance clé/valeur
				uasort( $filtreMenu, 'strcmp' );
				$this->set( 'filtreMenu', $filtreMenu );
				$this->set( 'menuControllersTree', $menuControllersTree );
				$this->set( 'nbMenuControllers', $nbMenuControllers );
				$this->set( 'tabDroits', $this->tabDroits );
				$this->set( 'structColonnes', $structColonnes );
			}
			else {
				// Mise à jour des tables acos et aros
				$this->_ajouteMenuControllersDansAcos( $menuControllersTree );
				$this->_ajouteProfilsUsersDansAros( $profilsUsersTree );

				// Mise à jour des droits
				$this->tabDroits = str_split( $this->request->data['Droit']['strDroits'], $nbMenuControllers );
				$this->tabDroits = array_map( 'str_split', $this->tabDroits );
				$this->_majDroits( $profilsUsersTree, $menuControllersTree );

				echo ('<script>');
				echo ('    document.getElementById("pourcentage").style.display="none"; ');
				echo ('    document.getElementById("progrbar").style.display="none";');
				echo ('    document.getElementById("affiche").style.display="none";');
				echo ('    document.getElementById("contTemp").style.display="none";');
				echo ('</script>');

				echo ("Enregistrement effectu&eacute;e <br />");
				$urlWebroot = 'http://'.$_SERVER['HTTP_HOST'].$this->request->base;
				echo ("<a href=' $urlWebroot/'>Retour a la page precedente</a>");
				exit;
			}
		}

		/* construit l'arborescence des profils et des utilisateurs dans le tableau $profilsTree */
		/* retourne le nombre d'éléments du profil */

		protected function _chargeProfilsUsers( &$profilsTree, $parentId = 0 ) {
			// Initialisations
			$nbTotElement = 0;

			// Lecture des profils
			$profils = $this->Groups->find(
					'all', array(
				'conditions' => array( 'parent_id' => $parentId ),
				'fields' => array( 'id', 'name' ),
				'order' => array( 'name ASC' ),
				'recursive' => -1
					)
			);
			// Sort si il n'y a pas de profils lu en base
			if( empty( $profils ) )
				return;

			// Parcours des profils lu en base
			foreach( $profils as $profil ) {
				$profilsTree[] = $profil['Groups'];
				$key = count( $profilsTree ) - 1;
				$profilsTree[$key]['arosAlias'] = 'Group:'.$profil['Groups']['name'];
				$profilsTree[$key]['nbElement'] = 0;

				// Traitement des Users
				$profilId = $profil['Groups']['id'];
				$users = $this->User->find(
						'all', array(
					'conditions' => array( 'group_id' => $profilId ),
					'fields' => array( 'id', 'username' ),
					'order' => array( 'username ASC' ),
					'recursive' => -1
						)
				);
				if( !empty( $users ) ) {
					foreach( $users as $user ) {
						$profilsTree[$key]['users'][] = $user['User'];
						$keyUser = count( $profilsTree[$key]['users'] ) - 1;
						$profilsTree[$key]['users'][$keyUser]['arosAlias'] = 'Utilisateur:'.$user['User']['username'];
						$profilsTree[$key]['nbElement']++;
					}
				}

				// Traitement des sous-profils
				if( $this->Groups->find( "count", array( 'conditions' => array( "parent_id" => $profilId ), 'recursive' => -1 ) ) > 0 ) {
					$profilsTree[$key]['nbElement'] += $this->_chargeProfilsUsers( $profilsTree[$key]['sousProfils'], $profilId );
				}
				$nbTotElement += $profilsTree[$key]['nbElement'] + 1;
			}
			return $nbTotElement;
		}

		/* ajoute les utilisateurs sans profil dans le tableau $profilsTree */
		/* retourne le nombre d'éléments du profil 'Sans profil' */

		protected function _chargeUsersSansProfil( &$profilsTree ) {
			// Lecture des utilisateurs sans profil
			$users = $this->User->find( "group_id = 0", array( 'id', 'username' ) );

			// Sort si il n'y a pas d'utilisateur sans profil
			if( empty( $users ) )
				return;

			// Création de l'entrée dans le tableau
			$profilsTree[] = array( );
			$key = count( $profilsTree ) - 1;
			$profilsTree[$key]['id'] = 0;
			$profilsTree[$key]['name'] = 'Sans Groupe';
			$profilsTree[$key]['arosAlias'] = 'Group:Sans Groupe';
			$profilsTree[$key]['nbElement'] = 0;

			// Parcours des utilisateurs
			foreach( $users as $user ) {
				$profilsTree[$key]['users'][] = $user['User'];
				$keyUser = count( $profilsTree[$key]['users'] ) - 1;
				$profilsTree[$key]['users'][$keyUser]['arosAlias'] = 'Utilisateur:'.$user['User']['username'];
				$profilsTree[$key]['nbElement']++;
			}
			return $profilsTree[$key]['nbElement'] + 1;
		}

		/* retourne la liste des profils principaux sous forme d'un tableau */
		/* utilisé dans la vue pour filtrer l'affichage des profils */
		/* retourne un tableau associatif : ligneDeb-ligneFin => nom_du_profil */

		protected function _chargeFiltreProfils( $profilsUsersTree ) {
			// Initialisations
			$fProfil = array( );
			$lDeb = 0;

			foreach( $profilsUsersTree as $profil ) {
				$lDeb++;
				$key = $lDeb.'-'.($lDeb + $profil['nbElement']);
				$fProfil[$key] = $profil['name'];
				$lDeb += $profil['nbElement'];
			}
			return $fProfil;
		}

		/* construit l'arborescence du menu et des controleurs dans le tableau $menuCtrlTree */
		/* retourne le nombre d'éléments du menu */

		protected function _chargeMenuControllers( &$menuCtrlTree, $menu, $niveau = 0 ) {
			// Initialisations
			$nbTotElement = 0;

			// Parcours des items du menu
			$items = $menu['items'];
			foreach( $items as $title => $menuItem ) {
				$menuCtrlTree[] = array( );
				$key = count( $menuCtrlTree ) - 1;
				$menuCtrlTree[$key]['title'] = ($niveau == 0 ? 'Menu:' : '').$title;
				$menuCtrlTree[$key]['acosAlias'] = $this->Menu->_calcAction( $menuItem['link'] );
				$menuCtrlTree[$key]['nbElement'] = 0;

				// Traitement des sous-menus
				if( array_key_exists( 'subMenu', $menuItem ) and
						is_array( $menuItem['subMenu'] ) and count( $menuItem['subMenu'] ) > 0 ) {
					$menuCtrlTree[$key]['nbElement'] += $this->_chargemenuControllers( $menuCtrlTree[$key]['subMenu'], $menuItem['subMenu'], $niveau + 1 );
				}

				$nbTotElement += $menuCtrlTree[$key]['nbElement'] + 1;
			}
			return $nbTotElement;
		}

		/* ajoute les actions des controller qui ne sont pas liés au menu $menuCtrlTree     */
		/* retourne le nombre d'éléments ajoutés                                            */

		protected function _chargeControllersActions( &$menuCtrlTree ) {
			// Initialisation
			$nbElements = 0;

			// Parcours des controleurs
			$controllerList = $this->_listClasses( APP."/controllers/" );
			foreach( $controllerList as $controllerFile ) {
				if( strpos( $controllerFile, '_controller.php' ) > 0 ) {
					$controllerName = Inflector::camelize( str_replace( '_controller.php', '', $controllerFile ) );
					$listeActions = $this->Droits->listeActionsControleur( $controllerName );
					// Supprime les actions déjà liées au menu
					foreach( $listeActions as $key => $action ) {
						if( $this->_trouveAction( $controllerName.':'.$action, $menuCtrlTree ) )
							unset( $listeActions[$key] );
					}
					// Ajout à la liste des menus-controleurs
					if( !empty( $listeActions ) ) {
						$menuCtrlTree[] = array( );
						$key = count( $menuCtrlTree ) - 1;
						$menuCtrlTree[$key]['title'] = 'Module:'.$this->Droits->libelleControleur( $controllerName );
						$menuCtrlTree[$key]['acosAlias'] = $controllerName;
						$menuCtrlTree[$key]['nbElement'] = count( $listeActions );
						$menuCtrlTree[$key]['subMenu'] = array( );
						// Ajoute les libellés des actions
						$listeLibelles = $this->Droits->libellesActionsControleur( $controllerName, $listeActions );
						$i = 0;
						foreach( $listeActions as $action ) {
							$menuCtrlTree[$key]['subMenu'][$i]['title'] = $listeLibelles[$i];
							$menuCtrlTree[$key]['subMenu'][$i]['acosAlias'] = $controllerName.':'.$action;
							$menuCtrlTree[$key]['subMenu'][$i]['nbElement'] = 0;
							$i++;
						}
						$nbElements += 1 + count( $listeActions );
					}
				}
			}
			return $nbElements;
		}

		/* retourne la liste des éléments principaux du menu sous forme d'un tableau */
		/* utilisé dans la vue pour filtrer l'affichage des menus */
		/* retourne un tableau associatif : colDeb-colFin => titre_du_menu */

		protected function _chargeFiltreMenu( $menuControllersTree ) {
			// Initialisations
			$fMenu = array( );
			$cDeb = 0;

			foreach( $menuControllersTree as $menu ) {
				$cDeb++;
				$key = $cDeb.'-'.($cDeb + $menu['nbElement']);
				$fMenu[$key] = $menu['title'];
				$cDeb += $menu['nbElement'];
			}
			return $fMenu;
		}

		/* Mise à jour de la table Acos avec les éléments du menu et les actions associées */

		protected function _ajouteMenuControllersDansAcos( $menuControllersTree ) {
			// Supression de tous les éléments en base
			$this->User->query( "TRUNCATE TABLE acos" );

			// mise à jour de la table Acos
			$this->_majAcos( $menuControllersTree );
		}

		/* Mise à jour de la table Acos avec les éléments du menu et les actions associées */

		protected function _majAcos( $menuControllersTree, $parent = 0 ) {
			// Initialisations
			$aco = new Aco();
			// Parcours des éléments du menu et des actions de controleurs
			foreach( $menuControllersTree as $menuController ) {
				if( !$aco->find( 'first', array( 'conditions' => array( 'Aco.alias' => $menuController['acosAlias'] ) ) ) ) {
					$aco->create();
					$sav = array( 'foreign_key' => 0, 'parent_id' => $parent, 'alias' => $menuController['acosAlias'] );
					$aco->save( $sav );
					$parent_id = $aco->getLastInsertId();
				}
				// Traitement des sous-menus
				if( is_array( $menuController ) )
					if( array_key_exists( 'subMenu', $menuController ) and !empty( $menuController['subMenu'] ) )
						$this->_majAcos( $menuController['subMenu'], $parent_id );
			}
		}

		/* Mise à jour de la table Aros avec les profils et les utilisateurs */

		protected function _ajouteProfilsUsersDansAros( $profilsUsersTree ) {
			// Supression de tous les éléments en base
			$this->User->query( "TRUNCATE TABLE aros" );

			// mise à jour de la table Acos
			$this->_majAros( $profilsUsersTree );
		}

		/* Mise à jour de la table Aros avec les profils et les utilisateurs */

		protected function _majAros( $profilsUsersTree, $parent = null ) {
			// Initialisations
			$aro = new Aro();
			// Parcours des profils et des utilisateurs
			foreach( $profilsUsersTree as $profilUsers ) {
				if( !$aro->find( 'first', array( 'conditions' => array( 'Aro.alias' => $profilUsers['arosAlias'] ) ) ) ) {
					$aro->create();
					$sav = array( 'foreign_key' => 0, 'parent_id' => $parent, 'alias' => $profilUsers['arosAlias'] );
					$aro->save( $sav );
					$profil_id = $aro->getLastInsertId();
				}

				// Traitement des utilisateurs
				if( array_key_exists( 'users', $profilUsers ) ) {
					foreach( $profilUsers['users'] as $user ) {
						$qd_aro = array(
							'conditions' => array(
								'Aro.alias' => $user['arosAlias']
							)
						);
						$aro = $this->find('first', $qd_aro);

						if( empty($aro['Aro']) ) {
							$aro->create();
							$sav1 = array( 'foreign_key' => $user['id'], 'parent_id' => $profil_id, 'alias' => $user['arosAlias'] );
							$aro->save( $sav1 );
						}
					}
				}

				// Traitement des sous-profils
				if( array_key_exists( 'sousProfils', $profilUsers ) )
					$this->_majAros( $profilUsers['sousProfils'], $profil_id );
			}
		}

		/* Mise à jour de la table aros_acos en fonction de la chaine $strDroits */

		protected function _majDroits( $profilsUsersTree, $menuControllersTree ) {
			// Supression de tous les droits en base
			$this->User->query( "TRUNCATE TABLE aros_acos" );

			// Ecriture des droits
			$this->_majDroitsProfilsUsers( $profilsUsersTree, $menuControllersTree );
		}

		/* Fonction récursive sur les profils pour la mise à jour des droits */

		protected function _majDroitsProfilsUsers( $profilsUsersTree, $menuControllersTree, $iProfilPere = -1 ) {
			// Initialisations
			$iProfilCourant = 0;

			// Parcours des profils
			foreach( $profilsUsersTree as $profilUsers ) {
				$this->iProfil++;
				$iProfilCourant = $this->iProfil;
				$this->iMenu = -1;
				$this->_majDroitsMenuControllers( $profilUsers['arosAlias'], $menuControllersTree, $iProfilPere );

				// Traitement des utilisateurs du profil courant
				if( array_key_exists( 'users', $profilUsers ) ) {
					foreach( $profilUsers['users'] as $user ) {
						$this->iProfil++;
						$this->iMenu = -1;
						$this->_majDroitsMenuControllers( $user['arosAlias'], $menuControllersTree, $iProfilCourant );
					}
				}

				// Traitement des sous-profils
				if( array_key_exists( 'sousProfils', $profilUsers ) ) {
					$this->_majDroitsProfilsUsers( $profilUsers['sousProfils'], $menuControllersTree, $iProfilCourant );
				}
			}
		}

		/* Fonction récursive sur les menus et actions des controleurs pour la mise à jour des droits */

		protected function _majDroitsMenuControllers( $aro, $menuControllersTree, $iProfilPere, $iMenuPere = -1 ) {
			// Parcours des menus et controleurs
			foreach( $menuControllersTree as $menuController ) {
				$this->iMenu++;
				// On ajoute des entrées dans la table aros_acos que si nécessaire selon les 4 cas possibles
				if( ($iProfilPere == -1) && ($iMenuPere == -1) ) {
					// cas 1 : profil et option principale du menu : on creer une entrée dans aros_acos
					$creerDroits = true;
				}
				elseif( ($iProfilPere == -1) && ($iMenuPere != -1) ) {
					// cas 2 : profil et sous-menu : on creer une entrée dans aros_acos si droits menu père != sous-menu pour ce profil
					$creerDroits = $this->tabDroits[$this->iProfil][$this->iMenu] != $this->tabDroits[$this->iProfil][$iMenuPere];
				}
				elseif( ($iProfilPere != -1) && ($iMenuPere == -1) ) {
					// cas 3 : sous-profil ou utilisateur, et menu principal : on creer une entrée dans aros_acos si droits profil != sous-profil ou utilisateur pour ce menu principal
					$creerDroits = $this->tabDroits[$this->iProfil][$this->iMenu] != $this->tabDroits[$iProfilPere][$this->iMenu];
				}
				elseif( ($iProfilPere != -1) && ($iMenuPere != -1) ) {
					// cas 4 : sous-profil ou utilisateur, et sous-menu : ça se complique
					$accesProfilPereMenuPere = $this->tabDroits[$iProfilPere][$iMenuPere] == '1';
					$accesProfilPereMenu = $this->tabDroits[$iProfilPere][$this->iMenu] == '1';
					$accesProfilMenuPere = $this->tabDroits[$this->iProfil][$iMenuPere] == '1';
					if( $this->tabDroits[$this->iProfil][$this->iMenu] == '1' ) {
						$creerDroits =
								($accesProfilPereMenuPere && $accesProfilPereMenu && !$accesProfilMenuPere) ||
								($accesProfilPereMenuPere && !$accesProfilPereMenu && $accesProfilMenuPere) ||
								($accesProfilPereMenuPere && !$accesProfilPereMenu && !$accesProfilMenuPere) ||
								(!$accesProfilPereMenuPere && !$accesProfilPereMenu && !$accesProfilMenuPere);
					}
					if( $this->tabDroits[$this->iProfil][$this->iMenu] == '0' ) {
						$creerDroits =
								($accesProfilPereMenuPere && $accesProfilPereMenu && $accesProfilMenuPere) ||
								(!$accesProfilPereMenuPere && $accesProfilPereMenu && $accesProfilMenuPere) ||
								(!$accesProfilPereMenuPere && $accesProfilPereMenu && !$accesProfilMenuPere) ||
								(!$accesProfilPereMenuPere && !$accesProfilPereMenu && $accesProfilMenuPere);
					}
				}
				if( $creerDroits ) {
					if( $this->tabDroits[$this->iProfil][$this->iMenu] == '1' )
						$this->Acl->allow( $aro, $menuController['acosAlias'] );
					else
						$this->Acl->deny( $aro, $menuController['acosAlias'] );
				}

				// Traitement des sous-menus
				if( is_array( $menuController ) )
					if( array_key_exists( 'subMenu', $menuController ) and !empty( $menuController['subMenu'] ) )
						$this->_majDroitsMenuControllers( $aro, $menuController['subMenu'], $iProfilPere, $this->iMenu );
			}
		}

		/* charge les droits des couples Profils/User - Menu/Controleurs dans le tableau tabDroits */

		protected function _chargeDroits( $profilsUsersTree, $menuControllersTree ) {
			$cpt = 0;
			$nbUsers = count( $this->User->find( 'all', null, 'id' ) );
			// Parcours des profils
			foreach( $profilsUsersTree as $profilUsers ) {
				$this->iProfil++;
				$this->iMenu = -1;
				$this->_chargeDroitsMenuControllers( $profilUsers['arosAlias'], $menuControllersTree );

				// Traitement des utilisateurs du profil courant
				if( array_key_exists( 'users', $profilUsers ) ) {
					foreach( $profilUsers['users'] as $user ) {
						$cpt++;
						$indice = $cpt * (100 / $nbUsers);
						$this->iProfil++;
						$this->iMenu = -1;
						$this->_chargeDroitsMenuControllers( $user['arosAlias'], $menuControllersTree, $indice, $user['username'] );
					}
				}

				// Traitement des sous-profils
				if( array_key_exists( 'sousProfils', $profilUsers ) ) {
					$this->_chargeDroits( $profilUsers['sousProfils'], $menuControllersTree );
				}
			}
		}

		/* Fonction récursive sur les menus et actions des controleurs pour le chargement des droits */

		protected function _chargeDroitsMenuControllers( $aro, $menuControllersTree, $indice = null, $user = null ) {
			// Parcours des menus et controleurs
			foreach( $menuControllersTree as $menuController ) {
				$this->iMenu++;
				// lecture des droits
				$this->tabDroits[$this->iProfil][$this->iMenu] = @$this->Acl->check( $aro, $menuController['acosAlias'] );
				if( $indice != null )
					ProgressBar( $indice, 'Lecture des droits '.$menuController['title'].' pour '.$user );
				// Traitement des sous-menus
				if( is_array( $menuController ) )
					if( array_key_exists( 'subMenu', $menuController ) and !empty( $menuController['subMenu'] ) )
						$this->_chargeDroitsMenuControllers( $aro, $menuController['subMenu'] );
			}
		}

		/* retourne True si l'action $actionName est dans $menuCtrlTree */

		protected function _trouveAction( $actionName, $menuCtrlTree ) {
			foreach( $menuCtrlTree as $menuCtrl ) {
				if( $menuCtrl['acosAlias'] == $actionName )
					return true;

				// Traitement du sous-menu
				if( $menuCtrl['nbElement'] > 0 ) {
					if( $this->_trouveAction( $actionName, $menuCtrl['subMenu'] ) )
						return true;
				}
			}
			return false;
		}

		/* construit le tableau $structColonnes avec le nombre d'éléments du menu par colonnes */

		protected function _chargeStructureColonnes( $menuControllersTree, &$structColonnes ) {

			foreach( $menuControllersTree as $menu ) {
				$structColonnes[] = $menu['nbElement'];
				if( $menu['nbElement'] > 0 )
					$this->_chargeStructureColonnes( $menu['subMenu'], $structColonnes );
			}
		}

		protected function _listClasses( $path ) {
			$dir = opendir( $path );
			$classes = array( );
			while( false !== ($file = readdir( $dir )) ) {
				if( (substr( $file, -3, 3 ) == 'php') && substr( $file, 0, 1 ) != '.' ) {
					$classes[] = $file;
				}
			}
			closedir( $dir );
			return $classes;
		}

	}
?>