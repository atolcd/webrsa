<?php
/**
 * CakephpConfigurationParserTest file
 *
 * PHP 5.3
 *
 * @package Configuration
 * @subpackage Test.Case.Utility
 */

App::uses('CakephpConfigurationParser', 'Configuration.Utility');
require_once CakePlugin::path('Configuration').DS.'Test'.DS.'Config'.DS.'test.php';

/**
 * CakephpConfigurationParserTest class
 *
 * @package Configuration
 * @subpackage Test.Case.Utility
 */
class CakephpConfigurationParserTest extends CakeTestCase
{
	public function test() {
		$result = CakephpConfigurationParser::parseFile(CakePlugin::path('Configuration').DS.'Test'.DS.'Config'.DS.'test.php');
		
		// Rend le resultat lisible
		foreach ($result as $key => $value) {
			$result[$key]['comment'] = explode('<br />', preg_replace('/ *\n */', '', $value['comment']));
			
			foreach ($result[$key]['comment'] as $subKey => $subValue) {
				if ($subValue === '') {
					unset($result[$key]['comment'][$subKey]);
				}
			}
		}
		
		$expected = array(
			'String.test' => array(
				'comment' => array(
					(int) 0 => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ',
					(int) 4 => 'Suspendisse accumsan turpis bibendum nisl pharetra, ut condimentum ante ',
					(int) 6 => 'porttitor.',
					(int) 10 => 'Etiam ullamcorper mollis dui, eget ornare orci ultricies vel. ',
					(int) 12 => 'Vivamus facilisis varius massa, id bibendum tortor ullamcorper vel.',
					(int) 16 => 'Nulla et rhoncus nisi. Vivamus feugiat ultrices rutrum. Donec ornare eget ',
					(int) 18 => 'odio ut eleifend. Fusce rhoncus congue elit in pulvinar. Curabitur cursus ',
					(int) 20 => 'ante libero. Donec a elementum nulla. Nam non diam ipsum. Nam quis sem id ',
					(int) 22 => 'velit semper mattis. Suspendisse eu metus vitae odio maximus sollicitudin ',
					(int) 24 => 'ac in dui. Mauris mollis, diam nec egestas tempor, nisl ipsum rutrum lectus, ',
					(int) 26 => 'in porttitor odio justo sit amet nibh. Phasellus vitae dictum justo.'
				),
				'value' => true
			),
			'isTest' => array(
				'comment' => array(
					(int) 0 => 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, ',
					(int) 2 => 'consectetur, adipisci velit...',
					(int) 3 => '<table><tbody>',
					(int) 4 => '<tr><th>@meta</th><td>string Comment</td>',
					(int) 5 => '<tr><th>@meta</th><td>array Another comment with multi-line</td>',
					(int) 6 => '<tr><th>@meta</th><td>integer last comment</td>',
					(int) 7 => '</tbody></table>'
				),
				'value' => array(
					'param1' => true,
					'param2' => false
				)
			),
			'key1' => array(
				'comment' => array(
					(int) 0 => 'Consecutive test'
				),
				'value' => true
			),
			'key2' => array(
				'comment' => array(
					(int) 0 => 'Consecutive test'
				),
				'value' => false
			),
			'key3' => array(
				'comment' => array(
					(int) 0 => 'Consecutive test'
				),
				'value' => (int) 155
			),
			'anotherTest' => array(
				'comment' => array(
					(int) 0 => 'Odd test'
				),
				'value' => array(
					(int) 1 => 'foo'
				)
			),
			'foo' => array(
				'comment' => array(
					(int) 0 => 'this is a comment'
				),
				'value' => 'bar'
			),
			'baz' => array(
				'comment' => array(
					(int) 0 => 'this is a comment'
				),
				'value' => 'bar'
			)
		);
		
		$this->assertEqual($result, $expected);
	}
}
