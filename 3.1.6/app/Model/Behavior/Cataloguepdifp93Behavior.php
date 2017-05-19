<?php
	/**
	 * Code source de la classe Cataloguepdifp93Behavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Cataloguepdifp93Behavior ...
	 *
	 * @package app.Model.Behavior
	 */
	class Cataloguepdifp93Behavior extends ModelBehavior
	{
		public function searchQuery( Model $Model, array $query = array() ) {
			// TODO: cache
//			$cacheKey = sprintf( '%s_%s_%s_%s_%s', $Model->useDbConfig, __CLASS__, $Model->alias, __FUNCTION__, md5( serialize( $query ) ) );
//			debug( $cacheKey );

			$query += array(
				'fields' => array(),
				'joins' => array(),
				'conditions' => array(),
				'order' => array()
			);

			if( !empty( $Model->belongsTo ) ) {
				foreach( array_keys( $Model->belongsTo ) as $alias ) {
					$Parent = $Model->{$alias};

					$query = $this->searchQuery( $Parent, $query );

					array_unshift( $query['joins'], $Model->join( $alias, array( 'type' => 'INNER' ) ) );
				}
			}

			$query['fields'][] = "{$Model->alias}.{$Model->primaryKey}";
			$query['fields'][] = "{$Model->alias}.{$Model->displayField}";

			/*$query['fields'] = array_merge(
				$query['fields'],
				array(
					"{$Model->alias}.{$Model->primaryKey}",
					"{$Model->alias}.{$Model->displayField}"
				)
			);*/

			if( !in_array( $Model->alias, array( 'Prestatairefp93', 'Adresseprestatairefp93' ) ) ) {
				if( !in_array( 'Thematiquefp93.type', $query['fields'] ) ) {
					array_unshift( $query['fields'], 'Thematiquefp93.type' );
				}

				if( !in_array( 'Thematiquefp93.type DESC', $query['order'] ) ) {
					array_unshift( $query['order'], 'Thematiquefp93.type DESC' );
				}
			}

			// TODO: pdi, thématique, catégorie, ...
			// array_unshift( $query['order'], $Model->order );
			$query['order'][] = $Model->order;

			return $query;
		}

		public function setup( Model $model, $config = array( ) ) {
			parent::setup( $model, $config );

			if( $model->order === null ) {
				$model->order = "{$model->alias}.name ASC";
			}

			/*$query = $this->searchQuery( $model );
			$results = $model->find( 'all', $query );

			debug( array(
				'model' => $model->alias,
				'joins' => (array)Hash::extract( $query, 'joins.{n}.alias' ),
				'list' => Hash::combine( $results, "{n}.{$model->alias}.{$model->primaryKey}", "{n}.{$model->alias}.{$model->displayField}" ),
				'query' => $query,
				'results' => $results,
			) );*/
		}

		/**
		 * Recherche l'enregistrement répondant aux conditions. Si celui-ci existe,
		 * sa clé primaire est renvoyée; sinon, on tente d'enregistrer les données.
		 *
		 * En cas de succès, la clé primaire du nouvel enregistrement est retournée.
		 *
		 * @deprecated
		 *
		 * @param Model $Model
		 * @param array $conditions
		 * @return integer
		 */
		/*public function createOrUpdate( Model $Model, array $conditions ) {
			$conditions = Hash::flatten( $Model->doFormatting( Hash::expand( $conditions ) ) );
			// Remplacement des espaces multiples par un espace simple
			foreach( $conditions as $key => $value ) {
				$conditions[$key] = preg_replace( '/ +/', ' ', $value );
			}

			$primaryKeyField = "{$Model->alias}.{$Model->primaryKey}";

			$query = array(
				'fields' => array( $primaryKeyField ),
				'conditions' => $conditions
			);

			// On cherche un intitulé approchant à la casse et aux accents près
			foreach( $query['conditions'] as $path => $value ) {
				if( $value !== null && $value !== '' ) {
					if( !is_numeric( $value ) ) {
						unset( $query['conditions'][$path] );
						list( $m, $f ) = model_field( $path );
						$query['conditions']["NOACCENTS_UPPER( \"{$m}\".\"{$f}\" )"] = noaccents_upper( $value );
					}
				}
				else {
					unset( $query['conditions'][$path] );
					$query['conditions'][] = "{$path} IS NULL";
				}
			}

			$record = $Model->find( 'first', $query );

			if( empty( $record ) ) {
				$record = Hash::expand( $conditions );
				$Model->create( $record );

				if( !$Model->save() ) {
					return null;
				}
				else {
					return $Model->{$Model->primaryKey};
				}
			}
			else {
				return Hash::get( $record, $primaryKeyField );
			}
		}*/

		/**
		 * Recherche de l'enregistrement sinon insertion, avec complément de
		 * données si besoin et retourne la vlauer de la clé primaire ou null.
		 *
		 * @param Model $Model
		 * @param array $conditions
		 * @param array $complement
		 * @return integer
		 */
		public function getInsertedPrimaryKey( Model $Model, array $conditions, array $complement = array() ) {
			$conditions = Hash::flatten( $Model->doFormatting( Hash::expand( $conditions ) ) );

			$modelPrimaryKey = "{$Model->alias}.{$Model->primaryKey}";

			$query = array(
				'fields' => array( $modelPrimaryKey ),
				'conditions' => $conditions
			);

			// Début copie ci-dessus.
			// On cherche un intitulé approchant à la casse et aux accents près
			foreach( $query['conditions'] as $path => $value ) {
				if( $value !== null && $value !== '' ) {
					if( !is_numeric( $value ) ) {
						unset( $query['conditions'][$path] );
						list( $m, $f ) = model_field( $path );
						$query['conditions']["NOACCENTS_UPPER( \"{$m}\".\"{$f}\" )"] = noaccents_upper( $value );
					}
				}
				else {
					unset( $query['conditions'][$path] );
					$query['conditions'][] = "{$path} IS NULL";
				}
			}
			// Fin copie ci-dessus.

			$record = $Model->find( 'first', $query );

			if( !empty( $record ) ) {
				$primaryKey = Hash::get( $record, $modelPrimaryKey );
			}
			else {
				$data = Hash::merge(
					Hash::expand( $conditions ),
					Hash::expand( $complement )
				);

				$Model->create( $data );
				if( $Model->save() ) {
					$primaryKey = $Model->{$Model->primaryKey};
				}
				else {
					$primaryKey = null;
				}
			}

			return $primaryKey;
		}
	}
?>