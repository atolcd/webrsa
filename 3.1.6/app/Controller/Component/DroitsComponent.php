<?php
	/*
	 * Gestion dynamique des droits des utilisateurs sur les actions (méthodes) des controleurs
	 *
	 *  Utilisation des variables suivantes à placer dans les contrôleurs :
	 *  $demandeDroit = array('actionName') -> liste des actions qui sont concernées par le contrôle des droits
	 *  $demandePost = array('actionName') -> liste des actions qui demandent un post pour être exécuté (utilisée avec le composant security)
	 *  $aucunDroit = array('actionName')|Null -> listes des actions qui ne sont pas soumises au contrôle des droits (si vide, aucune actions n'est soumises au contrôle)
	 *  $ajouteDroit = array('newActionName') -> listes des actions qui sont ajoutés pour le contrôle des droits alors que la méthode n'existe pas'
	 *  $commeDroit = array('actionName'=>'controllerAction'|array('controllerAction')) -> liste des actions soumises aux mêmes droits que d'autres actions (réalise un OU dans le cas d'un array)
	 *  $libelleControleurDroit = string -> permet de définir un nom métier pour le controleur
	 *  $libellesActionsDroit = array(string=>string) -> permet de définir un nom pour les méthodes du controleur
	 *
	 * Fonctionnement de la méthode check
	 *  la méthode check vérifie à l'aide du composant Acl, que l'utilisateur dont l'id est passé en paramètre
	 *  à les droits suffisants pour exécuter une action d'un controleur passée également
	 *  en paramètre sous la forme NomControleur:nomMethode (ex : User:add).
	 *  Pour chaque controleur, la liste des actions soumises aux droits est déterminée de la façon suivante
	 *      si $demandeDroit est définie et est non vide
	 *          alors -> liste des actions soumises aux droits = $demandeDroit
	 *      si $aucunDroit est définie et est vide
	 *          alors -> aucune des actions n'est soumises au contrôle des droits
	 *      si $demandeDroit n'est pas définie ou est vide
	 *          alors -> liste des actions soumises aux droits =
	 *              liste des actions non privées du controleurs
	 *              - liste des actions définies par $demandePost
	 *              - liste des actions définies par $aucunDroit
	 *              - liste des actions définies par $commeDroit
	 *              + liste des actions définies par $ajouteDroit
	 *
	 *  Si la variables $commeDroit est définie, le contrôle des droits se fait sur l'action définie dans cette variable
	 *  Par défaut, si aucune des variables précédentes n'est définie dans un controleur, alors
	 *  toutes les méthodes non privées seront soumises au contrôle des droits.
	 *
	 *  Attention : la méthode check autorise toutes les actions qui ne sont pas soumises au droits.
	 */
	class DroitsComponent extends Component
	{

		public $components = array( 'Acl' );

		/**
		 * Vérifie si l'utilisateur $userAlias est autorisée à exécuter l'action $controllerAction vérifie les droits
		 * si l'action est dans la liste des actions soumises aux droits.
		 *
		 * @param string $userAlias
		 * @param string $controllerAction
		 * @return boolean
		 */
		public function check( $userAlias, $controllerAction ) {
			// Initialisations
			$listeActions = array( );
			$listeActionsComme = array( );
			$controller = substr( $controllerAction, 0, strpos( $controllerAction, ':' ) );
			$action = substr( $controllerAction, strpos( $controllerAction, ':' ) + 1 );

			// Pas de contrôle si controller = App -> Retourne true
			if( $controller == 'App' )
				return true;

			// Initialisation de la liste des actions soumises aux droits
			if( $controller != 'Pages' ) {
				$listeActions = $this->listeActionsControleur( $controller );
				$listeActionsComme = $this->_listeActionsCommeControleur( $controller );
			}

			// Vérifie les droits si controller = 'Pages' ou si l'action est dans la liste des actions soumises aux droits
			if( $controller == 'Pages' or in_array( $action, $listeActions ) or array_key_exists( $action, $listeActionsComme ) ) {
				if( array_key_exists( $action, $listeActionsComme ) ) {
					// Traite les droits de commeDroit
					if( is_array( $listeActionsComme[$action] ) ) {
						foreach( $listeActionsComme[$action] as $ctrlActionComme ) {
							if( $this->Acl->check( $userAlias, $ctrlActionComme ) )
								return true;
						}
						return false;
					}
					else
						return $this->Acl->check( $userAlias, $listeActionsComme[$action] );
				}
				else
					return $this->Acl->check( $userAlias, $controllerAction );
			}
			else
				return true;
		}

		/**
		 * Détermine la liste des actions (méthodes) qui sont soumises aux droits d'un controleur en fonction
		 * des variables $demandeDroit, $aucunDroit, $demandePost, $commeDroit, $ajouteDroit.
		 *
		 * @param $controllerName string
		 * @return array
		 */
		public function listeActionsControleur( $controllerName ) {
			$controllerName = preg_replace( '/Controller$/', '', $controllerName );

			// chargement du controleur
			if( CAKE_BRANCH == '1.2' ) {
				App::import( 'Controller', $controllerName );
			}
			else {
				App::uses( "{$controllerName}Controller", 'Controller' );
			}

			$controllerName = "{$controllerName}Controller";

			if( !class_exists( $controllerName ) ) {
				return array( );
			}

			$subClassVars = get_class_vars( $controllerName );

			// Si $demandeDroit est définie et non vide alors retourne cette liste
			if( array_key_exists( 'demandeDroit', $subClassVars ) and !empty( $subClassVars['demandeDroit'] ) ) {
				return $subClassVars['demandeDroit'];
			}

			// Si $aucunDroit est définie et vide alors retourne liste vide
			/*if( array_key_exists( 'aucunDroit', $subClassVars ) and empty( $subClassVars['aucunDroit'] ) ) {
				return array( );
			}*/

			// Création de la liste des actions du controleur
			$parentClassMethods = get_class_methods( 'AppController' );
			$subClassMethods = get_class_methods( $controllerName );
			$classMethods = array_diff( $subClassMethods, $parentClassMethods );

			// Suppression des actions privées et protégées (commencent par '_')
			$classMethods = array_filter( $classMethods, array( $this, '_nonPrivee' ) );

			// Ajout des actions du scaffold
			if( array_key_exists( 'scaffold', $subClassVars ) && !empty( $subClassVars['scaffold'] ) ) {
				if( !in_array( 'index', $classMethods ) )
					$classMethods[] = 'index';
				if( !in_array( 'view', $classMethods ) )
					$classMethods[] = 'view';
				if( !in_array( 'add', $classMethods ) )
					$classMethods[] = 'add';
				if( !in_array( 'edit', $classMethods ) )
					$classMethods[] = 'edit';
				if( !in_array( 'delete', $classMethods ) )
					$classMethods[] = 'delete';
			}

			// Suppression des actions soumises à un post $demandePost
			if( array_key_exists( 'demandePost', $subClassVars ) and !empty( $subClassVars['demandePost'] ) ) {
				$classMethods = array_diff( $classMethods, $subClassVars['demandePost'] );
			}

			// Suppression des actions qui ne sont soumises à aucun droit $aucunDroit
			if( array_key_exists( 'aucunDroit', $subClassVars ) and !empty( $subClassVars['aucunDroit'] ) ) {
				$classMethods = array_diff( $classMethods, $subClassVars['aucunDroit'] );
			}

			// Suppression des actions qui sont soumises à d'autre droits $commeDroit
			if( array_key_exists( 'commeDroit', $subClassVars ) and !empty( $subClassVars['commeDroit'] ) ) {
				$classMethods = array_diff( $classMethods, array_keys( $subClassVars['commeDroit'] ) );
			}

			// Ajout des actions supplémentaires $ajouteDroit
			if( array_key_exists( 'ajouteDroit', $subClassVars ) and !empty( $subClassVars['ajouteDroit'] ) ) {
				$classMethods = array_merge( $classMethods, $subClassVars['ajouteDroit'] );
			}

			return $classMethods;
		}

		/**
		 * Retourne les libellés définits dans $libellesActionsDroit correspondant à $ListeActions
		 *
		 * @param string $controllerName
		 * @param array $listeActions
		 * @return array
		 */
		public function libellesActionsControleur( $controllerName, $listeActions ) {
			$controllerName = preg_replace( '/Controller$/', '', $controllerName );

			// chargement du controleur
			if( CAKE_BRANCH == '1.2' ) {
				App::import( 'Controller', $controllerName );
			}
			else {
				App::uses( "{$controllerName}Controller", 'Controller' );
			}

			$controllerName = "{$controllerName}Controller";

			if( !class_exists( $controllerName ) ) {
				return preg_replace( '/Controller$/', '', $controllerName );
			}

			$subClassVars = get_class_vars( $controllerName );

			// initialisation du tableau des libelles
			$i = 0;
			foreach( $listeActions as $action ) {
				$listeActionsLibelles[$i] = $action;
				$i++;
			}

			// teste si $libellesDroit est défini et non vide
			if( array_key_exists( 'libellesActionsDroit', $subClassVars ) and !empty( $subClassVars['libellesActionsDroit'] ) ) {
				$i = 0;
				foreach( $listeActions as $action ) {
					if( array_key_exists( $action, $subClassVars['libellesActionsDroit'] ) )
						$listeActionsLibelles[$i] = $subClassVars['libellesActionsDroit'][$action];
					$i++;
				}
			}

			return $listeActionsLibelles;
		}

		/**
		 * Retourne le libellé défini dans $libelleControleurDroit.
		 *
		 * @param string $controllerName
		 * @return string
		 */
		public function libelleControleur( $controllerName ) {
			$controllerName = preg_replace( '/Controller$/', '', $controllerName );

			// chargement du controleur
			if( CAKE_BRANCH == '1.2' ) {
				App::import( 'Controller', $controllerName );
			}
			else {
				App::uses( "{$controllerName}Controller", 'Controller' );
			}

			$controllerName = "{$controllerName}Controller";

			if( !class_exists( $controllerName ) ) {
				return preg_replace( '/Controller$/', '', $controllerName );
			}

			$subClassVars = get_class_vars( $controllerName );

			// teste si $libelleControleurDroit est défini et non vide
			if( array_key_exists( 'libelleControleurDroit', $subClassVars ) and !empty( $subClassVars['libelleControleurDroit'] ) ) {
				return $subClassVars['libelleControleurDroit'];
			}

			return preg_replace( '/Controller$/', '', $controllerName );
		}

		/**
		 * Retourne true si $nomMethode ne commence pas pas '_'.
		 *
		 * @param string $nomMethode
		 * @return boolean
		 */
		protected function _nonPrivee( $nomMethode ) {
			return $nomMethode[0] != '_';
		}

		/**
		 * Retourne la liste $commeDroit si elle est définie et non vide.
		 *
		 * @param string $controllerName
		 * @return array
		 */
		protected function _listeActionsCommeControleur( $controllerName ) {
			// chargement du controleur
			App::import( 'Controller', $controllerName );

			$subClassVars = get_class_vars( $controllerName.'Controller' );

			if( array_key_exists( 'commeDroit', $subClassVars ) and !empty( $subClassVars['commeDroit'] ) ) {
				return $subClassVars['commeDroit'];
			} else
				return array( );
		}

	}
?>