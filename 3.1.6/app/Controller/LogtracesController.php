<?php
	/**
	 * Code source de la classe LogtracesController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe LogtracesController ...
	 *
	 * @package app.Controller
	 */
	class LogtracesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Logtraces';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Group',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
		);
		
		public function index() {
			$total_duration = (integer)Configure::read('Module.Logtrace.total_duration');
	
			$extracted = array();
			foreach (array_reverse(file(TMP."logs/trace.log", FILE_IGNORE_NEW_LINES)) as $data) {
				if (preg_match('/([\d]{4})\-([\d]{2})\-([\d]{2}) ([\d]{2}):([\d]{2}):([\d]{2}) Trace: Page "(.*)" construite pour "(.*)" \((.*)\) en (.*) secondes. (.*) \/ (.*)\. ([\d]+)/', $data, $matches)) {
					list(, $year, $month, $day, $hour, $min, $sec, $url, $username, $ip, $loading_time, $mem_usage, $mem_allocated, $nb_models)
						= $matches;
					
					$timestamp = mktime($hour, $min, $sec, $month, $day, $year);
					// Ne traite que la derniere heure
					if ((time() - $timestamp) > $total_duration) {
						break;
					}
					
					$extracted[] = compact('year', 'month', 'day', 'hour', 'min', 'sec', 'url', 'username', 'ip', 'nb_models', 'timestamp') + array(
						'loading_time' => (float)str_replace(',', '.', $loading_time),
						'mem_usage' => $this->_standardize($mem_usage),
						'mem_allocated' => $this->_standardize($mem_allocated),
						'text' => $data
					);
				}
			}
			unset($log);
			$this->set('log', array_reverse($extracted));
		}
		
		protected function _standardize($mem) {
			preg_match('/([\d]+(?:\.[\d]+){0,1}) ([KMG]B)/', $mem, $matches);
			if ($matches[2] === 'KB') {
				$result = $matches[1] * 1024;
			} elseif ($matches[2] === 'GB') {
				$result = $matches[1] / 1024;
			} else {
				$result = $matches[1];
			}
			return round($result, 2);
		}
	}
?>
