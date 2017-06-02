<?php
	/**
	 * Code source de la classe PrototypeObserverComponent.
	 *
	 * PHP 5.3
	 *
	 * @package Prototype
	 * @subpackage Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PrototypeObserverComponent ...
	 *
	 * @package Prototype
	 * @subpackage Controller.Component
	 */
	class PrototypeObserverComponent extends Component
	{
		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array();

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array();

		/**
		 * Permet de désactiver et éventuellement de masquer un ensemble de champs
		 * suivant la valeur d'un champ maître.
		 *
		 * @see PrototypeObserverHelper::disableFieldsOnValue()
		 *
		 * @param array $data Les données
		 * @param string $master Le chemin CakePHP du champ maître
		 * @param string|array $slaves Les chemins CakePHP des champs à désactiver
		 * @param mixed $values Les valeurs à prendre en compte pour le champ maître
		 * @param boolean $condition true pour désactiver lorsque le champ maître a une des valeurs, false sinon
		 * @param boolean $hide true pour en plus cacher les champs lorsqu'ils sont désactivés
		 * @return array
		 */
		public function disableFieldsOnValue( array $data, $master, $slaves, $values, $condition, $hide = false ) {
			$value = Hash::get( $data, $master );

			if( in_array( $value, (array)$values ) === $condition ) {
				foreach( (array)$slaves as $slave ) {
					if( $hide ) {
						$data = Hash::remove( $data, $slave );
					}
					else {
						$data = Hash::insert( $data, $slave, null );
					}
				}
			}

			return $data;
		}

		/**
		 * Permet de désactiver et éventuellement de masquer un ensemble de champs
		 * suivant qu'une case à cocher est cochée ou non.
		 *
		 * @see PrototypeObserverHelper::disableFieldsOnCheckbox()
		 *
		 * @param array $data Les données
		 * @param string $master Le chemin CakePHP de la case à cocher
		 * @param string|array $slaves Les id des champs
		 * @param boolean $condition true pour désactiver lorsque la case est cochée, false sinon
		 * @param boolean $hide true pour en plus cacher les champs lorsqu'ils sont désactivés
		 * @return array
		 */
		public function disableFieldsOnCheckbox( array $data, $master, $slaves, $condition = false, $hide = false ) {
			$value = Hash::get( $data, $master );

			if( !empty( $value ) === $condition ) {
				foreach( (array)$slaves as $slave ) {
					if( $hide ) {
						$data = Hash::remove( $data, $slave );
					}
					else {
						$data = Hash::insert( $data, $slave, null );
					}
				}
			}

			return $data;
		}
	}
?>