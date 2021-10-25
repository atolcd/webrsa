<?php
/**
 * Fichier source de la classe SessionAclUtility.
 *
 * PHP 5.3
 *
 * @package SessionAcl.Utility
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

App::uses('SessionAcl', 'SessionAcl.Model/Datasource');

/**
 * La classe SessionAclUtility offre des méthodes utiles pour gérer les ACLs
 *
 * @package SessionAcl.Utility
 */
abstract class SessionAclUtility
{
	/**
	 * @var boolean
	 */
	protected static $_initialized = false;

	/**
	 * Classe de gestion des Acl
	 *
	 * @var AclInterface
	 */
	protected static $_acl;

	/**
	 * Variables "globales" de la classe
	 *
	 * @var array
	 */
	protected static $_data = array();

	/**
	 * Initialisation de la classe
	 *
	 * @throws Exception
	 */
	public static function initialize() {
		if (!static::$_initialized) {
			static::$_acl = SessionAcl::get('acl');

			if (empty(static::$_acl)) {
				throw new Exception("La classe SessionAcl doit être initialisé avant de pouvoir utiliser ".__CLASS__);
			}
		}
	}

	/**
	 * Permet de reconstruire les acos de type controllers en fonction des
	 * controllers de l'application
	 *
	 * @param boolean $transaction effectue une transaction begin/commit/rollback
	 */
	public static function updateAcos($transaction = true) {
		static::initialize();

		if (method_exists(static::$_acl->Aco, 'getDataSource') && $transaction) {
			static::$_acl->Aco->begin();
			static::deleteOrphans(static::$_acl->Aco, false);
		}

		static::initUpdate(static::$_acl->Aco);
		$success = static::addMissingsAcos(false) && static::deleteNotExistingAcos(false);

		if (method_exists(static::$_acl->Aco, 'getDataSource') && $transaction) {
			if ($success) {
				static::deleteOrphans(static::$_acl->Aco, false);
				static::fastRecover(static::$_acl->Aco, false);
				static::$_acl->Aco->commit();
			} else {
				static::$_acl->Aco->rollback();
			}
		}
	}

	/**
	 * Permet d'obtenir la liste des acos de type controllers en fonction des
	 * controlleurs de l'application
	 *
	 * @return array array('controllers/Moncontroller/monaction', ...)
	 */
	protected static function _getAppControllersActionsTree() {
		App::uses("AppController", 'Controller');
		$appMethods = get_class_methods('AppController');

		$controllers = App::objects('controller');

		$acos = array('controllers');
		foreach ($controllers as $controllerName) {
			App::uses($controllerName, 'Controller');

			$reflection = new ReflectionClass($controllerName);
			if ($reflection->isAbstract() || $controllerName === 'AppController') {
				continue;
			}

			$controllerNameShort = preg_replace('/Controller$/', '', $controllerName);

			$acos[] = 'controllers/'.$controllerNameShort;

			$controllerMethods = get_class_methods($controllerName);
			$classMethods = array_diff($controllerMethods, $appMethods);

			foreach ($classMethods as $action) {
				if ($action[0] !== '_') {
					$acos[] = 'controllers/'.$controllerNameShort.'/'.$action;
				}
			}
		}

		sort($acos);

		return $acos;
	}

	/**
	 * Assure l'existance de l'aco 'controllers', parent de tout les controllers
	 *
	 * @return integer id de l'aco 'alias' => 'controllers'
	 */
	protected static function _findOrCreateAcoControllers() {
		$query = array(
			'fields' => 'id',
			'recursive' => -1,
			'conditions' => array('alias' => 'controllers')
		);

		$result = static::$_acl->Aco->find('first', $query);

		if (empty($result)) {
			static::$_acl->Aco->create(array('alias' => 'controllers'));
			static::$_acl->Aco->save(null, array('validate' => false, 'callbacks' => false, 'atomic' => false));
			$id = static::$_acl->Aco->id;
		} else {
			$id = Hash::get($result, static::$_acl->Aco->alias.'.id');
		}

		return $id;
	}

