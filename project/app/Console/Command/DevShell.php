<?php
	/**
	 * Fichier source de la classe DevShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe DevShell ...
	 *
	 * @package app.Console.Command
	 */
	class DevShell extends XShell
	{

		/**
		 *
		 */
		public function main() {
			debug($this->params);
		}

	}
?>