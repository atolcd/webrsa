<?php
/**
 * Code source de la classe DisplayValidationErrorsHelper.
 *
 * PHP 5.3
 *
 * @package DisplayValidationErrors
 * @subpackage View.Helper
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

App::uses('AppHelper', 'View/Helper');
App::uses('Translator', 'Translator.Utility');

/**
 * La classe DisplayValidationErrorsHelper permet d'afficher les erreurs non visible
 *
 * @package DisplayValidationErrors
 * @subpackage View.Helper
 */
class DisplayValidationErrorsHelper extends AppHelper
{
	/**
	 * @var array
	 */
	public $helpers = array('Html');

	/**
	 * Génère le javascript pour insérer les erreurs cachés dans un element au
	 * choix
	 * 
	 * @param string $cssIdentifier selecteur css (default 'p.error')
	 * @return string javascript
	 */
	public function into($cssIdentifier = 'p.error') {
		$cssIdentifier = addcslashes($cssIdentifier, "'");
		
		$errors = $this->getErrors();
		
		if (empty($errors)) {
			return '';
		}
		
		$traductions = $this->getTraductions(array_keys($errors));
		
		$jsonErrors = json_encode($errors);
		$jsonTraductions = json_encode($traductions);
		
		$script = <<<EOT

	var DisplayValidationErrors = {
			errors: $jsonErrors,
			traductions: $jsonTraductions,
			identifier: '$cssIdentifier'
	};

EOT;
		
		return $this->Html->tag('script', $script, array('type' => 'text/javascript'))
			."\n"
			. $this->Html->script('DisplayValidationErrors.display-hidden-errors');
	}
	
	/**
	 * Permet d'obtenir un array exploitable par le helper en fonction de
	 * validationErrors
	 * 
	 * @return array
	 */
	public function getErrors() {
		$errors = array();
		
		foreach ($this->_View->validationErrors as $modelName => $values) {
			foreach ($values as $key => $value) {
				if (is_numeric($key)) {
					foreach ($value as $fieldName => $v) {
						$errors = $this->_extractErrors($modelName.'.'.$fieldName, $v, $errors);
					}
				} else {
					$errors = $this->_extractErrors($modelName.'.'.$key, $value, $errors);
				}
			}
		}
		
		return $errors;
	}
	
	/**
	 * Merge les erreurs pour la fonction getErrors
	 * 
	 * @param string $key
	 * @param array $value
	 * @param array $errors
	 * @return array
	 */
	protected function _extractErrors($key, array $value, array $errors) {
		if (!array_key_exists($key, $errors)) {
			$errors[$key] = array();
		}
		$errors[$key] = array_merge($errors[$key], $value);
		
		return $errors;
	}
	
	/**
	 * Permet d'obtenir les traductions pour une liste de clef "Model.field"
	 * 
	 * @param array|string $keys
	 * @return array array($key => $traduction, ...)
	 */
	public function getTraductions($keys) {
		$translator = Translator::getInstance();
		$traductions = array();
		foreach ((array)$keys as $key) {
			$traductions[$key] = $translator::translate($key);
		}
		
		return $traductions;
	}
}
