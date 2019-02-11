<?php
	/**
	 * Defini les functions __m et __mn
	 * 
	 * @package Translator
	 * @subpackage Config
	 */
	App::uses('Translator', 'Translator.Utility');

	if (!function_exists('__m')) {
		/**
		 * Permet d'obtenir la traduction d'une phrase de façon automatique.
		 * 
		 * @param string $singular
		 * @return string
		 */
		function __m($singular, $args = null) {
			$instance = Translator::getInstance();
			if (!is_array($args)) {
				$args = array_slice(func_get_args(), 1);
			}
			return vsprintf($instance::translate($singular), $args);
		}
	}
	
	if (!function_exists('__mn')) {
		/**
		 * Permet d'obtenir la traduction d'une phrase au singulier ou au pluriel de façon automatique.
		 * 
		 * @param string $singular
		 * @param string $plural
		 * @param integer $count
		 * @return string
		 */
		function __mn($singular, $plural, $count, $args = null) {
			$instance = Translator::getInstance();
			if (!is_array($args)) {
				$args = array_slice(func_get_args(), 3);
			}
			return vsprintf($instance::translate($singular, $plural, 6, $count), $args);
		}
	}
	
	if (!function_exists('__domain')) {
		/**
		 * Permet d'obtenir le nom du domain utilisé pour une traduction
		 * 
		 * @param string $singular
		 * @param string $plural
		 * @param integer $count
		 * @return string
		 */
		function __domain($singular, $plural = null, $category = 6, $count = null, $language = null) {
			$instance = Translator::getInstance();
			$instance::translate($singular, $plural, $category, $count, $language, $useCache = false);
			
			return $instance::$lastDomain;
		}
	}
