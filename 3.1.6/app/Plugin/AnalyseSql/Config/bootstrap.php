<?php
	/**
	 * Ajoute une vue à la liste existante pour préférer cette dernière à celle de cakephp
	 * Ajoute les traductions du plugin
	 * 
	 * @package AnalyseSql
	 * @subpackage Config
	 */
	App::build(
		array(
			'View' => CakePlugin::path( 'AnalyseSql' ).'View'.DS,
			'Locale' => CakePlugin::path( 'AnalyseSql' ).'Locale'.DS
		)
	);
?>