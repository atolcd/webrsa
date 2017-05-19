<?php
	/**
	 * Code source de la classe Workflowscers93Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe Workflowscers93Component.
	 *
	 * @package app.Controller.Component
	 */
	class Workflowscers93Component extends Component
	{
		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array( );

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array(
			'Session',
			'WebrsaUsers'
		);

		/**
		 *
		 * @var string
		 */
		protected $_assertErrorTemplate = 'L\'utilisateur doit etre %s pour pouvoir accéder à cette fonctionnalité.';

		/**
		 * Permet de récupérer la structure référente à laquelle est attaché
		 * l'utilisateur connecté.
		 *
		 * @param boolean $required Doit-on retourner une erreur 403 lorsque
		 *	l'utilisateur n'est pas attaché à une structure référente ?
		 * @return integer
		 * @throws Error403Exception
		 */
		public function getUserStructurereferenteId( $required = true ) {
			$Controller = $this->_Collection->getController();

			$structuresreferentes_ids = $this->WebrsaUsers->structuresreferentes();

			// S'il est obligatoire d'être rattaché à une structure référente
			if( $required && empty( $structuresreferentes_ids ) ) {
				$this->Session->setFlash( 'L\'utilisateur doit etre rattaché à une structure référente.', 'flash/error' );
				throw new Error403Exception( null );
			}

			return $structuresreferentes_ids;
		}

		/**
		 * Permet de s'assurer que l'utilisateur connecté soit bien un CPDV.
		 *
		 * @throws Error403Exception
		 */
		public function assertUserCpdv() {
			if( false === $this->isUserCpdv() ) {
				$this->Session->setFlash( sprintf( $this->_assertErrorTemplate, 'un responsable, une secrétaire ou un chef de projet communautaire' ), 'flash/error' );
				throw new error403Exception( null );
			}
		}

		/**
		 * Permet de s'assurer que l'utilisateur connecté soit bien un CI.
		 *
		 * @throws Error403Exception
		 */
		public function assertUserCi() {
			if( false === $this->isUserCi() ) {
				$this->Session->setFlash( sprintf( $this->_assertErrorTemplate, 'un chargé d\'insertion' ), 'flash/error' );
				throw new error403Exception( null );
			}
		}

		/**
		 * Permet de s'assurer que l'utilisateur connecté soit bien un utilisateur CG.
		 *
		 * @throws Error403Exception
		 */
		public function assertUserCg() {
			if( false === $this->isUserCg() ) {
				$this->Session->setFlash( sprintf( $this->_assertErrorTemplate, 'un utilisateur du conseil général' ), 'flash/error' );
				throw new error403Exception( null );
			}
		}

		/**
		 * Permet de s'assurer que l'utilisateur connecté soit bien un externe
		 * (CPDV ou un CI).
		 *
		 * @throws Error403Exception
		 */
		public function assertUserExterne() {
			if( false === $this->isUserExterne() ) {
				$this->Session->setFlash( sprintf( $this->_assertErrorTemplate, 'un responsable, une secrétaire, un chargé d\'insertion ou un chef de projet communautaire' ), 'flash/error' );
				throw new error403Exception( null );
			}
		}

		/**
		 * Retourne true si l'utilisateur connecté est un externe (CPDV,
		 * secrétaire, CI, chef de projet communautaire).
		 *
		 * @return boolean
		 */
		public function isUserExterne() {
			return ( strpos( $this->Session->read( 'Auth.User.type' ), 'externe_' ) === 0 );
		}

		/**
		 * Retourne true si l'utilisateur connecté est de type CG.
		 *
		 * @return boolean
		 */
		public function isUserCg() {
			return $this->Session->read( 'Auth.User.type' ) === 'cg';
		}

		/**
		 * Retourne true si l'utilisateur connecté est de type externe CI.
		 *
		 * @return boolean
		 */
		public function isUserCi() {
			return $this->Session->read( 'Auth.User.type' ) === 'externe_ci';
		}

		/**
		 * Retourne true si l'utilisateur connecté est de type externe CPDV,
		 * secrétaire structure ou chef de projet communautaire.
		 *
		 * @return boolean
		 */
		public function isUserCpdv() {
			$accepted = array( 'externe_cpdv', 'externe_secretaire', 'externe_cpdvcom' );
			return in_array( $this->Session->read( 'Auth.User.type' ), $accepted );
		}
	}
?>