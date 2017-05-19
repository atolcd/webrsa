<?php
	/**
	 * Fichier source de la classe ThemeHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe ThemeHelper ...
	 *
	 * @package app.View.Helper
	 */
	class ThemeHelper extends AppHelper
	{
		public $helpers = array( 'Xhtml', 'Html', 'Locale' );

		public $columnTypes = array();

		/**
		* FIXME docs
		*/

		public function button( $type, $url, $htmlAttributes = array(), $confirmMessage = false, $escapeTitle = true ) {
			$enabled = ( isset( $htmlAttributes['enabled'] ) ? $htmlAttributes['enabled'] : true );
			$iconFileSuffix = ( ( $enabled ) ? '' : '_disabled' ); // TODO: les autres aussi

			switch( $type ) {
				case 'add':
					$icon = 'icons/add'.$iconFileSuffix.'.png';
					$text = 'Ajouter';
					break;
				case 'edit':
					$icon = 'icons/pencil'.$iconFileSuffix.'.png';
					$text = 'Modifier';
					break;
				case 'delete':
					$icon = 'icons/delete'.$iconFileSuffix.'.png';
					$text = __( 'Delete' );
					break;
				case 'view':
					$icon = 'icons/zoom'.$iconFileSuffix.'.png';
					$text = __( 'View' );
					break;
				case 'print':
					$icon = 'icons/printer'.$iconFileSuffix.'.png';
					$text = 'Imprimer';
					break;
				case 'pdf':
					$icon = 'icons/page_white_acrobat'.$iconFileSuffix.'.png';
					$text = 'Générer PDF';
					break;
				case 'selection':
					$icon = 'icons/text_list_bullets'.$iconFileSuffix.'.png';
					$text = 'Sélection';
					break;
				case 'table':
					$icon = 'icons/table'.$iconFileSuffix.'.png';
					$text = 'Générer rapport';
					break;
				case 'validate':
					$icon = 'icons/tick'.$iconFileSuffix.'.png';
					$text = 'Valider';
					break;
				case 'money':
					$icon = 'icons/money'.$iconFileSuffix.'.png';
					$text = 'Versement';
					break;
				default:
					$this->cakeError( 'error500' ); // FIXME -> proprement --> $this->cakeError( 'wrongParameter' )
			}

			$text = ( isset( $htmlAttributes['text'] ) ? $htmlAttributes['text'] : $text );
			unset( $htmlAttributes['text'] );

			$content = $this->Html->image( $icon, array( 'alt' => '' ) ).' '.$text;

			$class = implode(
				' ',
				array(
					'widget button',
					$type,
					( $enabled ? 'enabled' : 'disabled' ),
					( isset( $htmlAttributes['class'] ) ? $htmlAttributes['class'] : null ),
				)
			);
			$htmlAttributes['class'] = $class;

			$htmlAttributes = array_filter_keys( $htmlAttributes, array( 'enabled' ), true );

			if( $enabled ) {
				return $this->Xhtml->link(
					$content,
					$url,
					$htmlAttributes,
					$confirmMessage,
					false
				);
			}
			else {
				return $this->Xhtml->tag( 'span', $content, $htmlAttributes, false, false );
			}
		}

		/**
		*
		*/

		public function label( $column, $options = array() ) {
			if( isset( $options['domain'] ) && !empty( $options['domain'] ) ) {
				$domain = $options['domain'];
			}
			else {
				list( $currentModelName, $currentFieldName ) = explode( '.', $column );
				$domain = strtolower( $currentModelName );
			}
			return __d( $domain, $column );
		}

		/**
		*
		*/

		public function format( $data, $path, $params = array() ) {
			$tag = Set::classicExtract( $params, 'tag' );

			/*
			*   Find model name
			*/

			$modelName = Set::classicExtract( $params, 'model' );
			if( empty( $modelName ) ) {
				// FIXME: regex -> "/(?<!\w)({$this->name}\.){0,1}{$fieldName}(?!\w)/"
				$modelField = preg_replace( '/\.[0-9]+\./', '', preg_replace( '/[0-9]+\./', '', $path ) );
				list( $modelName, ) = explode( '.', $modelField );
				if( empty( $modelName ) ) {
					// TODO: throw error
					trigger_error( "...", E_USER_WARNING );
					return null;
				}
			}

			$fieldName = preg_replace( '/^(.+\.){0,1}([^.]+)$/', '\2', $path );

			$value = Set::classicExtract( $data, $path );

			/*
			*   If field is of "type enum", translate it
			*/

			$enums = Set::classicExtract( $params, 'options' );
			if( !empty( $enums ) && is_array( $enums ) ) {
				$value = trim( $value );
				if( isset( $enums[$value] ) ) {
					//$value = Set::enum( $value, $enums );
					$value = $enums[$value];
				}
			}

			/*
			*   Get type of field
			*/

			if( isset( $params['type'] ) && !empty( $params['type'] ) ) {
				$type = $params['type'];
			}
			else {
				if( empty( $this->columnTypes[$modelName] ) ) {
					$modelClass = ClassRegistry::init( Inflector::classify( $modelName ) );
					$this->columnTypes[$modelName] = $modelClass->getColumnTypes( true );
				}

				$type = Set::classicExtract( $this->columnTypes[$modelName], $fieldName );
			}

			if( empty( $type ) ) {
				// TODO: throw error
				trigger_error( "...", E_USER_WARNING );
				return null;
			}

			/*
			*   Format entry
			*/

			$classes = array();
			switch( $type ) {
				// TODO: l10n + spécialisation des types
				case 'boolean':
					if( !is_null( $value ) && !( is_string( $value ) && strlen( trim( $value ) ) == 0 ) ) {
						$classes = "number $type ".( $value ? 'true' : 'false' );
						$value = ( $value ? __( 'Yes', true ) : __( 'No' ) );
					}
					else {
						$classes = "number $type ";
						$value = '';
					}
					break;
				case 'float':
					$classes = "number $type ".( ( $value >= 0 ) ? 'positive' : 'negative' );
					$value = $this->Locale->number( $value, 2 );
					break;
				case 'integer':
					$classes = "number $type ".( ( $value >= 0 ) ? 'positive' : 'negative' );
					$value = $this->Locale->number( $value );
					break;
				case 'money':
					$classes = "number $type ".( ( $value >= 0 ) ? 'positive' : 'negative' );
					$value = $this->Locale->money( $value, 2 );
					break;
				case 'date':
				case 'time':
				case 'timestamp':
				case 'datetime':
					$classes = $type;
					$value = $this->Locale->date( "Locale->{$type}", $value );
					break;
				case 'string':
				case 'text':
					$classes = $type;
					$value = ( !empty( $value ) ? $value : '&nbsp' );
					break;
				default:
					if( preg_match( '/^enum\(.*\)$/', $type ) ) {
						$classes = 'enum string';
						$value = ( !empty( $value ) ? $value : '&nbsp' );
					}
					else {
						// TODO: throw error
						trigger_error( "...", E_USER_WARNING );
						return null;
					}
			}

			if( !empty( $tag ) ) {
				$value = $this->Xhtml->tag(
					$tag,
					$value,
					array( 'class' => $classes )
				);
			}

			return $value;
		}

		/**
		*
		*/

		public function tableDemandeurConjoint( $data, $columns, $extra = array() ) {
			$lineNr = 0;

			$id = '';
			if( !empty( $extra['id'] ) ) {
				$id = ' id="'.$extra['id'].'"';
			}

			$return = '<table class="demcjt"'.$id.'>
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Demandeur</th>
						<th>Conjoint</th>
					</tr>
				</thead><tbody>';

			foreach( Set::normalize( $columns ) as $column => $options ) {
				$lineNr++;

				$return .= '<tr class="'.( ( $lineNr % 2 ) ?  'odd' : 'even' ).'">';
				$return .= '<th>'.$this->label( $column, $options ).'</th>';

				foreach( array( 'DEM', 'CJT' ) as $rolepers ) {
					$params = array( 'tag' => 'td' );
					if( isset( $options['options'] ) ) {
						$params['options'] = $options['options'];
					}
					// FIXME
					if( isset( $options['type'] ) ) {
						$params['type'] = $options['type'];
					}

					$return .= $this->format( Set::classicExtract( $data, $rolepers ), "{$column}", $params );
				}
				$return .= '</tr>';
			}
				$return .= '</tbody></table>';

			return $return;
		}
	}
?>