	/**
	 * Permet d'obtenir la liste complète des acos
	 *
	 * @return array array(	$aco_id => array(
	 *							'alias' => ...,
	 *							'parent_id' => ...,
	 *							'parent' => 'alias_du_parent')
	 *						'alias' => array('alias de aco' => $aco_id, ...))
	 */
	protected static function _getAll(Model $Model) {
		$alias = $Model->alias;
		$results = $Model->find('all', array('recursive' => -1));
		$parent = static::$_data['tree']['parent'];

		$items = array();
		foreach (Hash::extract($results, '{n}.'.$alias) as $item) {
			$items[$item['id']] = $item;
			$items['alias'][$item['alias']] = $item['id']; // Deux alias identiques s'écrasent
		}

		foreach ($items as $id => $item) {
			if ($id !== 'alias' && isset($item[$parent]) && isset($items[$item[$parent]])) {
				$items[$id]['parent'] = $items[$item[$parent]]['alias'];
			}
		}

		return $items;
	}

	/**
	 * Ajoute les acos manquant
	 *
	 * @param boolean $transaction effectue une transaction begin/commit/rollback
	 *
	 * @return boolean success (save)
	 */
	public static function addMissingsAcos($transaction = true) {
		$success = true;
		$parent = static::$_data['tree']['parent'];

		if ($transaction) {
			static::$_acl->Aco->begin();
		}

		foreach (static::$_data['appControllers'] as $path) {
			preg_match('/controllers(?:\/([^\/]+))?(?:\/([^\/]+))?/', $path, $matches);
			$exists = false;

			if (isset($matches[2])) {
				foreach (static::$_data['existing'] as $id => $values) {
					if ($id === 'alias' || !isset($values['parent'])) {
						continue;
					}

					if ($values['alias'] === $matches[2]
						&& $values['parent'] === $matches[1]
					) {
						$exists = true;
						break;
					}
				}

				$parent_id = static::$_data['existing']['alias'][$matches[1]];

			} elseif (isset($matches[1])) {
				$exists = isset(static::$_data['existing']['alias'][$matches[1]]);
				$parent_id = static::$_data['controllersAco_id'];
			} else {
				$exists = true;
			}

			if (!$exists) {
				$data = array(
					$parent => $parent_id,
					'alias' => end($matches)
				);

				static::$_acl->Aco->create($data);
				$success = $success && static::$_acl->Aco->save(null, array('validate' => false, 'callbacks' => false, 'atomic' => false));
				static::$_data['existing']['alias'][$data['alias']] = static::$_acl->Aco->id;
				static::$_data['existing'][static::$_acl->Aco->id] = $data
					+ array('parent' => static::$_data['existing'][$parent_id]['alias']);

				$Event = new CakeEvent(
					'SessionAcl.insert',
					static::$_acl->Aco,
					static::$_data['existing'][static::$_acl->Aco->id]
				);
				static::$_acl->Aco->getEventManager()->dispatch($Event);
			}
		}

		if ($transaction) {
			if ($success) {
				static::$_acl->Aco->commit();
			} else {
				static::$_acl->Aco->rollback();
			}
		}

		return $success;
	}

