<?php
	/**
	 * Code source de la classe Dashboard.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Dashboard ...
	 *
	 * @package app.Model
	 */
	class Dashboard extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Dashboard';

		/**
		 * Table utilisé par le modèle
		 *
		 * @var string
		 */
		public $useTable = false;

		/**
		 * Modèles utilisé par ce modèle
		 */
		public $uses = array(
			'WebrsaRecherche'
		);

		/**
		 * Ajoute le nombre de résultats d'un role utilisant un moteur de recherche/cohorte
		 *
		 * @param array $roles
		 * @return array
		 */
		public function addCounts(array $roles) {
			Cache::config('one day', array(
				'engine' => 'File',
				'duration' => '+1 day',
				'path' => CACHE,
				'prefix' => 'cake_oneday_'
			));

			foreach ($roles as $kRole => $role) {
				$keyCache = 'role_'.Hash::get($role, 'Role.id');
				$cache = Cache::read($keyCache, 'one day');

				if ($cache) {
					$roles[$kRole] = $cache;
					continue;
				}

				$roles[$kRole]['Role']['date_count'] = date('d/m/Y G:i');
				foreach ($role['Actionrole'] as $kActionrole => $actionrole) {
					// /controller/action[/var:value]+||[number]+
					if (!preg_match('/\/([\w]+)\/([\w]+)((?:\/[\w]+:[\w]*|\/[\d]+)*)$/', (string)Hash::get($actionrole, 'url'), $matches)) {
						continue;
					}

					list(, $controller, $action, $flattenParams) = $matches;
					$roles[$kRole]['Actionrole'][$kActionrole]['controller'] = $controller;
					$roles[$kRole]['Actionrole'][$kActionrole]['action'] = $action;

					$searchKey = Inflector::camelize($controller).'.'.$action;

					if (isset($this->WebrsaRecherche->searches[$searchKey])) {
						$params = array();
						foreach (explode('/', trim($flattenParams, '/')) as $param) {
							if (strpos($param, ':')) {
								list($key, $value) = explode(':', $param);
								$params = Hash::merge($params, Hash::expand(array($key => $value), '__'));
							}
						}

						$rechercheParams = $this->WebrsaRecherche->searches[$searchKey];
						$Modelrecherche = ClassRegistry::init($rechercheParams['modelRechercheName']);
						$query = $Modelrecherche->searchConditions($Modelrecherche->searchQuery(), $params['Search']);

						$roles[$kRole]['Actionrole'][$kActionrole]['count'] =
							ClassRegistry::init($rechercheParams['modelName'])->find('count', $query)
						;
					}
				}

				Cache::write($keyCache, $roles[$kRole], 'one day');
			}

			return $roles;
		}
	}
?>