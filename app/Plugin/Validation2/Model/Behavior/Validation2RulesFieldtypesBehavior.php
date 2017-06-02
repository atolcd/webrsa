<?php
	/**
	 * Code source de la classe Validation2RulesFieldtypesBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe Validation2RulesFieldtypesBehavior ...
	 *
	 * @package app.Model.Behavior
	 */
	class Validation2RulesFieldtypesBehavior extends ModelBehavior
	{
		/**
		 * Permet de s'assurer qu'une valeur soit un nombre entier.
		 *
		 * @param Model $Model
		 * @param mixed $check
		 * @return boolean
		 */
		public function integer( Model $Model, $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$result = true;
			foreach( Hash::normalize( $check ) as $value ) {
				$result = preg_match( '/^[0-9]+$/', $value ) && $result;
			}

			return $result;
		}

		/**
		 * Permet de s'assurer qu'une valeur soit un booléen.
		 *
		 * @param Model $Model
		 * @param mixed $check
		 * @return boolean
		 */
		public function boolean( Model $Model, $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$result = true;
			foreach( Hash::normalize( $check ) as $value ) {
				$result = (
						is_bool( $value )
						|| preg_match( '/^(0|1|true|false)$/i', $value )
				) && $result;
			}

			return $result;
		}
	}
?>