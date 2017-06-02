<?php
	/**
	 * Code source de la classe Analysesql.
	 *
	 * @package AnalyseSql
	 * @subpackage app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	
	/**
	 * La classe Analysesql permet de traiter une requête au format SQL afin de la décortiquer et de la rendre plus compréhensible pour un humain.
	 *
	 * @package AnalyseSql
	 * @subpackage app.Controller
	 */
	abstract class Analysesql
	{
		/**
		 * Nom du datasource à utiliser
		 * 
		 * @var string 
		 */
		public static $datasourceName = 'default';
		
		/**
		 * Echapement renseigné par DboSource
		 * 
		 * @var string 
		 */
		public static $startQuote;
		
		/**
		 * Echapement renseigné par DboSource
		 * 
		 * @var string 
		 */
		public static $endQuote;
		
		/**
		 * Liste des jointures extraite d'un requête SQL
		 * 
		 * @var array 
		 */
		protected static $_joins;
		
		/**
		 * Découpage d'une requête dans les blocs select from et where
		 * 
		 * @var array
		 */
		protected static $_cuttedSql;
		
		/**
		 * Capture l'intérieur des parenthèses et remplace le contenu par un indice d'array
		 * ex: ... ON (Foo.id = Bar.foo_id) ... => ... ON [1] ...
		 * 
		 * @param string $sql Code SQL
		 * @return array array('sql' => $sql, 'bracketsInnerText' => $bracketsInnerText); sql contient le code SQL dépourvu de parenthèses tendis que bracketsInnerText contient leurs contenu
		 */
		protected static function _bracketsInnerText( $sql ) {
			$i = 0;
			$offset = 0;
			$continue = true;
			$fail = false;
			while ($continue && $i < 500){
				$continue = preg_match('/\((?P<bracket>[^()]*)\)/', $sql, $brackets[$i], PREG_OFFSET_CAPTURE, $offset);
				
				// Retire le dernier match qui renvoi un array vide
				if ( empty($brackets[$i]) ) {
					$continue = false;
					unset($brackets[$i]);
				}
				
				// On donner une autre chance de trouver des parenthèses
				if ( !$continue && $fail === false ) {
					$fail = true;
					$continue = true;
					$offset = 0;
				}
				
				// Apres un fail, il en a trouver un autre, on passe donc fail à faux
				elseif ( $continue && $fail === true ) {
					$fail = false;
				}
				
				// Si tout c'est bien passé, on retire du sql le contenu capturé
				if ( $continue && $fail === false ){
					$offset = $brackets[$i]['bracket'][1];
					$length = strpos($sql, ')', $offset) - $offset;
					$sql = substr($sql, 0, $offset -1) . '['.$i.']' . substr($sql, $offset + $length +1 );
				}
				
				$i++;
			}
			
			// On formate pour une meilleur lisibilité
			$bracketsInnerText = array();
			foreach ($brackets as $i => $value) {
				if (preg_match('/^\[(\d+)\]$/', trim($value['bracket'][0]), $match)) {
					$bracketsInnerText[$i] = trim($bracketsInnerText[$match[1]]);
					unset($bracketsInnerText[$match[1]]);
				}
				else {
					$bracketsInnerText[$i] = trim($value['bracket'][0]);
				}
			}
			
			$result = array(
				'sql' => $sql,
				'bracketsInnerText' => $bracketsInnerText
			);
			return $result;
		}
		
		/**
		 * Récupère la liste des jointures et du from depuis une requête sql
		 * sous la forme : 'tablename' => array( 'alias' => 'Alias', 'type' => 'INNER JOIN' )
		 * 
		 * @param string $sql Code SQL
		 * @param array $bracketsInnerText Contenu des parenthèses : SELECT [0] FROM ... le [0] désigne $bracketsInnerText[0]
		 * @return array
		 */
		protected static function _extractJoins( $sql, $bracketsInnerText ) {
			self::_getGlobalMatches($sql);
			$joins = self::$_joins;
			
			$regType = '(?:(?P<type>[\w]+) (?:OUTER )*(?:JOIN)*) *';
			$regTableName = '(?:'.self::$startQuote.'[\w]+'.self::$endQuote.'\.)*(?:'.self::$startQuote.'(?P<tablename>[\w]+)'.self::$endQuote.')(?: AS '.self::$startQuote.'(?P<alias>[\w]+)'.self::$endQuote.')* *';
			$regOn = '(?:ON (?P<condition>.*))*';
			foreach($joins as $key => $value) {
				preg_match("/{$regType}{$regTableName}{$regOn}/", trim($value), $match);
				
				// Permet de restituer un niveau de parenthèses
				$condition = array();
				if (isset($match['condition'])) {
					preg_match('/\[(?P<bracket>[\d]+)\]/', $match['condition'], $condition);
				}
				
				if ( !empty($match) ) {
					$joins[$key] = array(
						'table' => '"' . $match['tablename'] . '"',
						'alias' => isset($match['alias']) ? $match['alias'] : $match['tablename'],
						'type' => $match['type'],
						'conditions' => isset($condition['bracket']) && isset($bracketsInnerText[$condition['bracket']]) ? $bracketsInnerText[$condition['bracket']] : (isset($match['condition']) ? $match['condition'] : '')
					);
				}
				elseif ($value === '') {
					unset($joins[$key]);
				}
			}
			
			return !empty($joins) ? $joins : array();
		}
		
		/**
		 * Récupère la liste des champs selectionné dans une requête SQL sous le format cakePHP
		 * 
		 * @param sql $sql
		 * @return array array('Model.field1', 'Model.field2')
		 */
		protected static function _extractFields( $sql ) {
			$matches = self::_getGlobalMatches($sql);
		
			$key = isset($matches['select']) ? 'select' : (isset($matches['set']) ? 'set' : false);
			
			if ($key === false) {
				return array();
			}
			
			$fields = explode(',', trim($matches[$key]) );
			foreach($fields as $key => $value) {
				if (preg_match('/(?:AS '.self::$startQuote.'([\w]+)'.self::$endQuote.')/', $value, $matches)) {
					$fields[$key] = trim( str_replace( '__', '.', $matches[1] ) );
				}
				elseif (preg_match('/'.self::$startQuote.'([\w]+)'.self::$endQuote.'.'.self::$startQuote.'([\w]+)'.self::$endQuote.'/', $value, $matches)) {
					$fields[$key] = trim( $matches[1] . '.' . $matches[2] );
				}
				elseif (preg_match('/'.self::$startQuote.'([\w]+)'.self::$endQuote.'/', $value, $matches)) {
					$fields[$key] = trim( $matches[1] );
				}
				else{
					$fields[$key] = trim( $fields[$key] );
				}
			}
			
			return isset($fields) && !empty($fields) ? $fields : array();
		}
		
		/**
		 * preg_match utilisé par plusieurs fonctions, ici on ne le calcul qu'une fois.
		 * Defini self::_cuttedSql et self::$_joins
		 * 
		 * @param string $sql Code SQL
		 * @return array self::_cuttedSql
		 */
		protected static function _getGlobalMatches( $sql ){
			if ( !empty(self::$_cuttedSql) ){
				return self::$_cuttedSql;
			}
			$Dbo = ClassRegistry::init('ConnectionManager')->getDataSource(self::$datasourceName);
			self::$startQuote = $Dbo->startQuote;
			self::$endQuote = $Dbo->endQuote;
			
			$cutThis = array(
				'SELECT',
				'UPDATE',
				'SET',
				'FROM',
				'WHERE',
				'ORDER BY',
				'LIMIT'
			);

			foreach($cutThis as $key => $word) {
				$pos = strpos(strtoupper($sql), $word);
				$posNext = false;
				$i = $key +1;
				while($posNext === false) {
					if (!isset($cutThis[$i])) {
						$posNext = strlen($sql) +1;
						break;
					}
					$posNext = strpos(strtoupper($sql), $cutThis[$i]);
					$i++;
				}

				if ($pos !== false) {
					$offset = $pos + strlen($word) +1;
					$size = $posNext - $offset -1;
					$matches[strtolower($word)] = substr($sql, $offset, $size);
				}
			}

			$matches['from'] = isset($matches['from']) ? 'FROM ' . $matches['from'] : (isset($matches['update']) ? 'UPDATE ' . $matches['update'] : '');
			$matches['from'] = str_replace('INNER JOIN', '{{cutme}}INNER JOIN', $matches['from']);
			$matches['from'] = preg_replace('/(LEFT|RIGHT) (?:OUTER )*JOIN/', '{{cutme}}$1 JOIN', $matches['from']);
			self::$_joins = explode('{{cutme}}', $matches['from']);
			self::$_cuttedSql = $matches;
			
			return self::$_cuttedSql;
		}
		
		/**
		 * Décore une chaine 
		 * 
		 * @param string $title La chaine à décorer
		 * @return string La chaine décorée
		 */
		protected static function _titleise( $title ) {
			return  "\n#################################################################################\n"
					. __d('analysesql', $title) . "\n"
					. "#################################################################################\n"
			;
		}
		
		/**
		 * Ajoute un span autour d'un remplacement de parenthèses et ajoute son contenu dans le title de la balise
		 * 
		 * @param string $text Le texte à afficher
		 * @param array $bracketsInnerText Contenu des parenthèses : SELECT [0] FROM ... le [0] désigne $bracketsInnerText[0]
		 * @return string Texte avec à la place d'un [0], un <span title="$bracketsInnerText[0]">[0]</span>
		 */
		protected static function _spanBrackets( $text, $bracketsInnerText ) {
			$offset = 0;
			while(preg_match('/\[(?P<bracket>[\d]+)\]/', $text, $matches, PREG_OFFSET_CAPTURE, $offset)) {
				$offset = $matches['bracket'][1];
				$prev = substr($text, 0, $offset-1);
				$next = substr($text, $offset-1 + strlen($matches[0][0]));
				$insert = '<span title="' . h($bracketsInnerText[$matches['bracket'][0]]) . '">[' . $matches['bracket'][0] . ']</span>';
				$offset += strlen($insert);
				$text = $prev . $insert . $next;
			}
			
			return $text;
		}
		
		/**
		 * Récupère les conditions d'une requète SQL
		 * 
		 * @param string $sql Code SQL
		 * @param array $bracketsInnerText Contenu des parenthèses : SELECT [0] FROM ... le [0] désigne $bracketsInnerText[0]
		 * @return array Liste des conditions au format SQL
		 */
		protected static function _extractConditions( $sql, $bracketsInnerText ) {
			$matches = self::_getGlobalMatches($sql);
			
			if (!isset($matches['where'])) {
				return array();
			}
			
			$conditions = preg_split('/AND|OR/', $matches['where']);
			$unsetKeys = array();
			
			foreach($conditions as $key => $value) {
				if (strpos($value, 'BETWEEN')) {
					$value = $value . 'AND' . $conditions[$key+1];
					$conditions[$key] = $value;
					$unsetKeys[] = $key+1;
				}
				
				$conditions[$key] = trim($value);
				preg_match('/\[(?P<bracket>[\d]+)\]/', $conditions[$key], $matches, PREG_OFFSET_CAPTURE);
				if (isset($matches['bracket'][0]) && isset($bracketsInnerText[$matches['bracket'][0]])) {
					$offset = $matches['bracket'][1];
					$prev = substr($conditions[$key], 0, $offset-1);
					$next = substr($conditions[$key], $offset-1 + strlen($matches[0][0]));
					$conditions[$key] = $prev . '('. $bracketsInnerText[$matches['bracket'][0]] . ')' . $next;
				}
			}
			
			foreach($unsetKeys as $value) {
				unset($conditions[$value]);
			}
			
			return $conditions;
		}
		
		/**
		 * Analyse une requête sql et permet l'affichage d'un rapport
		 * 
		 * @param string $sqlData Requête SQL à analyser
		 * @return array Rapport sur la requête sous forme array('text' => $text, 'innerBrackets' => $innerBrackets);
		 */
		public static function analyse( $sqlData ) {
			self::reset();
			$formatedSql = self::_bracketsInnerText( $sqlData );
			$sql = $formatedSql['sql'];
			$bracketsInnerText = $formatedSql['bracketsInnerText'];
			$fields = self::_extractFields( $sql );
			$joins = self::_extractJoins( $sql, $bracketsInnerText );
			$conditions = self::_extractConditions( $sql, $bracketsInnerText );
			$randId = rand(0,9999999999);
			
			$sql2ln = preg_replace('/(BETWEEN .*)\n/', '$1',
				preg_replace('/(FROM|INNER|LEFT|RIGHT|WHERE|LIMIT|AND|OR)/', "\n$1",  str_replace(',', ",\n", $sql))
			);
			
			$text = self::_spanBrackets(
				  self::_titleise('Brakets.free.title')
				. '<input type="checkbox" onchange="$(\'noBraketsSqlReport'.$randId.'\').toggle();" checked="true">'
				. '<div id="noBraketsSqlReport'.$randId.'" style="display:block;">'
				. $sql2ln
				. '</div>'
				
				. self::_titleise('Brakets.contain.title')
				. '<input type="checkbox" onchange="$(\'innerBraketsReport'.$randId.'\').toggle();">'
				. '<div id="innerBraketsReport'.$randId.'" style="display:none;">'
				. var_export($bracketsInnerText, true)
				. '</div>'
				
				. self::_titleise('Fields.title')
				. '<input type="checkbox" onchange="$(\'sqlFieldsReport'.$randId.'\').toggle();">'
				. '<div id="sqlFieldsReport'.$randId.'" style="display:none;" class="restoreBrackets">'
				. var_export($fields, true)
				. '</div>'
				
				. self::_titleise('Joins.title')
				. '<input type="checkbox" onchange="$(\'sqlJoinsReport'.$randId.'\').toggle();">'
				. '<div id="sqlJoinsReport'.$randId.'" style="display:none;" class="restoreBrackets">'
				. var_export($joins, true)
				. '</div>'
				
				. self::_titleise('Conditions.title')
				. '<input type="checkbox" onchange="$(\'sqlConditionsReport'.$randId.'\').toggle();">'
				. '<div id="sqlConditionsReport'.$randId.'" style="display:none;" class="restoreBrackets">'
				. var_export($conditions, true)
				. '</div>'
			, $bracketsInnerText);
			
			$innerBrackets = array();
			foreach ($bracketsInnerText as $key => $value) {
				$innerBrackets[$key] = self::_spanBrackets($value, $bracketsInnerText);
			}
			
			$json = array(
				'text' => $text,
				'innerBrackets' => $innerBrackets,
				'random' => $randId
			);
			
			return $json;
		}
		
		/**
		 * Permet de choisir un nom de datasource
		 * 
		 * @param type $name
		 * @return boolean
		 */
		public static function setDatasourceName( $name = 'default' ) {
			self::$datasourceName = $name;			
			return true;
		}
		
		/**
		 * Destruction du cache
		 * 
		 * @return boolean
		 */
		public static function reset() {
			self::$_joins = array();
			self::$_cuttedSql = array();
			return true;
		}
	}
?>
