<?php
	/**
	 * Path to the tests directory.
	 */
	if (!defined('TESTS')) {
		define('TESTS', APP . 'Test' . DS);
	}

	/**
	 * Chemin vers les super fixtures
	 * 
	 * @package SuperFixture
	 * @subpackage Config
	 */
	App::build(
		array(
			'Fixture' => array(
				TESTS.'Fixture'.DS,
				CAKE.'Test'.DS.'Fixture'.DS
			),
			'SuperFixture' => array(TESTS.'SuperFixture'.DS),
		), App::REGISTER
	);

	/**
	 *  Trouve le chemin actuel
	 */
	foreach (App::path('Plugin') as $path) {
		if ($path === ROOT . DS . APP_DIR . DS . 'Plugin' . DS) {
			break;
		}
	}

	/**
	 * Inclusion de la bibliothèque Faker
	 */
	include_once $path.'SuperFixture'.DS.'Vendor'.DS.'autoload.php';