<?php
/**
 * Code source de la classe CakephpConfigurationParser.
 *
 * PHP 5.3
 *
 * @package app.Controller
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe CakephpConfigurationParser ...
 *
 * @package app.Controller
 */
class CakephpConfigurationParser
{	
	/**
	 * Effectue la lecture d'un fichier pour extraire les Configure::write
	 * et leurs blocs de commentaire
	 * 
	 * @param string $path
	 * @return array
	 * @throws CakeException
	 */
	public static function parseFile($path) {
		if (!file_exists($path)) {
			throw new CakeException("Le fichier demandé n'existe pas");
		}
		
		$tokens = token_get_all(implode("\n", file($path)));
		
		$lastDoc = '';
		$output = array();
		$key = null;
		$configure = false;
		$doubleColon = false;
		$write = false;
		
		foreach ($tokens as $k => $token) {
			if ($token[0] === T_DOC_COMMENT) {
				$lastDoc = static::_cleanDoc($token[1]);
			} elseif ($token[0] === T_STRING) {
				if ($token[1] === 'Configure') {
					$configure = true;
				} elseif ($token[1] === 'write') {
					$write = true;
				}
			} elseif ($token[0] === T_DOUBLE_COLON) {
				$doubleColon = true;
			} elseif ($token[0] === T_WHITESPACE) {
				continue;
			} elseif ($token[0] === T_CONSTANT_ENCAPSED_STRING
				&& $configure
				&& $doubleColon
				&& $write
				&& $key === null
			) {
				$key = trim($token[1], '"\'');
			} elseif ($token === ';') {
				if ($configure && $doubleColon && $write) {
					$output[$key] = array(
						'comment' => $lastDoc,
						'value' => Configure::read($key)
					);
				}
				
				$configure = false;
				$doubleColon = false;
				$write = false;
				$isArray = false;
				$key = null;
			} elseif (is_array($token)) {
				$tokens[$k][3] = token_name((int)$token[0]);
			}
		}
		
		return $output;
	}
	
	/**
	 * Nettoie un docblock pour ne donner que du texte formaté pour de l'HTML
	 * 
	 * @param string $docBlock
	 * @return string
	 */
	protected static function _cleanDoc($docBlock) {
		$preformated = trim(preg_replace('/ *\* ?| *\/\**?| *\*\//', '', $docBlock));
		
		if (preg_match_all('/@([a-z]+)\s+(.*?)\s*(?=$|@[a-z]+\s)/s', $preformated, $matches)) {
			$preformated = trim(substr($preformated, 0, strpos($preformated, $matches[0][0])))
				. "\n<table><tbody>";
			
			$count = count($matches[0]);
			for ($i = 0; $i < $count; $i++) {
				$preformated .= "\n"
					. '<tr><th>@' . $matches[1][$i] . '</th><td>' . preg_replace('/\s+/', ' ', trim($matches[2][$i])) . '</td>';
			}
			
			$preformated .= "\n</tbody></table>";
		}
		
		return nl2br(trim($preformated));
	}
}