	/**
	 * Supprime les acos en trop (de type controller)
	 *
	 * @param boolean $transaction effectue une transaction begin/commit/rollback
	 *
	 * @return boolean success (delete)
	 */
	public static function deleteNotExistingAcos($transaction = true) {
		$success = true;
		$isAcoController = array();
		$left = static::$_data['tree']['left'];
		$right = static::$_data['tree']['right'];
		$parent = static::$_data['tree']['parent'];

		// Trouve les controllers
		foreach (static::$_data['existing'] as $id => $values) {
			if (Hash::get($values, $parent) === static::$_data['controllersAco_id']) {
				$isAcoController[] = $id;
			}
		}

		if ($transaction) {
			static::$_acl->Aco->begin();
		}

		foreach (static::$_data['existing'] as $id => $values) {
			$delete = false;
			if ($id === 'alias') {
				continue;
			}

			if ($values[$parent] === static::$_data['controllersAco_id']) {
				$name = 'controllers/'.$values['alias'];
				$delete = !in_array($name, static::$_data['appControllers']);

			} elseif (in_array($values[$parent], $isAcoController)) {
				$name = 'controllers/'.$values['parent'].'/'.$values['alias'];
				$delete = !in_array($name, static::$_data['appControllers']);
			}

			if ($delete) {
				$conditions = array(
					static::$_acl->Aco->alias.'.'.$left.' >=' => $values[$left],
					static::$_acl->Aco->alias.'.'.$right.' <=' => $values[$right]
				);
				$success = $success
					&& static::_deleteAll(static::$_acl->Aco, $conditions);
			}
		}

		if ($transaction) {
			if ($success) {
				static::$_acl->Aco->commit();
			} else {
				static::$_acl->Aco->rollback();
			}
		}

		return $success;
	}

	/**
	 * Supprime les acos qui ont un alias vide
	 * @return bool
	 */
	public static function deleteNotExistingAliasAcos() {
		$Model = static::$_acl->Aco;
		return $Model->deleteAll( array('Aco.alias' => ''), false );
	}

	/**
	 * Rempli static::$_data des données indispensables au bon traitement d'une
	 * mise à jour des acos
	 *
	 * @param Model $Model Model pour lequel il faut initialiser l'utilitaire
	 */
	public static function initUpdate(Model $Model) {
		static::$_data['tree'] = $Model->Behaviors->Tree->settings[$Model->alias];
		static::$_data['controllersAco_id'] = static::_findOrCreateAcoControllers();
		static::$_data['existing'] = static::_getAll($Model);
		static::$_data['appControllers'] = static::_getAppControllersActionsTree();
		static::$_data['requesters'] = static::_getRequesters();
	}

	/**
	 * Supprime les acos orphelins
	 *
	 * @param Model $Model Aro | Aco
	 * @param boolean $transaction effectue une transaction begin/commit/rollback
	 */
	public static function deleteOrphans(Model $Model, $transaction = true) {
		extract($Model->Behaviors->Tree->settings[$Model->alias]);
		$oldAlias = $Model->alias;
		$Model->alias = 'a';

		$query = array(
			'fields' => array('a.'.$left, 'a.'.$right, 'a.alias'),
			'recursive' => -1,
			'joins' => array(
				array(
					'type' => 'LEFT',
					'table' => $Model->useTable,
					'alias' => 'b',
					'conditions' => 'a.'.$parent.' = b.id'
				)
			),
			'conditions' => array(
				'b.id IS NULL',
				'a.'.$parent.' IS NOT NULL'
			)
		);

		$results = $Model->find('all', $query);

		$success = true;
		if (!empty($results)) {
			if ($transaction) {
				$Model->begin();
			}

			foreach ($results as $result) {
				static::_deleteAll($Model, array(
					$Model->alias.'.'.$left.' >=' => Hash::get($result, 'a.'.$left),
					$Model->alias.'.'.$right.' <=' => Hash::get($result, 'a.'.$right)
				));
			}
		}

		$Model->alias = $oldAlias;

		if ($transaction) {
			if ($success) {
				$Model->commit();
			} else {
				$Model->rollback();
			}
		}

		return $success;
	}

