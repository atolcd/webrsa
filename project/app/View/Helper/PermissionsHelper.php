<?php
	/**
	 * Fichier source de la classe PermissionsHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe PermissionsHelper permet de vérifier les droits d'accès de
	 * l'utilisateur.
	 *
	 * @package app.View.Helper
	 */
	class PermissionsHelper extends AppHelper
	{
		/**
		 * Vérifie les droits d'accès à un couple controller/action par-rapport
		 * aux droits stockés en session.
		 *
		 * @see WebrsaPermissions::check()
		 *
		 * @param string $controllerName
		 * @param string $actionName
		 * @return boolean
		 */
		public function check( $controllerName, $actionName ) {
			return WebrsaPermissions::check( $controllerName, $actionName );
		}

		/**
		 *
		 * @see WebrsaPermissions::checkDossier()
		 *
		 * @param type $controllerName
		 * @param type $actionName
		 * @param type $dossierData
		 * @return type
		 */
		public function checkDossier( $controllerName, $actionName, $dossierData ) {
			return WebrsaPermissions::checkDossier( $controllerName, $actionName, $dossierData );
		}

		/**
		 * On donne une liste d'actions, et cette fonction se charge de vérifier les droits sur chaqu'unes.
		 * On peux également tester d'autres controller en les indiquant de cette façon : Moncontroller.monaction
		 *
		 * @param array $actions
		 * @param array $dossierMenu : array( 'monaction' => 'true' )
		 */
		public function permList( array $actions, $dossierMenu ) {
			$perm = array();

			foreach( $actions as $permission ){
				$controllerName = Hash::get($this->request->params, 'controller');
				$actionName = $permission;

				if ( strpos($permission, '.') !== false ){
					$divide = explode( '.', $permission );
					$controllerName = $divide[0];
					$actionName = $divide[1];
				}

				$perm[$permission] = $this->checkDossier( $controllerName, $actionName, $dossierMenu );
			}

			return $perm;
		}
	}
?>
