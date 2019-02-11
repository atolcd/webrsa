<?php
	/**
	 * Code source de la classe WebrsaTranslatorAutoloadComponent.
	 *
	 * @package Translator
	 * @subpackage Component
	 */

	App::uses('TranslatorAutoloadComponent', 'Translator.Controller/Component');
	App::uses('WebrsaTranslator', 'Utility');

	/**
	 * La classe WebrsaTranslatorAutoloadComponent ...
	 *
	 * @package Translator
	 * @subpackage Component
	 */
	class WebrsaTranslatorAutoloadComponent extends TranslatorAutoloadComponent
	{
		/**
		 * Name of the component.
		 *
		 * @var string
		 */
		public $name = 'WebrsaTranslatorAutoload';
		
		/**
		 * Default configuration.
		 *
		 * @var array
		 */
		public $_defaultConfig = array(
			'translatorClass' => 'WebrsaTranslator',
			'events' => array(
				'initialize' => 'load',
				'startup' => null,
				'beforeRender' => null,
				'beforeRedirect' => 'save',
				'shutdown' => 'save'
			)
		);
		
		/**
		 * Donne une liste de domaines potentiels
		 *
		 * @return array
		 */
		public function domains() {
		   if ($this->_domains === null) {
			   $Controller = $this->_Collection->getController();
			   $controllerName = Inflector::underscore(Hash::get($Controller->request->params, 'controller'));
			   $actionName = Inflector::underscore(Hash::get($Controller->request->params, 'action'));
			   $pluginName = ltrim(Inflector::underscore(Hash::get($Controller->request->params, 'plugin')) . '_', '_');
			   $suffix = rtrim('_' . Configure::read('WebrsaTranslator.suffix'), '_');
			   $possiblesDomains = array_values(
				   array_unique(
					   array(
						   $pluginName . $controllerName . '_' . $actionName . $suffix,
						   $pluginName . $controllerName . '_' . $actionName,
						   $controllerName . '_' . $actionName . $suffix,
						   $controllerName . '_' . $actionName,
						   $pluginName . $controllerName . $suffix,
						   $pluginName . $controllerName,
						   $controllerName . $suffix,
						   $controllerName,
						   'default'
					   )
				   )
			   );
			   
			   $lang = call_user_func(array($this->settings['translatorClass'], 'lang'));
			   $domains = array();
			   foreach ($possiblesDomains as $domain) {
				   foreach (App::path('locales') as $path){
						if (is_file($path . $lang . DS . 'LC_MESSAGES' . DS . $domain . '.po')){
							$domains[] = $domain;
							break;
						}
					}
			   }
			   
			   $this->_domains = $domains;
		   }
		   return $this->_domains;
		}
	}