	/**
	 * Permet une réparation rapide des lft et rght
	 *
	 * @param Model $Model Aco
	 * @param boolean $transaction effectue une transaction begin/commit/rollback
	 */
	public static function fastRecover(Model $Model, $transaction = true) {
		extract($Model->Behaviors->Tree->settings[$Model->alias]);
		$tree = static::_buildTree($Model);

		$cpt = 1;
		$dirs = array();
		foreach ($tree as $sub_id => $values) {
			static::_buildLftRght($sub_id, $values, $dirs, $cpt);
		}

		$success = true;

		if ($transaction) {
			$Model->begin();
		}

		foreach ($dirs as $id => $dirs) {
			$Model->create(false);
			$Model->id = $id;
			$data = array(
				$left => $dirs['lft'],
				$right => $dirs['rght'],
			);

			$success = $Model->save($data, array('validate' => false, 'callbacks' => false, 'atomic' => false));
		}

		if ($transaction) {
			if ($success) {
				$Model->commit();
			} else {
				$Model->rollback();
			}
		}

		return $success;
	}

	/**
	 * Permet d'obtenir l'arbre sous forme d'array
	 * ex: [$id1 => [$id2 => [$id2 => [], $id3 => []]]]
	 *
	 * @param Model $Model
	 * @param integer $parent_id
	 * @param array $tree arbre en construction
	 * @return array
	 */
	protected static function _buildTree(Model $Model, $parent_id = null, $tree = array()) {
		extract($Model->Behaviors->Tree->settings['Aco']);

		$results = $Model->find('all', array(
			'conditions' => array($scope, $parent => $parent_id),
			'fields' => array($Model->primaryKey),
			'recursive' => $recursive
		));

		foreach (Hash::extract((array)$results, '{n}.'.$Model->alias.'.id') as $id) {
			$tree[$id] = static::_buildTree($Model, $id);
		}

		return $tree;
	}

	/**
	 * Permet à partir d'un arbre sous forme d'array d'obtenir pour chaque id,
	 * le left et le right
	 *
	 * @see static::_buildTree()
	 *
	 * @param integer $id
	 * @param array $tree
	 * @param array $res
	 * @param integer $cpt
	 */
	protected static function _buildLftRght($id, $tree, &$res, &$cpt) {
		$res[$id]['lft'] = $cpt;
		$cpt++;

		foreach ($tree as $sub_id => $values) {
			static::_buildLftRght($sub_id, $values, $res, $cpt);
		}

		$res[$id]['rght'] = $cpt;
		$cpt++;
	}

	/**
	 * Obtention des droits en une seule requête (postgresql)
	 *
	 * @param Model $Model User | Group
	 * @param integer $foreign_key User.id | Group.id
	 * @param array $params
	 * @return array Liste des chemins en clef avec accès en valeur (boolean)
	 *				 ex: array('controllers/Module/action' => true, ...)
	 */
	public static function fastPostrgresGetAll(Model $Model, $foreign_key, array $params = array()) {
		$params += array(
			'Permission' => 'aros_acos',
			'Aro' => 'aros',
			'Aco' => 'acos',
			'keyAccess' => '_create'
		);

		$Aro = ClassRegistry::init('Aro');
		$aroTree = $Aro->Behaviors->Tree->settings[$Aro->alias];
		$Aco = ClassRegistry::init('Aco');
		$acoTree = $Aco->Behaviors->Tree->settings[$Aco->alias];

		$query = <<<EOT
		SELECT access, path
		FROM (

			SELECT

				(array_agg({$params['keyAccess']})
					OVER (PARTITION BY {$params['Aco']}.agg
						ORDER BY {$params['Aro']}.{$aroTree['left']} DESC)
				)[1] AS access,

				{$params['Aco']}.agg AS path

				FROM {$params['Permission']}

				JOIN {$params['Aro']} ON aro_id = {$params['Aro']}.id

				JOIN (
					SELECT *,
					array_to_string(
						(select array_agg(a.alias)
							FROM (SELECT * FROM {$params['Aco']} ORDER BY {$params['Aco']}.{$acoTree['left']}) a
							WHERE a.{$acoTree['left']} <= {$params['Aco']}.{$acoTree['left']}
							AND a.{$acoTree['right']} >= {$params['Aco']}.{$acoTree['right']}
						), '/'
					) AS agg
					FROM {$params['Aco']}
				) AS {$params['Aco']} ON aco_id = {$params['Aco']}.id

				WHERE {$params['Aro']}.id IN (
					SELECT aros_parent.id
					FROM {$params['Aro']} a
					JOIN {$params['Aro']} aros_parent
						ON aros_parent.{$aroTree['left']} <= a.{$aroTree['left']} AND aros_parent.{$aroTree['right']} >= a.{$aroTree['right']}
					AND a.model = '{$Model->alias}'
					AND a.foreign_key = $foreign_key
				)
				AND {$params['Permission']}.{$params['keyAccess']} IN ('1', '-1')

				ORDER BY
					agg,
					{$params['Aro']}.{$aroTree['left']}
		) AS sub

		GROUP BY access, path
EOT;

		foreach (Hash::extract((array)$Model->query($query), '{n}.{n}') as $value) {
			$droits[$value['path']] = (int)$value['access'];
		}

		$results = array();
		foreach (static::_getAppControllersActionsTree() as $path) {
			if (isset($droits[$path])) {
				$access = $droits[$path];
			} elseif (preg_match('/^controllers(\/[^\/]+)?/', $path, $match)
				&& isset($droits[$match[0]])
			) {
				$access = $droits[$match[0]];
			} elseif (isset($droits['controllers'])) {
				$access = $droits['controllers'];
			} else {
				$access = -1;
			}

			$results[$path] = $access > 0;
		}

		return $results;
	}

