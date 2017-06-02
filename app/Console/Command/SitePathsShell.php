<?php
	/**
	 * Fichier source de la classe SitePathsShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	
	App::uses('XShell', 'Console/Command');

	/**
	 * La classe SitePathsShell
	 * 
	 * exemple d'utilisation :
	 *	sudo -u www-data lib/Cake/Console/cake SitePaths -o graphviz_views.dot && dot -K fdp -T png -o ./graphviz_views.png ./graphviz_views.dot
	 *	sudo -u www-data lib/Cake/Console/cake SitePaths -o graphviz_personnes.dot -r /personne/i && dot -K fdp -T png -o ./graphviz_personnes.png ./graphviz_personnes.dot
	 * 
	 *
	 * @package app.Console.Command
	 */
	class SitePathsShell extends XShell
	{
		/**
		 * Liste des options et de leurs valeurs par défaut
		 * 
		 * @var array
		 */
		public $options = array(
			'view' => array(
				'short' => 'v',
				'help' => 'Nom du répertoire de vue',
				'default' => 'View'
			),
			'output' => array(
				'short' => 'o',
				'help' => 'Permet de spécifier le fichier de sortie',
				'default' => 'graphviz.dot'
			),
			'action' => array(
				'short' => 'a',
				'help' => 'affiche les liens entre les actions (très lourd)',
				'default' => false
			),
			'regex' => array(
				'short' => 'r',
				'help' => 'Défini le Regex utilisé pour ajouter un controller',
				'default' => '.*'
			)
		);
		
		/**
		 * Génère un fichier .dot
		 */
		public function main() {
			// "'controller' => 'nom_du_controller', 'action' => 'nom_de_l_action'" => match
			$urlRegex = "/['\"]controller['\"][\s]*=>[\s]*['\"]([\w]+)['\"],[\s]*'action'[\s]*=>[\s]*['\"]([\w]+)['\"]/";
			
			// "/nom_du_controller/nom_de_l_action" => match
			$urlRegex2 = "/['\"]\/([\w]+)\/([\w]+)(?:\/[\/\w#.\-]*)?['\"]/";
			
			$views = $this->_getViewList(APP.$this->params['view']);
			$results = array();
			$matches = null;
			
			foreach ($views as $view) {
				$url = self::_pathToControllerAction($view);
				$file = file_get_contents($view);
				$offset = 0;
				
				while (preg_match($urlRegex, $file, $matches, PREG_OFFSET_CAPTURE, $offset)
					|| preg_match($urlRegex2, $file, $matches, PREG_OFFSET_CAPTURE, $offset)
				) {
					$offset = $matches[0][1] + strlen($matches[0][0]);
					$toController = Inflector::camelize($matches[1][0]);
					
					if (preg_match($this->params['regex'], $url['controller']) 
						|| preg_match($this->params['regex'], $toController)
					) {
						$results[$url['controller']][$url['action']][$toController][$matches[2][0]] = true;
					}
				}
			}
			
			$this->createFile($this->params['output'], self::_renderDigraph($results));
		}
		
		/**
		 * Permet d'obtenir toutes les chemins de vues de façon récursive
		 * 
		 * @param string $path - Chemin du dossier de vues
		 * @return array - Liste des chemins vers les vues
		 */
		protected function _getViewList($path) {
			$files = array();
			foreach (scandir($path) as $fileName) {
				if (in_array($fileName, array('.', '..'))) {
					continue;
				}
				
				if (is_dir($path.DIRECTORY_SEPARATOR.$fileName)) {
					$files = array_merge(
						$files,
						$this->_getViewList($path.DIRECTORY_SEPARATOR.$fileName)
					);
				} elseif (preg_match('/\.ctp$/', $fileName)) {
					$files[] = $path.DIRECTORY_SEPARATOR.$fileName;
				}
			}
			
			return $files;
		}
		
		/**
		 * Transforme un chemin vers une vue en url type cakephp
		 * 
		 * @param string $path - .../app/View/Moncontroller/monaction.ctp
		 * @return array - array('controller' => 'Moncontroller', 'action' => 'monaction')
		 */
		protected function _pathToControllerAction($path) {
			list(, $controller, $action) = explode('/', substr($path, strlen(APP), -4));
			
			return compact('controller', 'action');
		}
		
		/**
		 * Transforme une liste de vues en fichier digraph
		 * 
		 * @param array $links
		 * @return string
		 */
		protected function _renderDigraph($links) {
			$output = array('digraph G {');
			
			// On commence à déssiner les controllers
			foreach ($links as $controller => $valueController) {
				$output[] = "\t$controller [shape=box];";
				
				// On déssine les actions
				foreach ($valueController as $action => $connexions) {
					// Fréquent sur webrsa, add_edit = edit
					if ($action === 'add_edit') {
						$action = 'edit';
					}
					
					// Crée les actions et leurs liens avec le contrôleur
					if ($this->params['action']) {
						$output[] = "\t".$controller.'_'.$action.' [label="'.$action.'"];';
						$output[] = "\t".$controller.' -> '.$controller.'_'.$action.' [label="'.$action.'"];';
					}
					
					self::_parseLinks($output, $controller, $action, $connexions);
				}
			}
			
			$output[] = '}';
			
			// Note : les points provoque des erreurs
			return str_replace('.', '_', implode("\n", $output));
		}
		
		/**
		 * Ajoute les fleches à output en fonction de la valeur de self::$options['action']
		 * 
		 * @param string $output
		 * @param string $controller
		 * @param string $action
		 * @param array $connexions
		 * @return string
		 */
		protected function _parseLinks(&$output, $controller, $action, $connexions) {
			// On dessine les fleches entre les actions
			foreach ($connexions as $controller2 => $actions) {
				// Si action est activé, on fait la relation entre actions
				if ($this->params['action']) {
					foreach (array_keys($actions) as $action2) {
						$output[] = "\t".$controller.'_'.$action.' -> '.$controller2.'_'.$action2.';';
					}
					
				// Si action est désactivé, on fait les relations entre les controlleurs uniquement
				} elseif ($controller !== $controller2) {
					$output[$controller.'_'.$controller2] = "\t".$controller.' -> '.$controller2.';';
				}
			}
			
			return $output;
		}
		
		/**
		 * Ajoute les options présentes dans $this->options
		 * 
		 * @return ConsoleOptionParser
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			
			$parser->addOptions($this->options);
			
			return $parser;
		}
	}