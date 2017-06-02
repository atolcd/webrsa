<?php
	/**
	 * Code source de la classe ConfigurableQueryFields.
	 *
	 * PHP 5.3
	 *
	 * @package ConfigurableQuery
	 * @subpackage Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ConfigurableQueryFields fournit des méthodes permettant de
	 * manipuler facilement la configuration des champs d'un querydata à partir
	 * de valeurs écrites via Configure::write() et de querydatas existants.
	 *
	 * @package ConfigurableQuery
	 * @subpackage Utility
	 */
	abstract class ConfigurableQueryFields
	{
		/**
		 * Retourne la liste des champs demandés dans le paramétrage et non
		 * présents dans la requête, à utiliser dans la vérification de
		 * l'application.
		 *
		 * @param array $keys
		 * @param array $query
		 * @return array
		 */
		public static function getErrors( array $keys, array $query ) {
			$given = array_keys( $query['fields'] );
			$return = array();
			$ignore = (array)Configure::read( 'ConfigurableQueryFields.ignore' );

			foreach( $keys as $key ) {
				$config = Configure::read( $key );
				$fields = array_keys( Hash::normalize( (array)$config ) );
				$missing = array_diff( $fields, $given );

				// Si le champ est en fait une URL ou un champ à ignorer, alors pas d'erreur
				foreach( $missing as $i => $tmp ) {
					if( strpos( $tmp, '/' ) === 0 || in_array( $tmp, $ignore ) ) {
						unset( $missing[$i] );
					}
				}
				// FIXME: URL not missing ("/Dossiers/index") + custom (Dossier.locked)

				$msgid = 'Les champs suivants sont demandés dans la configuration de %s mais pas disponibles: %s';
				$message = ( empty( $missing ) ? null : sprintf( $msgid, $key, implode( ', ', $missing ) ) );

				$return[$key] = array(
					'success' => empty( $missing ),
					'message' => $message,
					'value' => var_export( $config, true ),
				);
			}

			return $return;
		}

		/**
		 * Limite les champs retournés par le query par-rapport aux clés définies
		 * dans une ou plusieurs clés de configuration.
		 *
		 * @param string|array $keys
		 * @param array $query
		 * @return array
		 */
		public static function getFieldsByKeys( $keys, array $query ) {
			$keys = (array)$keys;

			$fields = array();
			foreach( $keys as $key ) {
				$fields = Hash::merge(
					$fields,
					Hash::normalize( (array)Configure::read( $key ) )
				);
			}
			$fields = array_keys( $fields );

			foreach( $query['fields'] as $key => $value ) {
				if( !( is_int( $key ) || in_array( $key, $fields ) ) ) {
					unset( $query['fields'][$key] );
				}
			}

			return $query;
		}

		public static function exportQueryFields( array $query, $domain, $fileName ) {
			$rows = array( implode( ',', array( '"Champ"', '"Intitulé"' ) ) );
			$fields = array_keys( $query['fields'] );
			foreach( $fields as $field ) {
				if( !is_numeric( $field ) ) {
					$rows[] = implode( ',', array( $field, __d( $domain, $field ) ) );
				}
			}

			$mask = umask( 0 );
			file_put_contents( $fileName, implode( "\n", $rows ) );
			umask( $mask );
		}

		/**
		 * Retourne la liste des champs (normaux et virtuels) des modèles envoyés
		 * en paramètres, formattés pour être utilisés par getFieldsByKeys().
		 *
		 * @param array $modelNames Les modèles desquels obtenir les champs
		 */
		public static function getModelsFields( array $Models ) {
			$result = array();

			foreach( $Models as $Model ) {
				foreach( array_keys( $Model->schema() ) as $fieldName ) {
					$result["{$Model->alias}.{$fieldName}"] = "{$Model->alias}.{$fieldName}";
				}

				foreach( array_keys( (array)$Model->virtualFields ) as $fieldName ) {
					$result["{$Model->alias}.{$fieldName}"] = "{$Model->alias}.{$fieldName}";
				}
			}

			return $result;
		}
	}
?>