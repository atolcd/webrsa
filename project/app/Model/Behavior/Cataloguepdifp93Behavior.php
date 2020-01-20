<?php
	/**
	 * Code source de la classe Cataloguepdifp93Behavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe Cataloguepdifp93Behavior ...
	 *
	 * @package app.Model.Behavior
	 */
	class Cataloguepdifp93Behavior extends ModelBehavior
	{
		public function searchQuery( Model $Model, array $query = array() ) {
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

			if( !in_array( $Model->alias, array(
				'Prestatairefp93', 'Adresseprestatairefp93',
				'Modtransmfp93',
				'Motifnonretenuefp93',
				'Motifnonintegrationfp93',
				'Motifactionachevefp93',
				'Motifnonactionachevefp93',
				'Documentbeneffp93'
			 ) ) ) {
				if( !in_array( 'Thematiquefp93.type', $query['fields'] ) ) {
					array_unshift( $query['fields'], 'Thematiquefp93.type' );
				}
				if( !in_array( 'Thematiquefp93.yearthema', $query['fields'] ) ) {
					array_unshift( $query['fields'], 'Thematiquefp93.yearthema' );
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
		}

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
				if( $Model->save( null, array( 'atomic' => false ) ) ) {
					$primaryKey = $Model->{$Model->primaryKey};
				}
				else {
					$primaryKey = null;
				}
			}
			return $primaryKey;
		}

		/**
		 * Recherche de l'enregistrement sinon insertion, avec complément de
		 * données si besoin et retourne la vlauer de la clé primaire ou null.
		 *
		 * @param Model $Model
		 * @param array $conditions
		 * @param array $complement
		 * @return integer
		 */
		public function getInsertedUpdatedPrimaryKey( Model $Model, array $conditions, array $complement = array() ) {
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
					if(
						!is_numeric( $value )
						&& !(preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.'[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $value) === 1)
						&& !$this->validateDate($value)
					) {
						unset( $query['conditions'][$path] );
						list( $m, $f ) = model_field( $path );
						$noacc_upp_value = noaccents_upper( $value );
						$encoding = mb_detect_encoding($noacc_upp_value, 'UTF-8', true);
						if ( $encoding != 'UTF-8' ) {
							$query['conditions']["\"{$m}\".\"{$f}\" LIKE"] = $value;
						}else{
							$query['conditions']["NOACCENTS_UPPER( \"{$m}\".\"{$f}\" ) LIKE"] = $noacc_upp_value;
						}
					}
				}
				else {
					unset( $query['conditions'][$path] );
					$query['conditions'][] = "{$path} IS NULL";
				}
			}
			// Fin copie ci-dessus.
			$data = Hash::merge(
				Hash::expand( $conditions ),
				Hash::expand( $complement )
			);

			$record = $Model->find( 'first', $query );

			$primaryKey = null;
			if( !empty( $record ) ) {
				$primaryKey = Hash::get( $record, $modelPrimaryKey );
				$data[$Model->alias]['id'] = $primaryKey;
			}

			$Model->create( $data );
			if( $Model->save( null, array( 'atomic' => false ) ) ) {
				$primaryKey = $Model->{$Model->primaryKey};
			}

			return $primaryKey;
		}

		function validateDate($date, $format = 'Y-m-d'){
			$d = DateTime::createFromFormat($format, $date);
			return $d && $d->format($format) === $date;
		}

	}
?>