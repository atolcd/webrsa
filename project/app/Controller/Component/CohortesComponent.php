<?php
	/**
	 * Fichier source de la classe CohortesComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe CohortesComponent permet de gérer les jetons dans les cohortes, avec prise en compte du
	 * passage de page en page.
	 *
	 * @package app.Controller.Component
	 */
	class CohortesComponent extends Component
	{
		/**
		 * Nom du component.
		 *
		 * @var string
		 */
		public $name = 'Cohortes';

		/**
		 * Contrôleur utilisant ce component.
		 *
		 * @var Controller
		 */
		public $Controller = null;

		/**
		 * Les paramètres du component.
		 *
		 * @var array
		 */
		public $settings = array();

		/**
		 * La page sur laquelle on se trouve.
		 *
		 * @var integer
		 */
		public $page = null;

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array( 'Session', 'Jetons2' );

		/**
		 * Initialisation du component.
		 *
		 * @param Controller $controller Le contrôleur avec des composants à initialiser
		 * @return void
		 */
		public function initialize( Controller $controller ) {
			$settings = $this->settings;
			parent::initialize( $controller, $settings );

			$this->Controller = $controller;
			$this->settings = (array)$settings;

            $this->Jetons2->initialize( $controller );

			if( $this->active() ) {
				$this->clean();
			}
		}

		/**
		 * Le component est-il actif (en fonction de l'action du contrôleur et du paramétrage) ?
		 *
		 * @return boolean
		 */
		public function active() {
			return in_array( $this->Controller->action, $this->settings );
		}

		/**
		 * Nettoyage des jetons stockés dans la session, pour les pages qui ne sont pas la page en cours.
		 *
		 * @return void
		 */
		public function clean() {
			$this->page = ( isset( $this->Controller->request->params['named']['page'] ) ? $this->Controller->request->params['named']['page'] : 1 );
			$sessionKey = $this->sessionKey();
			$jetons = (array)$this->Session->read( $sessionKey );

			//Il ne faut supprimer que les jetons liés à l'utilisateur courant
			unset( $jetons[$this->page] );

			if( !empty( $jetons ) ) {
				foreach( $jetons as $page => $dossiers_ids ) {
					if( !empty( $dossiers_ids ) ) {
						$this->release( $dossiers_ids );
						$this->Session->delete( "{$sessionKey}.{$page}" );
					}
				}
			}
		}

		/**
		 * Nettoyage des jetons stockés dans la session, pour la page en cours
		 * (contrôleur, action, page).
		 *
		 * @return void
		 */
		public function cleanCurrent() {
			if( $this->active() ) {
				$page = ( isset( $this->Controller->request->params['named']['page'] ) ? $this->Controller->request->params['named']['page'] : 1 );
				$sessionKey = $this->sessionKey().".{$page}";
				$dossiers_ids = (array)$this->Session->read( $sessionKey );

				if( !empty( $dossiers_ids ) ) {
					return $this->release( $dossiers_ids );
				}
			}

			return true;
		}

		/**
		 * Retourne la clé sous laquelle seront stockés les identifiants des dossiers dans la Session.
		 *
		 * @return string
		 */
		public function sessionKey() {
			return "{$this->name}.{$this->Controller->name}__{$this->Controller->action}";
		}

		/**
		 * On essaie d'acquérir un (ensemble de) jeton(s) pour l'utilisateur connecté au sein d'une transaction.
		 * Si on a réussi à acquérir les jetons, on stocke leurs ids dans la session (pour la page en cours).
		 *
		 * @see Jetons2::get
		 *
		 * @param mixed $dossiers
		 * @return boolean
		 */
		public function get( $dossiers, $noException = false  ) {
			$dossiers = array_unique( (array)$dossiers );

			$success  = $this->Jetons2->get( $dossiers, $noException );

			if(is_array($success)){
				foreach($success as $id){
					if (($key = array_search($id, $dossiers)) !== false) {
						unset($dossiers[$key]);
					}
				}
			};

			if( $success && !empty($dossiers)) {
				$sessionKey = $this->sessionKey();
				$this->Session->write( "{$sessionKey}.{$this->page}", $dossiers );
			}

			return $success;
		}

		/**
		 * On relache un (ensemble de) jeton(s).
		 *
		 * Si on a réussi à relacher les jetons, on supprime leurs ids dans la session (pour la page en cours).
		 *
		 * @see Jetons2::release
		 *
		 * @param mixed $dossiers
		 * @return boolean
		 */
		public function release( $dossiers ) {
			$dossiers = array_unique( (array)$dossiers );

			$success  = $this->Jetons2->release( $dossiers );

			if( $success ) {
				$sessionKey = $this->sessionKey();
				$this->Session->delete( "{$sessionKey}.{$this->page}" );
			}

			return $success;
		}

		/**
		 * Retourne une sous-reqûete permettant de savoir si le Dossier est locké.
		 *
		 * @see Jetons2::sqLocked
		 *
		 * @param string $modelAlias
		 * @param string $fieldName
		 * @return string
		 */
		public function sqLocked( $modelAlias = 'Dossier', $fieldName = null ) {
			return $this->Jetons2->sqLocked( $modelAlias, $fieldName );
		}

		/**
		 * Ajoute des conditions à un querydata afin d'exclure du jeu de résultats
		 * les dossiers lockés par d'autres utilisateurs.
		 *
		 * @param array $querydata
		 * @param string $dossierAlias
		 * @return array
		 */
		public function qdConditions( array $querydata, $dossierAlias = 'Dossier' ) {
			// TODO: if this->active() ?
			$querydata['conditions'][] = "NOT ".$this->sqLocked( $dossierAlias );

			return $querydata;
		}

		/**
		 * Called before Controller::redirect().  Allows you to replace the url that will
		 * be redirected to with a new url. The return of this method can either be an array or a string.
		 *
		 * If the return is an array and contains a 'url' key.  You may also supply the following:
		 *
		 * - `status` The status code for the redirect
		 * - `exit` Whether or not the redirect should exit.
		 *
		 * If your response is a string or an array that does not contain a 'url' key it will
		 * be used as the new url to redirect to.
		 *
		 * @param Controller $controller Controller with components to beforeRedirect
		 * @param string|array $url Either the string or url array that is being redirected to.
		 * @param integer $status The status code of the redirect
		 * @param boolean $exit Will the script exit.
		 * @return array|null Either an array or null.
		 * @link @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::beforeRedirect
		 */
		public function beforeRedirect( Controller $controller, $url, $status = null, $exit = true ) {
			return array( 'url' => $url, 'status' => $status, 'exit' => $exit );
		}
	}
?>