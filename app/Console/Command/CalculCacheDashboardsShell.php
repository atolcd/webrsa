<?php
	/**
	 * Code source de la classe CalculCacheDashboardsShell.
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 App::uses('XShell', 'Console/Command');
	 
	/**
	 * La classe CalculCacheDashboardsShell
	 *
	 * @package app.Console.Command
	 */
	class CalculCacheDashboardsShell extends XShell
	{	
		/**
		 * Méthode principale.
		 */
		public function main() {
			Cache::config('one day', array(
				'engine' => 'File',
				'duration' => '+1 day',
				'path' => CACHE,
				'prefix' => 'cake_oneday_'
			));
			
			$Role = ClassRegistry::init('Role');
			$Dashboard = ClassRegistry::init('Dashboard');
			
			$query = array(
				'contain' => array('Actionrole'),
				'conditions' => array('Role.actif' => 1)
			);
			$results = $Role->find('all', $query);
			
			$count = count($results);
			
			$this->out();
			$this->out("Calcul du nombre de résultats des moteurs de recherche à afficher dans le tableau de bord...");
			$this->out();
			
			foreach ((array)$results as $key => $result) {
				$this->out("Calcul du nombre de résultats de '".Hash::get($result, 'Role.name')."' ".($key+1)."/".$count);
				
				$keyCache = 'role_'.Hash::get($result, 'Role.id');
				Cache::delete($keyCache, 'one day');
				
				$Dashboard->addCounts(array($result));
			}
			
			$this->out();
			$this->out("Mise en cache du nombre de résultats effectué avec succès pour ".$count." rôles");
		}
	}
?>