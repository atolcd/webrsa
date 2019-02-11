<?php
	/**
	 * Fichier source de la classe DsphmHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe DsphmHelper ...
	 *
	 * @package app.View.Helper
	 */
	class DsphmHelper extends AppHelper
	{
		public $helpers = array( 'Xform', 'Xhtml' );

		/** ********************************************************************
		*
		*** *******************************************************************/

		public function fields( $model, $code, $libdetails, $dsp_id, $code_autre, $options ) {
			$i = 0;
			$return = '';
			foreach( $options as $value => $label ) {
				// FIXME: checked
				$checked = Set::extract( $this->request->data, "/{$model}[{$code}={$value}]" );

				$item_id = Set::classicExtract( $checked, "0.{$model}.id" );
				if( !empty( $item_id ) ) {
					$return .= $this->Xform->input( "{$model}.{$i}.id", array( 'type' => 'hidden', 'value' => $item_id ) );
				}

				if( !empty( $dsp_id ) ) {
					$return .= $this->Xform->input( "{$model}.{$i}.dsp_id", array( 'type' => 'hidden', 'value' => $dsp_id ) );
				}

				$return .= $this->Xform->input( "{$model}.{$i}.{$code}", array( 'label' => $label, 'value' => $value, 'domain' => 'dsp', 'type' => 'checkbox', 'checked' => !empty( $checked ) ) );

				if( $value == $code_autre ) {
					$value = Set::extract( $this->request->data, "/{$model}[{$code}={$code_autre}]/{$libdetails}" );
					$return .= $this->Xform->input( "{$model}.{$i}.{$libdetails}", array( 'domain' => 'dsp', 'type' => 'textarea', 'value' => implode( "\n\n", $value ) ) );
					$return .= "<script type=\"text/javascript\">document.observe( 'dom:loaded', function() { observeDisableFieldsOnCheckbox( '{$model}{$i}".ucfirst( $code )."', [ '{$model}{$i}".ucfirst( $libdetails )."' ], false ); } );</script>";
				}
				$i++;
			}

			return $return;
		}

		/** ********************************************************************
		*
		*** *******************************************************************/

		public function fieldset( $model, $code, $libdetails, $dsp_id, $code_autre, $options ) {
			$return = $this->fields( $model, $code, $libdetails, $dsp_id, $code_autre, $options );
			return $this->Xhtml->tag( 'fieldset', $this->Xhtml->tag( 'legend', __d( 'dsp', "{$model}.{$code}" ) ).$return );
		}

		/** ********************************************************************
		*
		*** *******************************************************************/

		public function details( $dsp, $model, $code, $libdetails, $options ) {
			$answers = array();

			$items = Set::extract( $dsp, "/{$model}/{$code}" );
			$libautrdifs = Hash::filter( (array)Set::extract( $dsp, "/{$model}/{$libdetails}" ) );

			if( !empty( $items ) ) {
				$ul = array();
				$libs = array();

				foreach( $items as $key => $item ) {
					$ul[] = $this->Xhtml->tag( 'li', Set::enum( $item , $options ) );
				}

				$answers[] = array(
					__d( 'dsp', "{$model}.{$code}" ),
					$this->Xhtml->tag( 'ul', implode( '', $ul ) )
				);

				if( !empty( $libdetails ) ) {
					$answers[] = array(
						__d( 'dsp', "{$model}.{$libdetails}" ),
						h( implode( '', $libautrdifs ) )
					);
				}
			}
			else {
				$answers = array(
					array( __d( 'dsp', "{$model}.{$code}" ), null )
				);

				if( !empty( $libdetails ) ) {
					$answers[] = array( __d( 'dsp', "{$model}.{$libdetails}" ), null );
				}
			}

			return $this->Xhtml->details( $answers, array( 'type' => 'list', 'empty' => true ) );
		}
	}
?>