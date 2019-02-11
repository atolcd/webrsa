<?php
	/**
	 * Code source de la classe WebrsaModelUtility.
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe WebrsaModelUtility fourni une palette d'outils pour travailler sur les modeles et les requetes
	 * 
	 * @package app.Utility
	 */
	class WebrsaModelUtility
	{
		/**
		 * Permet d'obtenir la clef dans les jointures de la query en fonction d'un nom de modele (alias)
		 * ex : 
		 *	$query['joins'][223]['alias'] = 'Personne';
		 *	findJoinKey('Personne', $query); // Renvoi (int) 223
		 * 
		 * @param string $modelName
		 * @param array $query
		 * @return integer
		 */
		public static function findJoinKey($modelName, array $query) {
			foreach ((array)Hash::get($query, 'joins') as $key => $jointure) {
				if (Hash::get($jointure, 'alias') === $modelName) {
					return $key;
				}
			}
			return false;
		}
		
		/**
		 * Permet de changer l'ordre des jointures d'un query
		 * 
		 * @param array $modelNames liste des modeles dans l'ordre de priorité (ex: array(model1, model2, ...))
		 * @param array $query contenant une clef joins
		 * @return array
		 */
		public static function changeJoinPriority($modelNames, array $query) {
			$newQuery = $query;
			$newQuery['joins'] = array();
			$noPriorityJoins = array();
			
			// On commence par mettre de coté les jointures qui ne sont pas dans $modelNames
			foreach ((array)Hash::get($query, 'joins') as $jointure) {
				if (!in_array(Hash::get($jointure, 'alias'), (array)$modelNames)) {
					$noPriorityJoins[] = $jointure;
				}
			}
			
			// On place les jointures dans l'ordre de modelNames
			foreach ((array)$modelNames as $modelName) {
				$joinKey = self::findJoinKey($modelName, $query);
				if ($joinKey !== false) {
					$newQuery['joins'][] = $query['joins'][$joinKey];
				} else {
					trigger_error(sprintf('La jointure sur le mod&egrave;le "%s" n\'a pas &eacute;t&eacute; trouv&eacute;.', $modelName));
				}
			}
			
			// On remet les autres jointures à la fin
			$newQuery['joins'] = array_merge($newQuery['joins'], $noPriorityJoins);
			
			return $newQuery;
		}
		
		/**
		 * Permet de retirer des jointures en fonction du nom de l'alias des modeles
		 * 
		 * @param mixed $modelNames liste des modeles à retirer du query
		 * @param array $query
		 * @return array
		 */
		public static function unsetJoin($modelNames, array $query) {
			foreach ((array)$modelNames as $modelName) {
				unset($query['joins'][self::findJoinKey($modelName, $query)]);
			}
			
			return $query;
		}
		
		/**
		 * Ajoute un lot de jointures si ils n'existent pas déjà dans $query
		 * 
		 * @param Model $Model - Model sur lequel effectuer la jointure
		 * @param mixed $modelNames - Liste des modèles à joindre
		 * @param array $query
		 * @return array
		 */
		public static function addJoins(Model $Model, $modelNames, array $query) {
			foreach ((array)$modelNames as $key => $modelName) {
				if (!is_array($modelName) && isset($Model->{$modelName})) {
					if (self::findJoinKey($modelName, $query) === false) {
						$query['joins'][] = $Model->join($modelName);
					}
				} elseif (isset($Model->{$key})) {
					if (self::findJoinKey($key, $query) === false) {
						$query['joins'][] = $Model->join($key);
					}
					$query = self::addJoins($Model->{$key}, $modelName, $query);
				} else {
					trigger_error(sprintf("Le mod&egrave;le %s n'a pas &eacute;t&eacute; trouv&eacute; comme jointure possible de %s", $key, $Model->alias));
				}
			}
			return $query;
		}
		
		/**
		 * Permet d'ajouter une condition pour avoir le dernier enregistrement d'un modèle
		 * en fonction des conditions de jointure ou des conditions tout court
		 * 
		 * @param mixed $model
		 * @param array $query
		 * @param mixed $order - Par défaut, primaryKey en DESC
		 * @return array $query
		 */
		public static function addConditionDernier($model, array $query, $order = array()) {
			if ($model instanceof Model === false) {
				$model = ClassRegistry::init($model);
			}
			if (empty($order)) {
				$order = array($model->alias.'.'.$model->primaryKey => 'DESC');
			}
			
			$conditions = array();
			if (($key = self::findJoinKey($model->alias, $query)) !== false) {
				$conditions = (array)$query['joins'][$key]['conditions'];
			} else {
				$conditions = (array)$query['conditions'];
			}
			
			$sq = $model->sq(
				array(
					'alias' => 'a',
					'fields' => 'a.'.$model->primaryKey,
					'contain' => false,
					'conditions' => array_words_replace($conditions, array($model->alias => 'a')),
					'order' => array_words_replace($order, array($model->alias => 'a')),
					'limit' => 1
				)
			);
			
			$query['conditions'][] = array(
				'OR' => array(
					$model->alias.'.'.$model->primaryKey.' IS NULL',
					$model->alias.'.'.$model->primaryKey.' IN ('.$sq.')'
				)
			);
			
			return $query;
		}
	}
