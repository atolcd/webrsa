<?php
	/**
	 * Code source de la classe GedoooUtility.
	 *
	 * PHP 5.3
	 *
	 * @package Gedooo.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	/**
	 * La classe GedoooUtility ...
	 *
	 * @package Gedooo.Utility
	 */
	abstract class GedoooUtility
	{
		/**
		 * "Live cache" de la correspondance chemin / clé.
		 *
		 * @var array
		 */
		protected static $_keys = array();

		/**
		 * "Live cache" de la correspondance chemin / msgstr.
		 *
		 * @var array
		 */
		protected static $_msgstrs = array();

		/**
		 * Retourne la clé correspondant à un chemin CakePHP. Cette clé sera
		 * utilisée lors de l'envoi des données au serveur Gedooo.
		 *
		 * @param string $path
		 * @return string
		 */
		public static function key( $path ) {
			if( false === isset( self::$_keys[$path] ) ) {
				self::$_keys[$path] = strtolower( str_replace( '.', '_', $path ) );
			}

			return self::$_keys[$path];
		}

		/**
		 * Retourne l'intitulé correspondant à un chemin CakePHP. Celui-ci sera
		 * utilisé lors de l'export des données d'impression.
		 *
		 * @param string $path
		 * @return string
		 */
		public static function msgstr( $path ) {
			if( false === isset( self::$_msgstrs[$path] ) ) {
				if( strpos( $path, '.' ) !== false ) {
					$modelField = model_field( $path );
					$domain = Inflector::underscore( $modelField[0] );
					$msgstr = __d( $domain, implode( '.', $modelField ) );
					self::$_msgstrs[$path] = $msgstr;
				}
				else {
					self::$_msgstrs[$path] = $path;
				}
			}

			return self::$_msgstrs[$path];
		}

		/**
		 * Exporte les chemins (et le cas échéant les données) d'un GDO_PartType.
		 *
		 * @param GDO_PartType $part
		 * @param array $data
		 * @param boolean $values true pour exporter les valeurs en plus des chemins
		 * @param string $type
		 * @param string $section
		 * @param integer $index
		 * @return array
		 */
		public static function export( &$part, array &$data, $values = false, $type = 'contenu', $section = null, $index = null ) {
			$result = array();

			$msgstrs = array();
			foreach( array_keys( Hash::flatten( $data ) ) as $path ) {
				$msgstrs[GedoooUtility::key( $path )] = GedoooUtility::msgstr( $path );
			}

			if( !empty( $part->field ) ) {
				foreach( $part->field as $field ) {
					$msgstr = Hash::get( $msgstrs, $field->target );
					$msgstr = $msgstr !== null ? $msgstr : $field->target;

					$key = preg_replace( '/_+/', '_', "{$type}_{$section}_{$index}_{$field->target}" );

					$result[$key] = "\"{$type}\";\"{$section}\"" . ( $values ? ";\"{$index}\"" : '' ) . ";\"{$field->target}\";\"{$field->dataType}\";\"{$msgstr}\"" . ( $values ? ";\"{$field->value}\"" : '' );
				}
			}

			return $result;
		}

		/**
		 * Retourne un array contenant des lignes à exporter au format CSV.
		 * Ces lignes contiennent un description des données envoyées à Gedooo.
		 * Suivant le paramètre de $values, on peut exporter les données de
		 * chacun des enregistrements ou seulement la forme des chemins.
		 *
		 * @param GDO_PartType $part
		 * @param array $data
		 * @param array $cohorte
		 * @param boolean $values true pour exporter les valeurs en plus des chemins
		 * @return array
		 */
		public static function toCsv( &$part, array &$data, array &$cohorte, $values = false ) {
			$lines = self::export( $part, $data, $values );

			if( !empty( $part->iteration ) ) {
				foreach( $part->iteration as $iteration ) {
					if( $values ) {
						foreach( $iteration->part as $index => $part ) {
							$lines += self::export( $part, $cohorte[$iteration->name][$index], $values, 'sections', $iteration->name, $index );
						}
					}
					else if( isset( $iteration->part[0] ) ) {
						$lines += self::export( $iteration->part[0], $cohorte[$iteration->name][0], $values, 'sections', $iteration->name, '{n}' );
					}
				}
			}

			return array( "\"Partie\";\"Nom de la section\"" . ( $values ? ";\"Iteration\"" : '' ) . ";\"Champ\";\"Type\";\"Traduction\"" . ( $values ? ";\"Valeur\"" : '' ) ) + $lines;
		}

		/**
		 * Effectue l'export CSV des lignes dans le fichier spécifié.
		 *
		 * @param string $fileName Le fichier dans lequel faire l'export
		 * @param array $lines Les lignes (au format CSV) à exporter
		 * @return boolean
		 */
		public static function exportCsv( $fileName, array &$lines ) {
			$oldMask = umask( 0 );
			$result = file_put_contents( $fileName, implode( "\n", $lines ) );
			umask( $oldMask );
			return $result !== false;
		}
	}
?>