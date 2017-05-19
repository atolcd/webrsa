<?php
	/**
	 * Code source de la classe AnalysesqlTest.
	 *
	 * PHP 5.3
	 *
	 * @package AnalyseSql
	 * @subpackage Test.Case.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Analysesql', 'AnalyseSql.Utility' );
	
	/**
	 * La classe AnalysesqlTest ...
	 *
	 * @package AnalyseSql
	 * @subpackage Test.Case.Utility
	 */
	class AnalysesqlTest extends CakeTestCase
	{
		/**
		 * Test de la méthode Analysesql::Analyse()
		 */
		public function testAnalyse() {
			Analysesql::setDatasourceName( 'test' );
			$Dbo = ClassRegistry::init('ConnectionManager')->getDataSource('test');
			$s = $Dbo->startQuote;
			$e = $Dbo->endQuote;
			$hs = h($s);
			$he = h($e);
			
			$sql = 'SELECT EXIST(SELECT id FROM tables2 INNER JOIN foo ON ((foo.id = tables2.foo_id)) LIMIT 1) AS '.$s.'Table1__existtest'.$e.', '.$s.'Table1'.$e.'.'.$s.'name'.$e.' AS '.$s.'Table1__name'.$e.', '.$s.'Table2'.$e.'.'.$s.'foo'.$e.', COUNT(*) FROM '.$s.'tables1'.$e.' AS '.$s.'Table1'.$e.' INNER JOIN '.$s.'tables2'.$e.' AS '.$s.'Table2'.$e.' ON ('.$s.'Table1'.$e.'.'.$s.'table2_id'.$e.' = '.$s.'Table2'.$e.'.'.$s.'id'.$e.') LEFT OUTER JOIN '.$s.'tables3'.$e.' ON ((SELECT id FROM tables4 WHERE tables4.name = '.$s.'tables3'.$e.'.'.$s.'name'.$e.' LIMIT 1) = '.$s.'Table1'.$e.'.'.$s.'name'.$e.') WHERE '.$s.'Table2'.$e.'.'.$s.'name'.$e.' LIKE \'foo%\' AND '.$s.'Table2'.$e.'.'.$s.'name'.$e.' LIKE \'%bar\' AND (('.$s.'Table1'.$e.'.'.$s.'name'.$e.' = \'foobar\') OR ('.$s.'Table2'.$e.'.'.$s.'name'.$e.' = \'foobar\')) ORDER BY '.$s.'Table1'.$e.'.'.$s.'id'.$e.' DESC LIMIT 5';
			
			$result = Analysesql::analyse($sql);
			$expected = array (
  'text' => '
#################################################################################
'.__d('analysesql', 'Brakets.free.title').'
#################################################################################
<input type="checkbox" onchange="$(\'noBraketsSqlReport'.$result['random'].'\').toggle();" checked="true"><div id="noBraketsSqlReport'.$result['random'].'" style="display:block;">SELECT EXIST<span title="SELECT id FROM tables2 INNER JOIN foo ON [7] LIMIT 1">[11]</span> AS '.$s.'Table1__existtest'.$e.',
 '.$s.'Table1'.$e.'.'.$s.'name'.$e.' AS '.$s.'Table1__name'.$e.',
 '.$s.'Table2'.$e.'.'.$s.'foo'.$e.',
 COUNT<span title="*">[1]</span> 
FROM '.$s.'tables1'.$e.' AS '.$s.'Table1'.$e.' 
INNER JOIN '.$s.'tables2'.$e.' AS '.$s.'Table2'.$e.' ON <span title="'.$hs.'Table1'.$he.'.'.$hs.'table2_id'.$he.' = '.$hs.'Table2'.$he.'.'.$hs.'id'.$he.'">[2]</span> 
LEFT OUTER JOIN '.$s.'tables3'.$e.' ON <span title="[3] = '.$hs.'Table1'.$he.'.'.$hs.'name'.$he.'">[8]</span> 
WHERE '.$s.'Table2'.$e.'.'.$s.'name'.$e.' LIKE \'foo%\' 
AND '.$s.'Table2'.$e.'.'.$s.'name'.$e.' LIKE \'%bar\' 
AND <span title="[4] OR [5]">[9]</span> 
ORDER BY '.$s.'Table1'.$e.'.'.$s.'id'.$e.' DESC 
LIMIT 5</div>
#################################################################################
'.__d('analysesql', 'Brakets.contain.title').'
#################################################################################
<input type="checkbox" onchange="$(\'innerBraketsReport'.$result['random'].'\').toggle();"><div id="innerBraketsReport'.$result['random'].'" style="display:none;">array (
  1 => \'*\',
  2 => \''.$s.'Table1'.$e.'.'.$s.'table2_id'.$e.' = '.$s.'Table2'.$e.'.'.$s.'id'.$e.'\',
  3 => \'SELECT id FROM tables4 WHERE tables4.name = '.$s.'tables3'.$e.'.'.$s.'name'.$e.' LIMIT 1\',
  4 => \''.$s.'Table1'.$e.'.'.$s.'name'.$e.' = \\\'foobar\\\'\',
  5 => \''.$s.'Table2'.$e.'.'.$s.'name'.$e.' = \\\'foobar\\\'\',
  7 => \'foo.id = tables2.foo_id\',
  8 => \'<span title="SELECT id FROM tables4 WHERE tables4.name = '.$hs.'tables3'.$he.'.'.$hs.'name'.$he.' LIMIT 1">[3]</span> = '.$s.'Table1'.$e.'.'.$s.'name'.$e.'\',
  9 => \'<span title="'.$hs.'Table1'.$he.'.'.$hs.'name'.$he.' = &#039;foobar&#039;">[4]</span> OR <span title="'.$hs.'Table2'.$he.'.'.$hs.'name'.$he.' = &#039;foobar&#039;">[5]</span>\',
  11 => \'SELECT id FROM tables2 INNER JOIN foo ON <span title="foo.id = tables2.foo_id">[7]</span> LIMIT 1\',
)</div>
#################################################################################
'.__d('analysesql', 'Fields.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlFieldsReport'.$result['random'].'\').toggle();"><div id="sqlFieldsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => \'Table1.existtest\',
  1 => \'Table1.name\',
  2 => \'Table2.foo\',
  3 => \'COUNT<span title="*">[1]</span>\',
)</div>
#################################################################################
'.__d('analysesql', 'Joins.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlJoinsReport'.$result['random'].'\').toggle();"><div id="sqlJoinsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => 
  array (
    \'table\' => \'"tables1"\',
    \'alias\' => \'Table1\',
    \'type\' => \'FROM\',
    \'conditions\' => \'\',
  ),
  1 => 
  array (
    \'table\' => \'"tables2"\',
    \'alias\' => \'Table2\',
    \'type\' => \'INNER\',
    \'conditions\' => \''.$s.'Table1'.$e.'.'.$s.'table2_id'.$e.' = '.$s.'Table2'.$e.'.'.$s.'id'.$e.'\',
  ),
  2 => 
  array (
    \'table\' => \'"tables3"\',
    \'alias\' => \'\',
    \'type\' => \'LEFT\',
    \'conditions\' => \'<span title="SELECT id FROM tables4 WHERE tables4.name = '.$hs.'tables3'.$he.'.'.$hs.'name'.$he.' LIMIT 1">[3]</span> = '.$s.'Table1'.$e.'.'.$s.'name'.$e.'\',
  ),
)</div>
#################################################################################
'.__d('analysesql', 'Conditions.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlConditionsReport'.$result['random'].'\').toggle();"><div id="sqlConditionsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => \''.$s.'Table2'.$e.'.'.$s.'name'.$e.' LIKE \\\'foo%\\\'\',
  1 => \''.$s.'Table2'.$e.'.'.$s.'name'.$e.' LIKE \\\'%bar\\\'\',
  2 => \'(<span title="'.$hs.'Table1'.$he.'.'.$hs.'name'.$he.' = &#039;foobar&#039;">[4]</span> OR <span title="'.$hs.'Table2'.$he.'.'.$hs.'name'.$he.' = &#039;foobar&#039;">[5]</span>)\',
)</div>',
  'innerBrackets' => 
  array (
    1 => '*',
    2 => ''.$s.'Table1'.$e.'.'.$s.'table2_id'.$e.' = '.$s.'Table2'.$e.'.'.$s.'id'.$e.'',
    3 => 'SELECT id FROM tables4 WHERE tables4.name = '.$s.'tables3'.$e.'.'.$s.'name'.$e.' LIMIT 1',
    4 => ''.$s.'Table1'.$e.'.'.$s.'name'.$e.' = \'foobar\'',
    5 => ''.$s.'Table2'.$e.'.'.$s.'name'.$e.' = \'foobar\'',
    7 => 'foo.id = tables2.foo_id',
    8 => '<span title="SELECT id FROM tables4 WHERE tables4.name = '.$hs.'tables3'.$he.'.'.$hs.'name'.$he.' LIMIT 1">[3]</span> = '.$s.'Table1'.$e.'.'.$s.'name'.$e.'',
    9 => '<span title="'.$hs.'Table1'.$he.'.'.$hs.'name'.$he.' = &#039;foobar&#039;">[4]</span> OR <span title="'.$hs.'Table2'.$he.'.'.$hs.'name'.$he.' = &#039;foobar&#039;">[5]</span>',
    11 => 'SELECT id FROM tables2 INNER JOIN foo ON <span title="foo.id = tables2.foo_id">[7]</span> LIMIT 1',
  ),
  'random' => $result['random']
);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
			
			$sql = 'SELECT myfunction(\'foo\')';
			$result = Analysesql::analyse($sql);
			$expected = array (
  'text' => '
#################################################################################
'.__d('analysesql', 'Brakets.free.title').'
#################################################################################
<input type="checkbox" onchange="$(\'noBraketsSqlReport'.$result['random'].'\').toggle();" checked="true"><div id="noBraketsSqlReport'.$result['random'].'" style="display:block;">SELECT myfunction<span title="&#039;foo&#039;">[0]</span></div>
#################################################################################
'.__d('analysesql', 'Brakets.contain.title').'
#################################################################################
<input type="checkbox" onchange="$(\'innerBraketsReport'.$result['random'].'\').toggle();"><div id="innerBraketsReport'.$result['random'].'" style="display:none;">array (
  0 => \'\\\'foo\\\'\',
)</div>
#################################################################################
'.__d('analysesql', 'Fields.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlFieldsReport'.$result['random'].'\').toggle();"><div id="sqlFieldsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => \'myfunction<span title="&#039;foo&#039;">[0]</span>\',
)</div>
#################################################################################
'.__d('analysesql', 'Joins.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlJoinsReport'.$result['random'].'\').toggle();"><div id="sqlJoinsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
)</div>
#################################################################################
'.__d('analysesql', 'Conditions.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlConditionsReport'.$result['random'].'\').toggle();"><div id="sqlConditionsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
)</div>',
  'innerBrackets' => 
  array (
    0 => '\'foo\'',
  ),
  'random' => $result['random'],
);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
			
			$sql = 'UPDATE '.$s.'public'.$e.'.'.$s.'foos'.$e.' AS '.$s.'Foo'.$e.' SET '.$s.'Foo'.$e.'.'.$s.'name'.$e.' = MYFUNCTION(\'Foo\', \'Bar\') WHERE '.$s.'Foo'.$e.'.'.$s.'name'.$e.' LIKE \'Foobar%\'';
			$result = Analysesql::analyse($sql);
			$expected = array (
  'text' => '
#################################################################################
'.__d('analysesql', 'Brakets.free.title').'
#################################################################################
<input type="checkbox" onchange="$(\'noBraketsSqlReport'.$result['random'].'\').toggle();" checked="true"><div id="noBraketsSqlReport'.$result['random'].'" style="display:block;">UPDATE '.$s.'public'.$e.'.'.$s.'foos'.$e.' AS '.$s.'Foo'.$e.' SET '.$s.'Foo'.$e.'.'.$s.'name'.$e.' = MYFUNCTION<span title="&#039;Foo&#039;, &#039;Bar&#039;">[0]</span> 
WHERE '.$s.'Foo'.$e.'.'.$s.'name'.$e.' LIKE \'Foobar%\'</div>
#################################################################################
'.__d('analysesql', 'Brakets.contain.title').'
#################################################################################
<input type="checkbox" onchange="$(\'innerBraketsReport'.$result['random'].'\').toggle();"><div id="innerBraketsReport'.$result['random'].'" style="display:none;">array (
  0 => \'\\\'Foo\\\', \\\'Bar\\\'\',
)</div>
#################################################################################
'.__d('analysesql', 'Fields.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlFieldsReport'.$result['random'].'\').toggle();"><div id="sqlFieldsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => \'Foo.name\',
)</div>
#################################################################################
'.__d('analysesql', 'Joins.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlJoinsReport'.$result['random'].'\').toggle();"><div id="sqlJoinsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => 
  array (
    \'table\' => \'"foos"\',
    \'alias\' => \'Foo\',
    \'type\' => \'UPDATE\',
    \'conditions\' => \'\',
  ),
)</div>
#################################################################################
'.__d('analysesql', 'Conditions.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlConditionsReport'.$result['random'].'\').toggle();"><div id="sqlConditionsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => \''.$s.'Foo'.$e.'.'.$s.'name'.$e.' LIKE \\\'Foobar%\\\'\',
)</div>',
  'innerBrackets' => 
  array (
    0 => '\'Foo\', \'Bar\'',
  ),
  'random' => $result['random'],
);
			
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
			
			$sql = 'UPDATE '.$s.'public'.$e.'.'.$s.'connections'.$e.' SET '.$s.'modified'.$e.' = \'2015-07-17 10:40:23\' WHERE '.$s.'public'.$e.'.'.$s.'connections'.$e.'.'.$s.'id'.$e.' = 244397';
			$result = Analysesql::analyse($sql);
			$expected = array (
  'text' => '
#################################################################################
'.__d('analysesql', 'Brakets.free.title').'
#################################################################################
<input type="checkbox" onchange="$(\'noBraketsSqlReport'.$result['random'].'\').toggle();" checked="true"><div id="noBraketsSqlReport'.$result['random'].'" style="display:block;">UPDATE '.$s.'public'.$e.'.'.$s.'connections'.$e.' SET '.$s.'modified'.$e.' = \'2015-07-17 10:40:23\' 
WHERE '.$s.'public'.$e.'.'.$s.'connections'.$e.'.'.$s.'id'.$e.' = 244397</div>
#################################################################################
'.__d('analysesql', 'Brakets.contain.title').'
#################################################################################
<input type="checkbox" onchange="$(\'innerBraketsReport'.$result['random'].'\').toggle();"><div id="innerBraketsReport'.$result['random'].'" style="display:none;">array (
)</div>
#################################################################################
'.__d('analysesql', 'Fields.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlFieldsReport'.$result['random'].'\').toggle();"><div id="sqlFieldsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => \'modified\',
)</div>
#################################################################################
'.__d('analysesql', 'Joins.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlJoinsReport'.$result['random'].'\').toggle();"><div id="sqlJoinsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => 
  array (
    \'table\' => \'"connections"\',
    \'alias\' => \'connections\',
    \'type\' => \'UPDATE\',
    \'conditions\' => \'\',
  ),
)</div>
#################################################################################
'.__d('analysesql', 'Conditions.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlConditionsReport'.$result['random'].'\').toggle();"><div id="sqlConditionsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => \''.$s.'public'.$e.'.'.$s.'connections'.$e.'.'.$s.'id'.$e.' = 244397\',
)</div>',
  'innerBrackets' => 
  array (
  ),
  'random' => $result['random'],
);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
			
			$sql = 'DELETE FROM '.$s.'foos'.$e.' AS '.$s.'Foo'.$e.' WHERE '.$s.'Foo'.$e.'.'.$s.'bar'.$e.' IS NULL';
			$result = Analysesql::analyse($sql);
			$expected = array (
  'text' => '
#################################################################################
'.__d('analysesql', 'Brakets.free.title').'
#################################################################################
<input type="checkbox" onchange="$(\'noBraketsSqlReport'.$result['random'].'\').toggle();" checked="true"><div id="noBraketsSqlReport'.$result['random'].'" style="display:block;">DELETE 
FROM '.$s.'foos'.$e.' AS '.$s.'Foo'.$e.' 
WHERE '.$s.'Foo'.$e.'.'.$s.'bar'.$e.' IS NULL</div>
#################################################################################
'.__d('analysesql', 'Brakets.contain.title').'
#################################################################################
<input type="checkbox" onchange="$(\'innerBraketsReport'.$result['random'].'\').toggle();"><div id="innerBraketsReport'.$result['random'].'" style="display:none;">array (
)</div>
#################################################################################
'.__d('analysesql', 'Fields.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlFieldsReport'.$result['random'].'\').toggle();"><div id="sqlFieldsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
)</div>
#################################################################################
'.__d('analysesql', 'Joins.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlJoinsReport'.$result['random'].'\').toggle();"><div id="sqlJoinsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => 
  array (
    \'table\' => \'"foos"\',
    \'alias\' => \'Foo\',
    \'type\' => \'FROM\',
    \'conditions\' => \'\',
  ),
)</div>
#################################################################################
'.__d('analysesql', 'Conditions.title').'
#################################################################################
<input type="checkbox" onchange="$(\'sqlConditionsReport'.$result['random'].'\').toggle();"><div id="sqlConditionsReport'.$result['random'].'" style="display:none;" class="restoreBrackets">array (
  0 => \''.$s.'Foo'.$e.'.'.$s.'bar'.$e.' IS NULL\',
)</div>',
  'innerBrackets' => 
  array (
  ),
  'random' => $result['random'],
);
			
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>