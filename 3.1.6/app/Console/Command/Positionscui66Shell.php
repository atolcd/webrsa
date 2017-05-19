<?php
	/**
	 * Fichier source de la classe Positionscui66Shell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('XShell', 'Console/Command');

	/**
	 * La classe Positionscui66Shell
	 *
	 * @package app.Console.Command
	 */
	class Positionscui66Shell extends XShell
	{
		/**
		 * Modèles utilisés par ce shell
		 *
		 * @var array
		 */
		public $uses = array('Cui66');

		/**
		 * Mise à jour de la position des CUIs
		 */
		public function main() {
			$this->out("Mise à jour des positions du CUI...");
			$this->Cui66->WebrsaCui66->updatePositionsCuisByConditions(array());
			$this->out("Mise à jour terminé avec succès.");
		}
	}