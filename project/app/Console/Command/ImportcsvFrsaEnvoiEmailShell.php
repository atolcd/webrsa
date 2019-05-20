<?php
	/**
	 * Code source de la classe ImportcsvFrsaEnvoiEmailShell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 *
	 * Se lance avec :  sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake ImportcsvFrsaEnvoiEmail -app app
	 *
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses('CakeEmail', 'Network/Email');

	/**
	 * La classe ImportcsvFrsaEnvoiEmailShell permet d'envoyer par email les imports rejetés de FRSA
	 *
	 * @package app.Console.Command
	 */
	class ImportcsvFrsaEnvoiEmailShell extends XShell
	{
		/**
		 * Méthode principale.
		 */
		public function main() {
			$success = false;
			try {
				$Email = new CakeEmail('import_frsa');
				$Email->emailFormat('html');

				$fichiersAJoindre = array();
				$files = array_diff(scandir(LOGS, 1), array('..', '.'));

				foreach( $files as $file ) {
					if(strpos($file, 'ImportcsvFrsa') !== false && strpos($file, '_rejects_'.date('Ymd')) !== false) {
						$fichiersAJoindre[] = LOGS.$file;
					}
				}

				if(!empty($fichiersAJoindre)) {
					$Email->attachments($fichiersAJoindre);
					$mailBody = 'Bonjour,<br><br> L\'import de FRSA a été réalisé avec '. count($fichiersAJoindre) . ' rejet(s) que vous trouverez en pièce jointe.';
				} else {
					$mailBody = 'Bonjour,<br><br> L\'import de FRSA a été réalisé sans rejets';
				}

				$result = $Email->send( $mailBody );
				$success = !empty( $result );
			} catch( Exception $e ) {
				$this->log( $e->getMessage(), LOG_ERROR );
				$success = false;
			}

			if( $success ) {
				$this->out( 'Mail envoyé' );
			}
			else {
				$this->out( 'Mail non envoyé' );
			}
		}
  }
?>