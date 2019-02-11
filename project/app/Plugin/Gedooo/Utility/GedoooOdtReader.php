<?php
	/**
	 * Code source de la classe GedoooOdtReader.
	 *
	 * PHP 5.3
	 *
	 * @package Gedooo.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe GedoooOdtReader permet d'extraire des informations d'un modèle
	 * de document odt.
	 *
	 * @package Gedooo.Utility
	 */
	abstract class GedoooOdtReader
	{
		/**
		 * Récupération du contenu du fichier content.xml d'un fichier odt.
		 *
		 * @param string $path Le chemin vers le fichier odt
		 * @return string Le contenu du fichier content.xml
		 */
		protected static function _getContent( $path ) {
			$odt = new ZipArchive();
			$content = false;
			if (true === $odt->open( $path ) ) {
				$content = $odt->getFromName( 'content.xml' );
				$odt->close();
			}
			return $content;
		}

		/**
		 * Retourne la liste des variables utilisateurs déclarées dans un document
		 * odt.
		 *
		 * @param string $path Le chemin vers le modèle de document
		 * @return array
		 */
		public static function getUserDeclaredFields( $path ) {
			$cacheKey = __CLASS__.'_'.__FUNCTION__.'_'.str_replace( DS, '_', preg_replace( '/'.preg_quote( ROOT . DS, '/' ).'/', '', $path ) );
			$fields = Cache::read( $cacheKey );

			if( false === $fields ) {
				$fields = array();
				$content = static::_getContent( $path );

				if( false !== $content ) {
					$dom = new DOMDocument();
					$dom->loadXML( $content );
					if( false !== $dom ) {
						$xpath = new DOMXpath( $dom );
						$nodes = $xpath->query('//text:user-field-decls/text:user-field-decl');
						foreach ($nodes as $node) {
							$fields[] = $node->getAttribute('text:name');
						}
					}
				}

				Cache::write( $cacheKey, $fields );
			}

			return $fields;
		}
	}
?>
