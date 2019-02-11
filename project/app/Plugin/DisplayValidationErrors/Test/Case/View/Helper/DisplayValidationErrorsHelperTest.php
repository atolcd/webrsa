<?php
/**
 * Code source de la classe DisplayValidationErrorsHelperTest.
 *
 * PHP 5.3
 *
 * @package DisplayValidationErrors
 * @subpackage Test.Case.View.Helper
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses('View', 'View');
App::uses('AppHelper', 'View/Helper');
App::uses('DisplayValidationErrorsHelper', 'DisplayValidationErrors.View/Helper');

/**
 * La classe DisplayValidationErrorsHelperTest ...
 *
 * @package DisplayValidationErrors
 * @subpackage Test.Case.View.Helper
 */
class DisplayValidationErrorsHelperTest extends CakeTestCase
{
	/**
	 * @var array
	 */
	public $fixtures = array(
		'plugin.DisplayValidationErrors.Monmodel'
	);
	
	/**
	 * Préparation du test.
	 */
	public function setUp() {
		parent::setUp();
		
		$controller = null;
		$this->View = new View($controller);
		$this->DisplayValidationErrors = new DisplayValidationErrorsHelper($this->View);
	}

	/**
	 * Test de la méthode DisplayValidationErrorsHelper::link()
	 */
	public function test() {
		$Model = ClassRegistry::init('Monmodel');
		$Model->useTable = 'monmodel';
		$Model->validate = array(
			'name' => array(
				'alphaNumeric' => array(
					'rule' => 'alphaNumeric',
					'message' => 'alphaNumeric',
					'last' => false
				),
				'minLength' => array(
					'rule' => array('minLength', 8),
					'message' => 'minLength'
				),
			),
			'fk' => array(
				'numeric',
				'message' => 'numeric'
			)
		);
		
		$data = array(
			array(
				'id' => '',
				'name' => '@toto',
				'fk' => 'fake',
			),
			array(
				'name' => 'random%happen',
				'fk' => 'test',
			),
		);
		$Model->saveAll($data, array('validate' => 'only'));
		
		$this->View->validationErrors = array(
			'Monmodel' => $Model->validationErrors
		);
		
		$result = $this->DisplayValidationErrors->into('p.error');
		$expected = '<script type="text/javascript">
	var DisplayValidationErrors = {
			errors: {"Monmodel.name":["alphaNumeric","minLength","alphaNumeric"],"Monmodel.fk":["This field cannot be left blank","This field cannot be left blank"]},
			traductions: {"Monmodel.name":"Monmodel.name","Monmodel.fk":"Monmodel.fk"},
			identifier: \'p.error\'
	};
</script>
<script type="text/javascript" src="/display_hidden_errors/js/display-hidden-errors.js"></script>';
		$this->assertEquals($expected, $result);
	}
}