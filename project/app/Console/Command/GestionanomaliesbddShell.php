<?php
	/**
	 * Fichier source de la classe GenerationpdfsShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses( 'Controller', 'Controller' );
	App::uses( 'Component', 'Controller/Component' );
	App::uses( 'GestionanomaliesbddComponent', 'Controller/Component' );

	/**
	 * La classe GenerationpdfsShell ...
	 *
	 * @see http://bakery.cakephp.org/articles/Jippi/2007/12/02/emailcomponent-in-a-cake-shell
	 *
	 * @package app.Console.Command
	 */
	class GestionanomaliesbddShell extends XShell
	{

		public $uses = array( );
		public $Controller = null;
		public $solve = false;
		public $defaultParams = array(
			'log' => false,
			'logpath' => LOGS,
			'verbose' => false,
			'solve' => false,
		);

		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->addOption( 'solve', array(
				'short' => 's',
				'help' => '',
				'default' => false,
				'boolean' => true
			) );
			return $parser;
		}

		protected function _showParams() {
			parent::_showParams();
			$this->out( '<info>'._d('gestionanomaliebdd', 'Shells::Detailsvaleurs').'</info><important>'.($this->params['solve'] ? 'true' : 'false').'</important>' );
		}

		/**
		 * Initialisation du contrôleur et du component, lecture des paramètres.
		 */
		public function initialize() {
			parent::initialize();
			$this->Controller = new Controller();
			$this->Gestionanomaliesbdd = new GestionanomaliesbddComponent( new ComponentCollection() );
			$this->Gestionanomaliesbdd->initialize( $this->Controller );
		}

		/**
		 *
		 */
		public function main() {
			$fonctions = array(
				'adressesSansAdressesfoyers',
				'prestationsMemeNatureEtMemeRole',
				'adressesPourPlusieursAdressesfoyers',
				'adressesfoyersEnDoublon',
				'personnesSansPrestationSansEntreeMetier',
			);

			$out = array( );
			$this->XProgressBar->start( count( $fonctions ) );
			foreach( $fonctions as $fonction ) {

				$this->XProgressBar->next( 1, '<info>'.$fonction.'</info>' );
				$return = $this->Gestionanomaliesbdd->{$fonction}( $this->params['solve'] );
				$return = ( is_bool( $return ) ? ( ( $return ) ? 'true' : 'false' ) : $return );
				$out[] = str_pad( '<info>'.__d( 'gestionanomaliebdd', "Component::{$fonction}" ).'</info>', 65 ).'<important>'.$return.'</important>';
			}

			$this->out();
			$this->out();
			$this->out( $out );
		}

	}
?>