<?php
	/**
	 * Fichier source de la classe WidgetHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe WidgetHelper ...
	 *
	 * @package app.View.Helper
	 */
	class WidgetHelper extends AppHelper
	{
		public $helpers = array( 'Xhtml', 'Html', 'Session', 'Form' );

		// --------------------------------------------------------------------

		public function booleanRadio( $fieldName, $attributes = array() ) {
			$error = Set::classicExtract( $this->Form->validationErrors, $fieldName );
			$class = 'radio'.( !empty( $error ) ? ' error' : '' );

			$value = Set::classicExtract( $this->request->data, $fieldName );
			if( !is_null( $value ) && ( ( is_string( $value ) && !in_array( $value, array( 'O', 'N' ) ) && ( strlen( trim( $value ) ) > 0 ) ) || is_bool( $value ) ) ) {
				$this->Form->data = Hash::insert( $this->Form->data, $fieldName, ( $value ? 'O' : 'N' ) );
			}

			$ret = '<div class="'.$class.'"><fieldset class="boolean">';
			$ret .= '<legend>'.$attributes['legend'].'</legend>';
			$attributes['legend'] = false;
			$ret .= '<div>'.$this->Form->radio( $fieldName, array( 'O' => 'Oui', 'N' => 'Non' ), $attributes ).'</div>';
			$ret .= ( !empty( $error ) ? '<div class="error-message">'.$error.'</div>' : '' );
			$ret .= '</fieldset></div>';
			return $ret;
		}
	}
?>