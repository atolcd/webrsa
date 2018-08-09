<?php
/**
 * Accueil Shell
 *
 * This Shell allows the running of test suites via the cake command line
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html
 * @since         CakePHP(tm) v 1.2.0.4433
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Shell', 'Console');

/**
 * Provides a CakePHP wrapper around PHPUnit.
 * Adds in CakePHP's fixtures and gives access to plugin, app and core test cases
 *
 * @package       Cake.Console.Command
 */
class AccueilShell extends Shell {

	/**
	 * Main entry point to this shell
	 *
	 * sudo -u apache ./lib/Cake/Console/cake accueil
	 *
	 * @return void
	 */
	public function main() {
		$User = ClassRegistry::init( 'User' );
		$Referent = ClassRegistry::init( 'Referent' );

		$users = $User->find ('all', array ('recursive' => -1));
		$referents = $Referent->find ('all', array ('recursive' => -1));

		$ok = 0;
		$ko = 0;
		foreach ($users as $user) {
			$name = $this->_characters (strtoupper (iconv ('UTF-8', 'ASCII//TRANSLIT//IGNORE', $user['User']['nom'])));
			$firstname = $this->_characters (strtoupper (iconv ('UTF-8', 'ASCII//TRANSLIT//IGNORE', $user['User']['prenom'])));
			$matched = false;

			foreach ($referents as $referent) {
				$nom = $this->_characters (strtoupper (iconv ('UTF-8', 'ASCII//TRANSLIT//IGNORE', $referent['Referent']['nom'])));
				$prenom = $this->_characters (strtoupper (iconv ('UTF-8', 'ASCII//TRANSLIT//IGNORE', $referent['Referent']['prenom'])));

				if ($nom === $name && $prenom === $firstname) {
					$query = 'UPDATE users SET accueil_referent_id = '.$referent['Referent']['id'].', accueil_reference_affichage = \'REFER\' WHERE id = '.$user['User']['id'].';';
					$User->query($query);
					$ok++;
					$matched = true;
					break;
				}
			}

			if (!$matched) {
				$ko++;
			}
		}

		$this->out('ok = '.$ok);
		$this->out('ko = '.$ko);
	}

	private function _characters ($word) {
		return str_replace(array ('-', ' ', '.', '_'), '', $word);
	}

}