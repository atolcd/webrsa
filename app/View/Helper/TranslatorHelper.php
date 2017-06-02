<?php
	/**
	 * Fichier source de la classe TranslatorHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'DefaultUrl', 'Default.Utility' );

	/**
	 * La classe TranslatorHelper
	 *
	 * @package Translator
	 * @subpackage app.View.Helper
	 */
	class TranslatorHelper extends AppHelper
	{
		/**
		 * Normalize et ajoute les traductions à l'array donné
		 *
		 * @param array $fields
		 * @return array
		 */
		public function normalize(array $fields) {
			$results = array();
			foreach (Hash::normalize($fields) as $key => $field) {
				$camel = $key;
				$field = (array)$field;
				$params = array();

				if (Hash::get((array)$field, 'type') !== 'hidden') {
					if (strpos($key, '/') === 0) {
						$url = DefaultUrl::toArray($key);
						$camel = str_replace($url['controller'], Inflector::camelize($url['controller']), $key);
						$params = array(
							'title' => __m($camel),
							'msgid' => __m('/'.Inflector::camelize($url['controller']).'/'.$url['action']),
						);

					} elseif (strpos($key, 'data[') !== 0) {
						$params = array('label' => __m($key));
					}
				}

				$results[$key] = (array)$field + $params;

				if (Hash::get((array)$field, 'confirm') === true) {
					$results[$key]['confirm'] = __m($camel.' ?');
				}
			}
			return $results;
		}
	}