	/**
	 * Rétabli l'héritage si l'enfant possède les mêmes droits que le parent
	 *
	 * @return integer Delete count
	 * @param boolean $transaction effectue une transaction begin/commit/rollback
	 */
	public static function forceHeritage($transaction = true) {
		$Permission = ClassRegistry::init('Permission');

		$Dbo = $Permission->getDatasource();

		$conditions = array();
		foreach ($Permission->getAcoKeys($Permission->schema()) as $column) {
			$conditions[$Permission->alias.'.'.$column] = $Dbo->identifier($Permission->alias.'_sub.'.$column);
			$conditions[$Permission->alias.'.'.$column.' !='] = 0;
		}

		extract($Permission->Aro->Behaviors->Tree->settings[$Permission->Aro->alias]);

		$query = array(
			'fields' => array(
				$Permission->alias.'.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => $Permission->Aro->useTable,
					'alias' => $Permission->Aro->alias,
					'conditions' => $Dbo->getConstraint(
						'belongsTo', $Permission, $Permission->Aro, $Permission->Aro->alias, $Permission->belongsTo['Aro']
					),
				),
				array(
					'type' => 'INNER',
					'table' => $Permission->Aro->useTable,
					'alias' => $Permission->Aro->alias.'_sub',
					'conditions' => array(
						$Permission->Aro->alias.'_sub.id' => $Dbo->identifier($Permission->Aro->alias.'.'.$parent)
					),
				),
				array(
					'type' => 'INNER',
					'table' => $Permission->useTable,
					'alias' => $Permission->alias.'_sub',
					'conditions' => array(
						$Permission->alias.'_sub.'.$Permission->belongsTo['Aro']['foreignKey']
							=> $Dbo->identifier($Permission->Aro->alias.'_sub.id'),
						$Permission->alias.'_sub.'.$Permission->belongsTo['Aco']['foreignKey']
							=> $Dbo->identifier($Permission->alias.'.'.$Permission->belongsTo['Aco']['foreignKey']),
					)
				)
			),
			'conditions' => $conditions,
			'recursive' => -1
		);

		$results = $Permission->find('all', $query);
		$success = true;
		if (!empty($results)) {
			if ($transaction) {
				$Dbo->begin();
			}

			$success = $Permission->deleteAll(array('id' => Hash::extract($results, '{n}.'.$Permission->alias.'.id')), false);

			if ($transaction) {
				if ($success) {
					$Dbo->commit();
				} else {
					$Dbo->rollback();
				}
			}
		}

