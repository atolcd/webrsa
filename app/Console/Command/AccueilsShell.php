<?php
/**
 * Code source de la classe ChecksShell.
 *
 * PHP 5.3
 *
 * @package app.Console.Command
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses( 'XShell', 'Console/Command' );

class AccueilsShell extends Shell {

	/**
	 * Main entry point to this shell
	 *
	 * sudo -u apache lib/Cake/Console/cake Accueils
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