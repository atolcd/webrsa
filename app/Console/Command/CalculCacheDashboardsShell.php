<?php
	/**
	 * Code source de la classe CalculCacheDashboardsShell.
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'Component', 'Controller/Component' );
	App::uses( 'WebrsaUsersComponent', 'Controller/Component' );


	/**
	 * La classe CalculCacheDashboardsShell
	 *
	 * @package app.Console.Command
	 */
	class CalculCacheDashboardsShell extends XShell
	{
		/**
		 * Modèles utilisés par ce shell.
		 *
		 * @var array
		 */
		public $uses = array( 'Actionrole', 'User' );

		/**
		 * Tâches utilisées par ce shell.
		 *
		 * @var array
		 */
		public $tasks = array( 'Session' );

		/**
		 * Chargement de la session d'un utilisateur.
		 *
		 * @param integer $id L'id technique de l'utilisateur
		 * @throws RuntimeException
		 */
		protected function _loadUser( $id ) {
			$this->Session->id();
			$this->Session->clear();

			$query = array(
				'conditions' => array(
					'User.id' => $id
				),
				'contain' => false
			);
			$user = $this->User->find('first', $query);
			if( true === empty( $user ) ) {
				$message = sprintf(
					'Impossible de charger l\'utilisateur d\'id %d',
					$id
				);
				throw new RuntimeException( $message, 500 );
			}
			$this->Session->write( 'Auth.User', $user['User'] );

			$message = "Traitement de l'utilisateur <info>%s</info> (<info>%s %s</info>)";
			$this->out( sprintf( $message, $user['User']['username'], $user['User']['nom'], $user['User']['prenom'] ) );

			$this->Controller = new AppController();
			$componentCollection = new ComponentCollection();
			$componentCollection->init($this->Controller);
			$this->WebrsaUsers = new WebrsaUsersComponent( $componentCollection );
			$this->WebrsaUsers->initialize( $this->Controller );
			$this->WebrsaUsers->load();
		}

		/**
		 * Traitement des caches des tableaux de  bords d'un utilisateur.
		 *
		 * @param integer $id L'id technique de l'utilisateur
		 * @return boolean
		 */
		protected function _processUser($id) {
			$this->_loadUser($id);

			$query = array(
				'fields' => array_merge(
					$this->Actionrole->fields(),
					$this->Actionrole->Categorieactionrole->fields(),
					$this->Actionrole->Role->fields(),
					$this->Actionrole->Role->RoleUser->fields()
				),
				'contain' => false,
				'joins' => array(
					$this->Actionrole->join( 'Categorieactionrole', array( 'type' => 'INNER' ) ),
					$this->Actionrole->join( 'Role', array( 'type' => 'INNER' ) ),
					$this->Actionrole->Role->join( 'RoleUser', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'Role.actif' => 1,
					'RoleUser.user_id' => $this->Session->read('Auth.User.id')
				),
				'order' => array(
					'Categorieactionrole.name',
					'Role.name',
					'Actionrole.name'
				)
			);
			$results = $this->Actionrole->find( 'all', $query );

			$done = array();
			$success = true;

			foreach( $results as $result ) {
				if(true === $success) {
					if( false === isset( $done[$result['Categorieactionrole']['name']] ) ) {
						$done[$result['Categorieactionrole']['name']] = array();
						$message = "\tOnglet <info>%s</info>";
						$out = sprintf( $message, $result['Categorieactionrole']['name'] );
						$this->out( $out );
					}

					if( false === isset( $done[$result['Categorieactionrole']['name']][$result['Role']['name']] ) ) {
						$done[$result['Categorieactionrole']['name']][$result['Role']['name']] = array();
						$message = "\t\tRôle <info>%s</info>";
						$out = sprintf( $message, $result['Role']['name'] );
						$this->out( $out );
					}

					$message = "\t\t\tAction de rôle <info>%s</info>";
					$out = sprintf( $message, $result['Actionrole']['name'] );
					$this->out( $out, 0 );

					$localSuccess = $this->Actionrole->Actionroleresultuser->refresh(
						$result['Actionrole']['id'],
						$this->Session->read('Auth.User.id')
					);
					$success = $localSuccess && $success;

					$this->overwrite('', 0);

					$message = "\t\tAction de rôle <info>%s</info>: %s";
					$out = sprintf(
						$message,
						$result['Actionrole']['name'],
						true == $localSuccess ? '<success>Succès</success>' : '<error>Erreur</error>'
					);
					$this->overwrite( $out );
				}
			}

			return $success;
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			$query = array(
				'fields' => array('RoleUser.user_id'),
				'contain' => false,
				'joins' => array(
					$this->Actionrole->Role->RoleUser->join( 'User', array( 'type' => 'INNER' ) )
				),
				'group' => array(
					'RoleUser.user_id',
					'User.username'
				),
				'order' => array( 'User.username' )
			);
			$results = $this->Actionrole->Role->RoleUser->find( 'all', $query );

			$this->Actionrole->begin();
			$success = true;

			foreach( $results as $result ) {
				$success = $success && $this->_processUser(
					Hash::get($result, 'RoleUser.user_id')
				);
			}

			$this->out();
			if( true === $success ) {
				$this->Actionrole->commit();
				$message = "<success>Shell terminé avec succès.</success>";
			}
			else {
				$this->Actionrole->rollback();
				$message = "<error>Shell terminé avec erreur(s).</error>";
			}
			$this->out( $message );

			$this->_scritpEnd();
			$this->_stop( true === $success ? self::SUCCESS : self::ERROR );
		}
	}
?>