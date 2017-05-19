<?php
	/**
	 * Fichier source de la classe FormValidatorHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @subpackage FormValidator
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe FormValidatorHelper permet de générer les règles à appliquer à un formulaire.
	 * A un lien avec :
	 * webrsa.validateRules.js
	 * webrsa.validateForms.js
	 *
	 * @package app.View.Helper
	 * @subpackage FormValidator
	 */
	class FormValidatorHelper extends AppHelper{

		/**
		 * Json sous la forme {'regle_validation': 'Message d\'erreur'}
		 * @var String
		 */
		public $traductions = 'undefined';

		/**
		 * Json sous la forme {'nom_model': {'nom_champ': {'rules' => ...}}}
		 * @var String
		 */
		public $validationJson = 'undefined';

		/**
		 * Genere le <script> nécéssaire pour utiliser la validation javascript
		 * A coller dans chaques vue utilisant un formulaire à vérifier :
		 * echo $this->FormValidator->generateJavascript();
		 *
		 * @return string
		 */
		public function generateJavascript( $additionnalFields=array(), $useRequestData=true ){
			$validationJS =
					Configure::read( 'ValidationJS.enabled' ) ?
					Configure::read( 'ValidationJS.enabled' ) : 0;
			$validationOnchange =
					Configure::read( 'ValidationOnchange.enabled' ) ?
					Configure::read( 'ValidationOnchange.enabled' ) : 0;
			$validationOnsubmit =
					Configure::read( 'ValidationOnsubmit.enabled' ) ?
					Configure::read( 'ValidationOnsubmit.enabled' ) : 0;
			$validationJson = $this->validationJson === 'undefined' ?
					$this->generateValidationRules($additionnalFields, $useRequestData)->validationJson : $this->validationJson;

			$script = '<script type="text/javascript">
//<![CDATA[
FormValidator.initializeVars({
	validationRules: ' . $validationJson . ',
	traductions: ' . $this->generateTraductions()->traductions . ',
	validationJS: ' . $validationJS . ',
	validationOnchange: ' . $validationOnchange . ',
	validationOnsubmit: ' . $validationOnsubmit . ',
	baseUrl: "'.Router::url( '/' ). '"
});
//]]>
</script>';

			return $script;
		}

		/**
		 * Génère un Json contenant les règles de validation selon le Model/field
		 * On peut manuelement renseigner les règles dans $additionnalFields
		 * On peut également désactiver la lecture de $this->request->data
		 *
		 * @param array $additionnalFields
		 * @param mixed $useRequestData
		 * @return \FormValidatorHelper
		 */
		public function generateValidationRules( $additionnalFields=array(), $useRequestData=true ){
			$json = array();

			if ( $useRequestData ){
				$requestDataIsString = is_string($useRequestData) && isset($this->request->data[$useRequestData]);
				$requestData = $requestDataIsString ? $this->request->data[$useRequestData] : (array)$this->request->data;

				if ( count($requestData) === 0 ){
					$this->validationJson = 'undefined';
					return $this;
				}

				foreach ( $requestData as $modelName => $champs ){
					$Model = ClassRegistry::init( $modelName );
					$validate = $Model->validate;
					$array = array($modelName => $validate);
					$json = array_merge($json, $array);
				}

				if ( $requestDataIsString ){
					$json = array( $useRequestData => $json );
				}
			}

			if ( count($additionnalFields) ){
				$json = hash::merge($json, $additionnalFields);
			}

			$encoded = json_encode($json);
			$this->validationJson = $encoded !== '[]' ? $encoded : 'undefined';
			return $this;
		}

		/**
		 * Génère un Json en fonction des règles de validation donné par generateValidationRules()
		 * Récupère les traduction selon le nom de la règle.
		 *
		 * @return \FormValidatorHelper
		 */
		public function generateTraductions(){
			if ( $this->validationJson === 'undefined' ){
				$this->traductions = 'undefined';
				return $this;
			}

			$traductions = array();

			$reglesUtilise = $this->_getRulesList();

			foreach($reglesUtilise as $regle){
				$nomTraduction = 'Validate::' . $regle;
				$traductions[$regle] = __d( 'default', $nomTraduction);

				if ($nomTraduction === $traductions[$regle]){
					$traductions[$regle] = '';
				}
			}

			$this->traductions = json_encode($traductions);
			return $this;
		}

		/**
		 * Renvoi une liste de toutes les règles de validation utilisé
		 * Utile pour generateTraductions()
		 *
		 * @return array
		 */
		protected function _getRulesList() {
			$json = str_replace(array('[',']'), '', $this->validationJson);
			$rule = true;
			$rules = array();
			$rulePos = 0;
			$limit = 0;
			while ( $limit < 10000 ) {
				$limit++;
				$lastRulePos = $rulePos;
				$rulePos = strpos($json, '"rule":', $rulePos) +8;
				if ( $rulePos <= 8 || $rulePos < $lastRulePos || $rule == false ) {
					break;
				}
				$nextQuote = strpos($json, '"', $rulePos);
				$rule = substr($json, $rulePos, $nextQuote - $rulePos);
				$rules[] = $rule;
			}

			return array_unique($rules);
		}

		/**
		 * Permet de retirer des vérifications par Model; par model->field ou par model->field->rule
		 *
		 * @param Mixed $validation
		 * @return \FormValidatorHelper
		 */
		public function removeValidations( $validation='*' ){
			if ( $validation === '*' || $this->validationJson === 'undefined' ) {
				$this->validationJson = 'undefined';
				return $this;
			}

			$json = json_decode( $this->validationJson, true );
			$validationArray = (array) $validation;

			foreach ( $json as $model => $field ){
				foreach ( $field as $key => $value ){
					$params = array( 'model' => $model, 'field' => $key, 'value' => $value );
					$json = $this->_removeThisValidation( $json, $validationArray, $params );
				}
			}

			$this->validationJson = json_encode($json);
			return $this;
		}

		/**
		 * Moteur de removeValidations, renvoi un array épuré de ses vérifications
		 *
		 * @param Array $json
		 * @param Array $validation
		 * @param Array $params
		 * @return Array
		 */
		public function _removeThisValidation( $json, $validation, $params ){
			foreach ( $validation as $key => $value ){
				if ( is_numeric($key) && $params['model'] === $value ){
					unset($json[$value]);
				}
				elseif ( $params['model'] === $key && $params['field'] === $value ){
					unset($json[$key][$value]);
				}
			}

			return $json;
		}

		/**
		 * Permet de n'éffectuer que les règles contenu dans rules.
		 *
		 * @param Mixed $rules
		 * @param Boolean $allowEmpty
		 * @param Array $additionnalFields
		 * @param Boolean $useRequestData
		 * @return \FormValidatorHelper
		 */
		public function checkOnly( $rules, $allowEmpty=true, $additionnalFields=array(), $useRequestData=true ){
			$rules = (array) $rules;
			if ( $this->validationJson === 'undefined' ){
				$this->generateValidationRules( $additionnalFields, $useRequestData );
			}

			$validationRules = json_decode( $this->validationJson, true );

			if ( is_string($useRequestData) && isset($validationRules[$useRequestData]) ){
				$validationRules = $validationRules[$useRequestData];
			}

			$validationCustom = $validationRules;

			if ( $validationRules === null ){
				return $this;
			}

			foreach( $validationRules as $modelName => $model ){
				foreach( $model as $fieldName => $field ){
					$validationCustom[$modelName][$fieldName] = $this->_onlyThisRule($rules, $allowEmpty, $validationCustom[$modelName][$fieldName], $field);
					if ( $validationCustom[$modelName][$fieldName] === null ){
						unset( $validationCustom[$modelName][$fieldName] );
					}
				}

				if ( empty($validationCustom[$modelName]) ){
					unset( $validationCustom[$modelName] );
				}
			}

			if ( is_string($useRequestData) && isset($validationRules[$useRequestData]) ){
				$validationCustom = array( $useRequestData => $validationCustom );
			}

			$this->validationJson = json_encode($validationCustom);
			return $this;
		}

		/**
		 * Moteur de checkOnly(), éfface tout autre vérifications que celles renseigné dans $rules
		 *
		 * @param Array $rules
		 * @param Boolean $allowEmpty
		 * @param Array $validationRule
		 * @param Mixed $field
		 * @return Array
		 */
		protected function _onlyThisRule( $rules, $allowEmpty, $validationRule, $field ){
			$isDate = false;
			if ( isset( $field['rule'] ) ){
				$field[0] = $field['rule'];
				unset($field['rule']);
			}

			foreach( $field as $rule ){
				$rule = (array)$rule;

				if ( isset($rule['rule']) ){
					$ruleName = is_array($rule['rule']) ? $rule['rule'][0] : $rule['rule'];
				}
				else{
					$ruleName = isset($rule[0]) ? $rule[0] : false;
				}

				if ( $ruleName && in_array( $ruleName, $rules ) ){
					$isDate = true;
					$validationRule = array( array('rule' => $ruleName, 'allowEmpty' => $allowEmpty) );
					break;
				}
			}

			return $isDate ? $validationRule : null;
		}
	}