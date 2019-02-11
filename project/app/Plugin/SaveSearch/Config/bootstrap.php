<?php
	/**
	 * Ajoute au à App::paths() les chemins MVC du plugin
	 * 
	 * @package SaveSearch
	 */
	App::uses('MultiDomainsTranslator', 'MultiDomainsTranslator.Utility');
	App::uses('Router', 'Routing');
	App::build(array('views' => CakePlugin::path('SaveSearch').'View'.DS));
	App::build(array('models' => CakePlugin::path('SaveSearch').'Model'.DS));
	App::build(array('controllers' => CakePlugin::path('SaveSearch').'Controller'.DS));
	App::build(array('locales' => CakePlugin::path('SaveSearch').'Locale'.DS));