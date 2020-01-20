<?php
	/**
	 * Code source de la classe PersonneCSVBehavior..
	 *
	 * PHP 7.2
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe PersonneCSVBehavior. contient du code commun des classes
	 * de modèles Personne, Infocontactpersonne , Personnefrsadiplomexper et Personnelangue.
	 *
	 * @package app.Model.Behavior
	 */
	class PersonneCSVBehavior extends ModelBehavior
	{

		/**
		 * Recherche et Update de l'enregistrement sinon Insertion, avec complément de
		 * données si besoin et retourne la valeur de la clé primaire ou null.
		 *
		 * @param Model $Model
		 * @param array $conditions
		 * @param array $complement
		 * @return integer
		 */
		public function csvInsertUpdate( Model $Model, array $conditions, array $complement = array() ) {
			$primaryKey = null;
			$conditions = Hash::flatten( $Model->doFormatting( Hash::expand( $conditions ) ) );

			$modelPrimaryKey = "{$Model->alias}.{$Model->primaryKey}";

			$query = array(
				'fields' => array( $modelPrimaryKey ),
				'recursive' => -1,
				'conditions' => $conditions
			);

			// Début copie ci-dessus.
			// On cherche un intitulé approchant à la casse et aux accents près
			foreach( $query['conditions'] as $path => $value ) {
				if( $value !== null && $value !== '' ) {
					if(
						!($value == 'true') && !($value == 'false') &&
						!is_numeric($value)
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
			$record = $Model->find( 'first', $query );
			if( !empty( $record ) ) {
				$data = Hash::merge(
					$record,
					Hash::expand( $conditions ),
					Hash::expand( $complement )
				);
			}else{
				$data = Hash::merge(
					Hash::expand( $conditions ),
					Hash::expand( $complement )
				);
			}

			$primaryKey = null;
			$Model->create($data);
			if ( $Model->save( null, array( 'atomic' => false ) ) ) {
				$primaryKey = $Model->{$Model->primaryKey};
			}

			return $primaryKey;
		}

		/**
		 * Recherche et Update de l'enregistrement sinon Insertion, avec complément de
		 * données si besoin et retourne la valeur de la clé primaire ou null.
		 *
		 * @param Model $Model
		 * @param array $conditions
		 * @param array $complement
		 * @return integer
		 */
		public function getInsertedUpdatedPrimaryKey( Model $Model, array $conditions, array $complement = array() ) {
			return $this->csvInsertUpdate ($Model,$conditions, $complement);
		}
	}
?>