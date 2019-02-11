<#include "../freemarker_functions.ftl">
<?php
	/**
	 * Code source de la classe ${name}.
	 *
<#if php_version??>
	 * PHP ${php_version}
	 *
</#if>
	 * @package app.Console.Command
	 * @license ${license}
	 */
	App::uses( 'AppShell', 'Console/Command' );

	/**
	 * La classe ${name} ...
	 *
	 * @package app.Console.Command
	 */
	class ${name} extends AppShell
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
		 * Démarrage du shell
		 */
		public function startup() {
			parent::startup();
		}

		/**
		 * Lignes de bienvenue.
		 */
		protected function _welcome() {
			parent::_welcome();
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			$this->_stop( self::SUCCESS );
		}

		/**
		 * Paramétrages et aides du shell.
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();
			return $Parser;
		}
	}
?>