		return $success ? count($results) : false;
	}

	/**
	 * Effectue un delete propre sans callbacks
	 *
	 * @param Model $Model Aro | Aco
	 * @param array $conditions
	 * @return boolean success
	 */
	protected static function _deleteAll(Model $Model, $conditions = array()) {
		$success = true;
		$toDelete = $Model->find('all',
			array(
				'fields' => array(
					'Permission.id',
					$Model->alias.'.id',
					$Model->alias.'.alias',
					'parent.id',
					'parent.alias',
				),
				'joins' => array(
					array(
						'type' => 'LEFT',
						'table' => $Model->Permission->useTable,
						'alias' => 'Permission',
						'conditions' => 'aco_id = '.$Model->alias.'.id'
					),
					array(
						'type' => 'LEFT',
						'table' => $Model->useTable,
						'alias' => 'parent',
						'conditions' => 'parent.id = '.$Model->alias.'.parent_id'
					)
				),
				'conditions' => $conditions,
				'recursive' => -1
			)
		);
		$idsAroAco = Hash::extract($toDelete, '{n}.Permission.id');

		if (!empty($idsAroAco)) {
			// Cascade manuelle pour éviter de recalculer les left et right
			$success = $success && $Model->Permission->deleteAll(
				array('id' => $idsAroAco),
				false
			);
		}

		$success = $success && $Model->deleteAll(
			array('id' => array_unique(Hash::extract($toDelete, '{n}.'.$Model->alias.'.id'))),
			false
		);

		$Event = new CakeEvent('SessionAcl.delete', $Model, $toDelete);
		$Model->getEventManager()->dispatch($Event);

		return $success;
	}

	/**
	 * Permet d'obtenir la liste des aros
	 *
	 * @return array
	 */
	protected static function _getRequesters() {
		$models = App::objects('models');

		$requesters = array();
		foreach ($models as $modelName) {
			App::uses($modelName, 'Model');
			$reflet = new ReflectionClass($modelName);

			$properties = $reflet->getDefaultProperties();
			if (isset($properties['actsAs']['Acl']['type'])
				&& $properties['actsAs']['Acl']['type'] === 'requester'
			) {
				$Model = ClassRegistry::init($modelName);

				$aliasField = null;
				foreach ($Model->schema() as $fieldName => $params) {
					if ($params['type'] === 'string') {
						if (in_array($fieldName, array('name', 'username'))) {
							$aliasField = $fieldName;
							break;
						}

						$aliasField = $fieldName;
					}
				}

				$results = $Model->find('all', array('recursive' => -1));
				foreach ($results as $result) {
					$parent = null;
					if (method_exists($Model, 'parentNode')) {
						$Model->id = Hash::get($result, $modelName.'.id');
						$parent = $Model->parentNode();
					}

					$requesters[] = array(
						'model' => $modelName,
						'foreign_key' => Hash::get($result, $Model->alias.'.id'),
						'alias' => Hash::get($result, $Model->alias.'.'.$aliasField),
						'parent' => $parent
					);
				}
			}
		}

		return $requesters;
	}

	/**
	 * Ajoute les acos manquant
	 *
	 * @param boolean $transaction effectue une transaction begin/commit/rollback
	 *
	 * @return boolean success (save)
	 */
	public static function addMissingsAros($transaction = true) {
		$success = true;
		$parent = static::$_data['tree']['parent'];

		if ($transaction) {
			static::$_acl->Aro->begin();
		}

		$done = array();
		$todo = static::$_data['requesters'];

		while (!empty($todo)) {
			$count = 0;
			foreach ($todo as $key => $values) {
				$aro_parent_id = null;
				if (!empty($values['parent'])) {
					$models = array_keys($values['parent']);
					$modelName = current($models);

					// Structure : $values['parent'] = array($modelName => array('id' => $x))
					$id = $values['parent'][$modelName]['id'];
					if (isset($done[$modelName][$id])) {
						$aro_parent_id = $done[$modelName][$id];
						$values['parent'] = static::$_data['existing'][$aro_parent_id]['alias'];
					} else {
						continue;
					}
				}

				$values[$parent] = $aro_parent_id;

				$aro = static::$_acl->Aro->find('first',
					array(
						'conditions' => array(
							static::$_acl->Aro->alias.'.model' => $values['model'],
							static::$_acl->Aro->alias.'.foreign_key' => $values['foreign_key'],
						),
						'recursive' => -1
					)
				);

				if (!empty($aro)) {
					$done[$values['model']][$values['foreign_key']] = Hash::get($aro, static::$_acl->Aro->alias.'.id');
				} else {
					static::$_acl->Aro->create($values);
					static::$_acl->Aro->save(null, array('validate' => false, 'callbacks' => false, 'atomic' => false));

					static::$_data['existing'][static::$_acl->Aro->id] = $values;

					$Event = new CakeEvent(
						'SessionAcl.insert',
						static::$_acl->Aro,
						static::$_data['existing'][static::$_acl->Aro->id]
					);
					static::$_acl->Aro->getEventManager()->dispatch($Event);

					$done[$values['model']][$values['foreign_key']] = static::$_acl->Aro->id;
				}

				$count++;
				unset($todo[$key]);
			}

			// Si on a pas réussi à insérer de nouveaux aros, on arrete
			if ($count === 0) {
				break;
			}
		}

		if ($transaction) {
			if ($success) {
				static::$_acl->Aro->commit();
			} else {
				static::$_acl->Aro->rollback();
			}
		}

		return $success;
	}
	/**
	 * Supprime les aros en trop
	 *
	 * @param boolean $transaction effectue une transaction begin/commit/rollback
	 *
	 * @return boolean success (delete)
	 */
	public static function deleteNotExistingAros($transaction = true) {
		$success = true;
		$left = static::$_data['tree']['left'];
		$right = static::$_data['tree']['right'];
		$parent = static::$_data['tree']['parent'];

		// Trouve les controllers
		foreach (static::$_data['existing'] as $id => $values) {
			if ($id === 'alias') {
				continue;
			}

			App::uses($values['model'], 'Model');
			$Model = ClassRegistry::init($values['model']);

			$result = $Model->find('first',
				array(
					'fields' => 'id',
					'conditions' => array(
						'id' => $values['foreign_key']
					),
					'recursive' => -1
				)
			);

			if (empty($result)) {
				$success = static::$_acl->Aro->delete($id, false);

				if (!$success) {
					break;
				}
			}
		}

		return $success;
	}

	/**
	 * Compare les acos avec les controlleurs de l'application
	 *
	 * @see self::checkControllersActionsAcos()
	 *
	 * @params array $available paths ex: array('controllers/Module/action', ...)
	 * @return array array('extra' => (array)..., 'missing' => (array)...)
	 */
	public static function checkAcos(array $available) {
		if (empty(static::$_data['tree'])) {
			static::$_data['tree'] = static::$_acl->Aco->Behaviors->Tree->settings[static::$_acl->Aco->alias];
		}

		$tree = array();
		$existing = static::_getAll(static::$_acl->Aco);

		foreach ($existing as $id => $aco) {
			if (!isset($aco['alias'])) {
				continue;
			}

			$alias = $aco['alias'];
			$parent_id = $aco['parent_id'];
			while ($parent_id !== null) {
				$aco = $existing[$parent_id];
				$parent_id = $aco['parent_id'];
				$alias = $aco['alias'] . '/' . $alias;
			}

			$tree[] = $alias;
		}

		sort($tree);
		sort($available);

		return array(
			'extra' => array_diff($tree, $available),
			'missing' => array_diff($available, $tree)
		);
	}

	/**
	 * Effectue un checkAcos avec comme élément de comparaison les acos controllers
	 *
	 * @return type
	 */
	public static function checkControllersActionsAcos() {
		return static::checkAcos(static::_getAppControllersActionsTree());
	}
}