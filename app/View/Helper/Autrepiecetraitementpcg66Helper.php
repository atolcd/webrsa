<?php
	/**
	 * Fichier source de la classe Autrepiecetraitementpcg66Helper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Autrepiecetraitementpcg66Helper ...
	 *
	 * @package app.View.Helper
	 */
	class Autrepiecetraitementpcg66Helper extends AppHelper
	{
		public $helpers = array( 'Xhtml', 'Html', 'Xform' );

		/**
		 *
		 */
		public function fieldsetPieces( $modelName, $id, $options, $autresId, $textfieldPath ) {
// 			Configure::write( 'debug', 2 );
			$options = $options[$id];
			$autre_id = array_search( 1, $autresId[$id] );

			$i = 0;
			$return = '';
			foreach( $options as $value => $label ) {
				$checkboxId = $this->domId( "{$modelName}.{$i}.id{$id}.{$modelName}" );

				// reprise de données par envoi de formulaire incomplet
				$checked = Set::extract( $this->request->data, "/{$modelName}/{$modelName}" );
				// reprise de données depuis le contrôleur
				if( empty( $checked ) ) {
					$checked = Set::extract( $this->request->data, "/{$modelName}/id" );
				}
				/*Configure::write( 'debug', 2 );
				debug( $checked );
				Configure::write( 'debug', 1 );*/
				$return .= $this->Xform->input( "{$modelName}.{$i}.{$modelName}", array( 'label' => $label, 'value' => $value, 'type' => 'checkbox', 'checked' => in_array( $value, $checked ), 'id' => $checkboxId ) );

				if( $value == $autre_id ) {
					$textfieldId = $this->domId( "{$textfieldPath}.id{$id}" );
					$value = Set::extract( $this->request->data, $textfieldPath );
					$return .= $this->Xform->input( $textfieldPath, array( 'type' => 'textarea', 'label' => false/*, 'div' => false*/, 'id' => $textfieldId ) );
					$return .= "<script type=\"text/javascript\">observeDisableFieldsOnCheckbox( '{$checkboxId}', [ '{$textfieldId}' ], false, true );</script>";
				}
				$i++;
			}
			/*Configure::write( 'debug', 2 );
			debug( h( $return ) );
			Configure::write( 'debug', 0 );*/

			return $return;
		}
	}
?>