<?php
	/**
	 * DatabaseTable behavior class.
	 *
	 * Behavior class adding methods to perform database operations on a CakePHP
	 * model class.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * Behavior class adding methods to perform database operations on a CakePHP
	 * model class.
	 *
	 * @package app.Model.Behavior
	 */
	class DatabaseTableBehavior extends ModelBehavior
	{
		/**
		 * Transforme les $querydata d'un appel "find all" en requête SQL,
		 * ce qui permet de faire des sous-requêtes moins dépendantes du SGBD.
		 *
		 * Les fields sont échappés.
		 *
		 * INFO: http://book.cakephp.org/view/74/Complex-Find-Conditions (Sub-queries)
		 *
		 * @param Model $model
		 * @param array $querydata
		 * @return string
		 * @throws RuntimeException
		 */
		public function sq( Model $model, $querydata ) {
			if( $model->useTable === false ) {
				$message = "Cannot generate a subquery for model \"{$model->alias}\" since it does not use a table.";
				throw new RuntimeException( $message, 500 );
				return array();
			}

			$dbo = $model->getDataSource( $model->useDbConfig );
			$fullTableName = $dbo->fullTableName( $model, true, false );

			$defaults = array(
				'fields' => null,
				'order' => null,
				'group' => null,
				'limit' => null,
				'table' => $fullTableName,
				'alias' => $model->alias,
				'conditions' => array(),
			);

			$querydata = Set::merge( $defaults, Hash::filter( (array)$querydata ) );
			if( empty( $querydata['fields'] ) ) {
				$querydata['fields'] = $dbo->fields( $model );
			}
			else {
				$querydata['fields'] = $dbo->fields( $model, null, $querydata['fields'] );
			}

			return $dbo->buildStatement( $querydata, $model );
		}

		/**
		 * Merges a mixed set of string/array conditions
		 *
		 * @param mixed $query
		 * @param mixed $assoc
		 * @return array
		 */
		protected function _mergeConditions( $query, $assoc ) {
			if( empty( $assoc ) ) {
				return $query;
			}

			if (is_array($query)) {
				return array_merge((array)$assoc, $query);
			}

			if (!empty($query)) {
				$query = array($query);
				if (is_array($assoc)) {
					$query = array_merge($query, $assoc);
				} else {
					$query[] = $assoc;
				}
				return $query;
			}

			return $assoc;
		}

		/**
		 *
		 * @param Model $model
		 * @param string $needleModelName
		 * @return string
		 */
		protected function _whichHabtmModel( Model $model, $needleModelName ) {
			foreach( $model->hasAndBelongsToMany as $habtmModel => $habtmAssoc ) {
				if( $habtmAssoc['with'] == $needleModelName ) {
					return $habtmModel;
				}
			}
			return null;
		}

		/**
		 *
		 * @param Model $model
		 * @param string $assoc
		 * @param array $params
		 * @return array
		 * @throws RuntimeException
		 */
		public function join( Model $model, $assoc, $params = array(/* 'type' => 'INNER' */) ) {
			// Is the assoc model really associated ?
			if( !isset( $model->{$assoc} ) ) {
				$message = "Unknown association \"{$assoc}\" for model \"{$model->alias}\"";
				throw new RuntimeException( $message, 500 );
				return array();
			}

			if( $model->useTable === false ) {
				$message = "Cannot generate a join from model \"{$model->alias}\" since it does not use a table.";
				throw new RuntimeException( $message, 500 );
				return array();
			}

			if( $model->{$assoc}->useTable === false ) {
				$message = "Cannot generate a join to model \"{$model->{$assoc}->alias}\" since it does not use a table.";
				throw new RuntimeException( $message, 500 );
				return array();
			}

			// Is the assoc model using the same DbConfig as the model's ?
			if( $model->useDbConfig != $model->{$assoc}->useDbConfig ) {
				$message = "Database configuration differs: \"{$model->alias}\" ({$model->useDbConfig}) and \"{$assoc}\" ({$model->{$assoc}->useDbConfig})";
				throw new RuntimeException( $message, 500 );
				return array();
			}

			$dbo = $model->getDataSource( $model->useDbConfig );

			// hasOne, belongsTo: OK
			$assocData = $model->getAssociated( $assoc );
			$assocData = Set::merge( $assocData, $params );

			// hasMany
			if( isset( $assocData['association'] ) && $assocData['association'] == 'hasMany' ) {
				$assocData['association'] = 'hasOne';
			}
			// hasAndBelongsToMany
			else if( !isset( $assocData['association'] ) ) {
				$whichHabtmModel = $this->_whichHabtmModel( $model, $assoc );

				if( !empty( $whichHabtmModel ) ) {
					$habtmAssoc = $model->hasAndBelongsToMany[$whichHabtmModel];
					$newAssocData = array(
						'className' => $habtmAssoc['with'],
						'foreignKey' => $habtmAssoc['foreignKey'],
						'conditions' => $habtmAssoc['conditions'],
						'association' => 'hasOne'
					);

					$assocData = Set::merge( $newAssocData, $assocData );
				}
			}

			if( empty( $assocData ) ) {
				$message = "Cannot generate a join from model \"{$model->alias}\" to model \"{$assoc}\".";
				throw new RuntimeException( $message, 500 );
				return array();
			}

			return array(
				'table' => $dbo->fullTableName( $model->{$assoc}, true, false ),
				'alias' => $assoc,
				'type' => isset($assocData['type']) ? $assocData['type'] : 'LEFT',
				'conditions' => trim(
					$dbo->conditions(
							$this->_mergeConditions(
							@$assocData['conditions'],
								$dbo->getConstraint(
									@$assocData['association'],
									$model,
									$model->{$assoc},
									$assoc,
									$assocData
								)
							),
						true,
						false,
						$model
					)
				)
			);
		}

		/**
		 * Retourne la liste des champs du modèle.
		 *
		 * @param Model $model
		 * @param boolean $virtualFields
		 * @return array
		 * @throws RuntimeException
		 */
		public function fields( Model $model, $virtualFields = false ) {
			if( $model->useTable === false ) {
				$message = "Cannot get fields for model \"{$model->alias}\" since it does not use a table.";
				throw new RuntimeException( $message, 500 );
				return array();
			}

			$fields = array();
			foreach( array_keys( $model->schema( $virtualFields ) ) as $field ) {
				$fields[] = "{$model->alias}.{$field}";
			}

			return $fields;
		}

		/**
		 * Retourne une sous-requête permettant de trouver le dernier enregistrement du modèle passé en
		 * paramètres. L'alias du modèle dans la sous-requête est le nom de la table.
		 *
		 * @param Model $model
		 * @param string $modelSubquery Le modèle sur lequel faire la sous-requête
		 * @param string $sortField Le champ sur lequel faire le tri
		 * @param boolean Autorise-t-on les valeurs NULL (pour les left join)
		 * @return string
		 */
		public function sqLatest( Model $model, $modelSubquery, $sortField, $conditions = array(), $null = true ) {
			$modelAlias = Inflector::tableize( $modelSubquery );

			$join = $this->join( $model, $modelSubquery );

			$conditions = (array)$join['conditions'] + (array)$conditions;
			$conditions = array_words_replace( $conditions, array( $modelSubquery => $modelAlias ) );

			$sq = $model->{$modelSubquery}->sq(
				array(
					'alias' => $modelAlias,
					'fields' => array(
						"{$modelAlias}.{$model->{$modelSubquery}->primaryKey}"
					),
					'contain' => false,
					'conditions' => $conditions,
					'order' => array(
						"{$modelAlias}.{$sortField} DESC",
					),
					'limit' => 1
				)
			);

			if( $null ) {
				$ds = $model->getDataSource();
				$alias = "{$ds->startQuote}{$modelSubquery}{$ds->endQuote}.{$ds->startQuote}{$model->{$modelSubquery}->primaryKey}{$ds->endQuote}";
				$sq = "( {$alias} IS NULL OR {$alias} IN ( {$sq} ) )";
			}

			return $sq;
		}

		protected function _normalize( array $array ) {
			$result = array();

			foreach( $array as $key => $value ) {
				if( is_int( $key ) && is_string( $value ) ) {
					$result[$value] = null;
				}
				else {
					$result[$key] = $value;
				}
			}

			return $result;
		}

		/**
		 * Permet de décrire les jointures à appliquer sur un modèle en spécifiant
		 * uniquement les noms des modèles (et éventuellement le type, condition,
		 * alias, table) ainsi que des sous-jointures dans la clé joins, un peu
		 * à la manière des contain.
		 *
		 * TODO:
		 *	- params -> replacements (alias, de manière générale + dans chaque jointure)
		 *	- conditions ?
		 *
		 * @fixme: si la jointure est déjà faite
		 *
		 * @param Model $model
		 * @param array $joins
		 * @return array
		 */
		public function joins( Model $model, array $joins = array() ) {
			$results = array();
			$joins = $this->_normalize( $joins );

			foreach( $joins as $joinModel => $joinParams ) {
				if( false === is_int( $joinModel) ) {
					$joinParams = (array)$joinParams;

					$innerJoins = (array)Hash::get( $joinParams, 'joins' );
					unset( $joinParams['joins'] );

					$results[] = $model->join( $joinModel, $joinParams );

					if( !empty( $innerJoins ) ) {
						$results = array_merge(
							$results,
							$model->{$joinModel}->joins( $innerJoins )
						);
					}
				}
				else {
					$results = array_merge(
						$results,
						array( $joinParams )
					);
				}
			}

			return $results;
		}
	}
?>