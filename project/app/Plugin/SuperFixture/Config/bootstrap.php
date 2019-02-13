<?php
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
		if ($path === PLUGIN_DIR) {
			break;
		}
	}

	/**
	 * Inclusion de la bibliothèque Faker
	 */
	include_once $path.'SuperFixture'.DS.'Vendor'.DS.'autoload.php';