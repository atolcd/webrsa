<?php
	/**
	 * Code source de la classe TagShell.
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 App::uses( 'XShell', 'Console/Command' );
	 App::uses( 'ConnectionManager', 'Model' );
	 
	/**
	 * La classe TagShell permet d'obtenir les correspondances entre les personne_id 
	 * de différents dossiers selon le nom/prenom/dtnai/nir
	 *
	 * @package app.Console.Command
	 */
	class TagShell extends XShell
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
		 * Initialisation: lecture des paramètres, on s'assure d'avoir une connexion
		 * valide
		 */
		public function startup() {
			parent::startup();
			try {
				$this->connection = ConnectionManager::getDataSource( $this->params['connection'] );
			}
			catch( Exception $e ) {

			}
		}

		/**
		 * Lignes de bienvenue.
		 */
		protected function _welcome() {
			parent::_welcome();
		}
		
		/**
		 * Méthode principale. On calcule la position des tags
		 */
		public function main() {
			$this->out();

			$Tag = ClassRegistry::init( 'Tag' );
			
			if ( $Tag->updateEtatTagByConditions() ) {
				$this->out( "Mise à jour de l'état des tags effectué." );
			}
			else {
				$this->out( "Une erreur s'est produite lors de la mise à jour de l'état des tags." );
			}
			
			$this->out();
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