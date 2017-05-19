<?php
	/**
	 * AppShell file
	 *
	 * PHP 5
	 *
	 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
	 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
	 * @link          http://cakephp.org CakePHP(tm) Project
	 * @since         CakePHP(tm) v 2.0
	 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
	 */
	App::uses( 'Shell', 'Console' );

	/**
	 * Application Shell
	 *
	 * Add your application-wide methods in the class below, your shells
	 * will inherit them.
	 *
	 * @package app.Console.Command
	 */
	class AppShell extends Shell
	{
		/**
		 * La constante à utiliser dans la méthode _stop() en cas de succès.
		 */
		const SUCCESS = 0;

		/**
		 * La constante à utiliser dans la méthode _stop() en cas d'erreur.
		 */
		const ERROR = 1;

		/**
		 * Vérifie que l'utilisateur qui lance le shell soit bien le même que
		 * l'utilisateur du serveur web, afin d'éviter les problèmes de droits
		 * sur les fichiers du cache ou les fichiers temporaires.
		 */
		public function checkCliUser() {
			$whoami = exec( 'whoami' );
			$accepted = array( 'www-data', 'apache', 'httpd', 'jenkins' );

			if( !in_array( $whoami, $accepted ) ) {
				$msgstr = 'Mauvais utilisateur (%s), veuillez exécuter ce shell en tant que: %s';

				$Parser = $this->getOptionParser();
				$command = $Parser->command();

				$this->error(
					sprintf( $msgstr, $whoami, implode( ', ', $accepted ) ),
					"<info>Exemple:</info> sudo -u {$accepted[0]} lib/Cake/Console/cake {$command} [...]"
				);
			}
		}

		/**
		 * Vérifie que le département configuré fasse bien partie des départements
		 * ayant le droit d'utiliser le shell.
		 *
		 * @see Configure::write() pour la clé Cg.departement
		 */
		public function checkDepartement( $departement ) {
			$departement = (array)$departement;

			if( !in_array( Configure::read( 'Cg.departement' ), $departement ) ) {
				$msgstr = __n( 'Ce shell est réservé au CG %s', 'Ce shell est réservé aux CGs %s', count( $departement ) );
				$this->error( sprintf( $msgstr, implode( ', ', $departement ) ) );
			}
		}

		/**
		 * Surcharge de la méthode pour vérifier que l'utilisateur qui lance la
		 * commande soit le même que l'utilisateur du serveur web.
		 *
		 * @see AppShell::checkCliUser()
		 */
		public function startup() {
			parent::startup();

			$this->checkCliUser();
		}
	}
?>