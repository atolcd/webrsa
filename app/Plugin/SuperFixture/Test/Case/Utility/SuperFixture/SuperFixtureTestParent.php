<?php
	/**
	 * SuperFixtureTestParent file
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('SuperFixture', 'SuperFixture.Utility');

	/**
	 * SuperFixtureTestParent class
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */
	class SuperFixtureTestParent extends CakeTestCase
	{
		/**
		 * Query de base pour les tests
		 * 
		 * @var array
		 */
		protected $_query = array(
			'fields' => array(
				'SuperFixtureFoo.name',
				'SuperFixtureFoo.integer_field',
				'SuperFixtureFoo.text_field',
				'SuperFixtureFoo.boolean_field',
				'SuperFixtureFoo.date_field',
				'SuperFixtureBar.name',
				'SuperFixtureBaz.name',
			),
			'joins' => array(
				array(
					'alias' => 'SuperFixtureBar',
					'table' => 'super_fixture_bars',
					'conditions' => 'SuperFixtureFoo.super_fixture_bar_id = SuperFixtureBar.id',
					'type' => 'INNER'
				),
				array(
					'alias' => 'SuperFixtureBaz',
					'table' => 'super_fixture_bazs',
					'conditions' => 'SuperFixtureBar.super_fixture_baz_id = SuperFixtureBaz.id',
					'type' => 'LEFT'
				),
			)
		);
		
		/**
		 * setUp method
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			
			$fixturePaths = array();
			$superFixturePaths = array();
			foreach (App::path('Plugin') as $path) {
				$fixturePaths[] = $path.'SuperFixture'.DS.'Test'.DS.'Fixture'.DS;
				$superFixturePaths[] = $path.'SuperFixture'.DS.'Test'.DS.'SuperFixture'.DS;
			}
			
			App::build(
				array(
					'Fixture' => $fixturePaths,
					'SuperFixture' => $superFixturePaths,
				)
			);
		}
	}
?>