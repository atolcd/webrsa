<?php
	/**
	 * Code source de la classe OccurencesBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe OccurencesBehavior ...
	 *
	 * @package app.Model.Behavior
	 */
	class OccurencesBehavior extends ModelBehavior
	{
		/**
		* Retourne un tableau dont la clé est l'id du champ du model choisi et la valeur
		* le nombre d'occurences trouvées dans les tables liées en hasOne, hasMany et
		* hasAndBelongsToMany avec possibilité d'envoyer des conditions supplémentaires
		*/

		public function occurences( Model $model, $conditions = array() ) {
			$counts = array();
			$joins = array();
			$dbo = $model->getDataSource( $model->useDbConfig );
			$sq = $dbo->startQuote;
			$eq = $dbo->endQuote;

			// remplissage des variables pour faire les jointure et le count sur les tables en hasOne et en hasMany
			foreach( array( 'hasOne', 'hasMany' ) as $assocType ) {
				if( !empty( $model->{$assocType} ) ) {
					foreach( $model->{$assocType} as $alias => $assoc ) {
						$joins[] = array(
							'table'      => $dbo->fullTableName( $model->{$alias}, false, false ),
							'alias'      => $alias,
							'type'       => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array( "{$alias}.{$assoc['foreignKey']} = {$model->alias}.{$model->primaryKey}" )
						);

						$counts[] = "COUNT({$sq}{$alias}{$eq}.{$sq}id{$eq})";
					}
				}
			}

			// remplissage des variables pour faire les jointure et le count sur les tables en hasAndBelongsToMany
			if( !empty( $model->hasAndBelongsToMany ) ) {
				foreach( $model->hasAndBelongsToMany as $alias => $assoc ) {
					$joins[] = array(
						'table'      => $dbo->fullTableName( $model->{$assoc['with']}, false, false ),
						'alias'      => $assoc['with'],
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( "{$assoc['with']}.{$assoc['foreignKey']} = {$model->alias}.{$model->primaryKey}" )
					);

					$counts[] = "COUNT({$sq}{$assoc['with']}{$eq}.{$sq}id{$eq})";
				}
			}

			if( !empty( $counts ) ) {
				$implodeCounts = implode( $counts, ' + ' );
			}
			else {
				$implodeCounts = '0';
			}

			// création du queryData
			$queryData = array(
				'fields' => array(
					"{$model->alias}.{$model->primaryKey}",
					"{$implodeCounts} AS {$sq}{$model->alias}__occurences{$eq}",
				),
				'joins' => $joins,
				'recursive' => -1,
				'conditions' => $conditions,
				'group' => array( "{$model->alias}.{$model->primaryKey}" ),
				'order' => array( "{$model->alias}.{$model->primaryKey}" )
			);

			$results = $model->find( 'all', $queryData );

			return Set::combine( $results, "{n}.{$model->alias}.{$model->primaryKey}", "{n}.{$model->alias}.occurences" );
		}

		/**
		* Retourne un tableau dont la clé est l'id du champ du model choisi et la valeur
		* le fait qu'il existe au moins une occurence dans une des tables liées en hasOne,
		* hasMany et hasAndBelongsToMany avec possibilité d'envoyer des conditions supplémentaires
		*/

		public function occurencesExists( Model $model, $conditions = array(), $blacklist = array(), $returnQuerydata = false ) {
			$exists = array();
			$dbo = $model->getDataSource( $model->useDbConfig );
			$sq = $dbo->startQuote;
			$eq = $dbo->endQuote;

			// remplissage des variables pour faire les jointure et le count sur les tables en hasOne et en hasMany
			foreach( array( 'hasOne', 'hasMany' ) as $assocType ) {
				if( !empty( $model->{$assocType} ) ) {
					foreach( $model->{$assocType} as $alias => $assoc ) {
						if( !in_array( $alias, $blacklist ) ) {
							$table = $dbo->fullTableName( $model->{$alias}, false, false );
							$exists[] = "EXISTS( SELECT {$table}.{$assoc['foreignKey']} FROM {$table} WHERE {$table}.{$assoc['foreignKey']} = {$sq}{$model->alias}{$eq}.{$sq}{$model->primaryKey}{$eq} )";
						}
					}
				}
			}

			// remplissage des variables pour faire les jointure et le count sur les tables en hasAndBelongsToMany
			if( !empty( $model->hasAndBelongsToMany ) ) {
				foreach( $model->hasAndBelongsToMany as $alias => $assoc ) {
					if( !in_array( $alias, $blacklist ) ) {
						$table = $dbo->fullTableName( $model->{$assoc['with']}, false, false );
						$exists[] = "EXISTS( SELECT {$table}.{$assoc['foreignKey']} FROM {$table} WHERE {$table}.{$assoc['foreignKey']} = {$sq}{$model->alias}{$eq}.{$sq}{$model->primaryKey}{$eq} )";
					}
				}
			}

			if( !empty( $exists ) ) {
				$implodeExists = implode( $exists, ' OR ' );
			}
			else {
				$implodeExists = 'false';
			}

			// création du queryData
			$queryData = array(
				'fields' => array(
					"{$model->alias}.{$model->primaryKey}",
					"{$implodeExists} AS {$sq}{$model->alias}__occurences{$eq}",
				),
				'recursive' => -1,
				'conditions' => $conditions,
				'group' => array( "{$model->alias}.{$model->primaryKey}" ),
				'order' => array( "{$model->alias}.{$model->primaryKey}" )
			);

            if( $returnQuerydata ){
                return $queryData;
            }

			$results = $model->find( 'all', $queryData );

			return Set::combine( $results, "{n}.{$model->alias}.{$model->primaryKey}", "{n}.{$model->alias}.occurences" );
		}

        /**
         * Complète le querydata passé en paramètre avec un champ virtuel de type
         * booléen, de nom occurences, et un group by.
         *
         * @param Model $model
         * @param array $querydata
         * @param array $blacklist La liste des modèles ne devant pas être pris en compte
         * @return array
         */
        public function qdOccurencesExists( Model $model, $querydata = array(), $blacklist = array() ) {
            $qdOccurences = $this->occurencesExists( $model, array(), $blacklist, true );

            $querydata['group'] = array_merge(
                ( isset( $querydata['group'] ) ? $querydata['group'] : array() ),
                ( isset( $querydata['fields'] ) ? $querydata['fields'] : array() )
            );

            foreach( array( 'fields', 'group' ) as $part ) {
                $querydata[$part] = array_merge(
                    ( isset( $querydata[$part] ) ? $querydata[$part] : array() ),
                    ( isset( $qdOccurences[$part] ) ? $qdOccurences[$part] : array() )
                );
            }

            return $querydata;
        }

		/**
		 * Retourne une sous-requête permettant de savoir si un enregistrement du
		 * modèle est référencé par une autre table (au niveau des foreign keys
		 * définies dans la base de données).
		 *
		 * @param Model $model Le modèle
		 * @param boolean|string $alias L'alias éventuel du champ (par défaut:
		 *	<alias du modèle>.has_linkedrecords )
		 * @param array $blacklist La liste des tables ne devant pas être prises
		 *	en compte
		 * @return string
		 */
		public function sqHasLinkedRecords( Model $model, $alias = true, array $blacklist = array() ) {
			if( false === $model->Behaviors->attached( 'Postgres.PostgresTable' ) ) {
				$model->Behaviors->attach( 'Postgres.PostgresTable' );
			}

			$sql = array();
			$foreignKeys = $model->getPostgresForeignKeys();
			if( false === empty( $foreignKeys ) ) {
				foreach( $foreignKeys['to'] as $foreignKey ) {
					if( false === in_array( $foreignKey['From']['table'], $blacklist ) ) {
						$sql[] = "EXISTS( SELECT * FROM \"{$foreignKey['From']['schema']}\".\"{$foreignKey['From']['table']}\" AS \"{$foreignKey['From']['table']}\" WHERE \"{$foreignKey['From']['table']}\".\"{$foreignKey['From']['column']}\" = \"{$model->alias}\".\"{$foreignKey['To']['column']}\" )";
					}
				}
				$sql = implode( ' OR ', $sql );
			}
			else {
				$sql = 'FALSE';
			}

			if( true === $alias ) {
				$alias = "{$model->alias}.has_linkedrecords";
			}

			if( is_string( $alias ) ) {
				return "( {$sql} ) AS \"".str_replace( '.', '__', $alias )."\"";
			}
			else {
				return $sql;
			}
		}
	}
?>