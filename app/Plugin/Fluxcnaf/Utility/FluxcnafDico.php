<?php
	/**
	 * Code source de la classe FluxcnafDico.
	 *
	 * PHP 5.3
	 *
	 * @package Fluxcnaf.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe FluxcnafDico permet de manipuler des fichiers de dictionnaire
	 * de données <schema>Dico.xml venant de la CNAF.
	 *
	 * @package Fluxcnaf.Utility
	 */
	abstract class FluxcnafDico
	{
		/**
		 * Retourne un array contenant pour chaque balise (champ) possédant une
		 * liste finie de valeurs la liste des valeurs et de leurs intitulés.
		 *
		 * @param string $file Le chemin vers le fichier <schema>Dico.xml
		 * @return array
		 */
		public static function enums( $file ) {
			$xml = Xml::build( $file );

			$enums = array();
			$donnees = $xml->xpath( '/DII-Dico/Donnee/Caract[NatureCaract="CD"]/..' );
			foreach( $donnees as $donnee ) {
				$name = (string)$donnee->attributes()->NomDonnee;
				$enums[$name] = array();

				$value = $msgstr = null;
				foreach( $donnee->xpath( 'Caract/CodificationCaract/*' ) as $code ) {
					if( $code->getName() === 'ValeurCodeCaract' ) {
						$value = (string)$code;
					}
					else if( $code->getName() === 'LibelleCodeCaract' ) {
						$msgstr = (string)$code;
					}

					if( $value !== null && $msgstr !== null ) {
						$enums[$name][$value] = $msgstr;
					}
				}

				if( empty( $enums[$name] ) ) { // FIXME ?
					unset( $enums[$name] );
				}
				else {
					ksort( $enums[$name] );
				}
			}

			return $enums;
		}

		/**
		 * Retourne la liste des champs et pour chacun d'entre eux, la liste des
		 * flux dans lesquels il est présent.
		 *
		 * @param array $list
		 * @return array
		 */
		protected static function _common( array $list ) {
			$result = array();

			foreach( $list as $name => $fields ) {
				foreach( $fields as $field ) {
					if( !isset( $result[$field] ) ) {
						$result[$field] = array();
					}
					$result[$field][] = $name;
				}
			}

			return $result;
		}

		/**
		 * Retourne la liste des balises (champs) contenue dans l'ensemble des
		 * fichiers passés en paramètre
		 *
		 * @param array $names Une liste nom du flux => chemin du fichier dico
		 * @return array
		 */
		public static function comparison( array $names ) {
			$flux = $fields = Hash::normalize( array_keys( $names ) );
			foreach( array_keys( $names ) as $name ) {
				$flux[$name] = self::enums( $names[$name] );
				$fields[$name] = array_keys( $flux[$name] );
			}

			$common = self::_common( $fields );
			$result = array();

			foreach( $common as $field => $available ) {
				$foo = array();

				$values = array();
				foreach( $available as $a ) {
					$values[$a] = array_keys( (array) $flux[$a][$field] );
				}

				if( count($available) > 1 ) {
					$intersect = call_user_func_array( 'array_intersect', $values );
				}
				else {
					$intersect = array();
				}

				foreach( array_keys( $names ) as $name ) {
					if( in_array( $name, $available ) ) {
						$foo[$name] = array_diff( array_keys( $flux[$name][$field] ), $intersect );
					}
					else {
						$foo[$name] = false;
					}
				}
				$foo['common'] = $intersect;
				$result[$field] = $foo;
			}

			ksort( $result );
			return $result;
		}
	}
?>