<?php
	/**
	 * Code source de la classe Catalogueromev3Behavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe Catalogueromev3Behavior ...
	 *
	 * @package app.Model.Behavior
	 */
	class Catalogueromev3Behavior extends ModelBehavior
	{
		/**
		 * Retourne les données à utiliser dans le formulaire de modification de
		 * la partie paramétrage.
		 *
		 * @param integer $id
		 * @return array
		 */
		public function getParametrageFormData( Model $Model, $id ) {
			$query = array(
				'fields' => array_keys( $Model->getParametrageFields() ),
				'conditions' => array(
					"{$Model->alias}.{$Model->primaryKey}" => $id
				)
			);

			return $Model->find( 'first', $query );
		}

		/**
		 * Retourne la liste des champs à utiliser dans le formulaire d'ajout / de
		 * modification de la partie paramétrage.
		 *
		 * @return array
		 */
		public function getParametrageFields( Model $Model ) {
			$fields = array_keys( $Model->schema() );

			array_remove( $fields, 'created' );
			array_remove( $fields, 'modified' );

			$fields = Hash::normalize( array_keys( Hash::flatten( array( $Model->alias => Hash::normalize( $fields ) ) ) ) );
			$enums = $Model->enums();

			foreach( $fields as $path => $params ) {
				$params = (array)$params;
				if( Hash::check( $enums, $path ) || ( strrpos( $path, '_id' ) === ( strlen( $path ) - 3 ) ) ) {
					$params['empty'] = true;
				}
				$fields[$path] = $params;
			}

			return $fields;
		}

		/**
		 * Retourne les options à utiliser dans le formulaire d'ajout / de
		 * modification de la partie paramétrage.
		 *
		 * @param boolean Permet de s'assurer que l'on possède au moins un
		 *	enregistrement au niveau inférieur.
		 * @return array
		 */
		public function getParametrageOptions( Model $Model, $hasDescendant = false ) {
			return $Model->enums();
		}

		/**
		 * Retourne la liste des champs dépendants à utiliser dans le formulaire
		 * d'ajout / de modification de la partie paramétrage.
		 *
		 * @return array
		 */
		public function getParametrageDependantFields( Model $Model ) {
			$return = array();
			$fields = $Model->getParametrageFields();

			$lastDependant = null;
			foreach( array_keys( $fields ) as $fieldName ) {
				$length = strlen( $fieldName );
				if( strrpos( $fieldName, '_id' ) === ( $length - 3 ) ) {
					if( $lastDependant !== null ) {
						$return[$lastDependant] = $fieldName;
					}
					$lastDependant = $fieldName;
				}
			}

			return $return;
		}

		/**
		 * Tentative de sauvegarde d'un élément du catalogue à partir de la
		 * partie paramétrage.
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function saveParametrage( Model $Model, array $data ) {
			$Model->create( $data );
			return ( $Model->save( null, array( 'atomic' => false ) ) !== false );
		}
	}
?>