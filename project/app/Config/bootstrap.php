<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Setup a 'default' cache configuration for use in the application.
 *
 * La gestion du cache est déplacée plus bas dans ce fichier, après la lecture de la
 * configuration générale, pour permettre la configuration du cache depuis WebRSA.
 */
/*
Cache::config( 'default', array(
	'engine' => 'File',
	'mask' => 0777,
	'duration' => ( Configure::read( 'debug' ) > 0 ? '+10 seconds' : '+999 days' )
) );
*/

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models/', '/next/path/to/models/'),
 *     'Model/Behavior'            => array('/path/to/behaviors/', '/next/path/to/behaviors/'),
 *     'Model/Datasource'          => array('/path/to/datasources/', '/next/path/to/datasources/'),
 *     'Model/Datasource/Database' => array('/path/to/databases/', '/next/path/to/database/'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions/', '/next/path/to/sessions/'),
 *     'Controller'                => array('/path/to/controllers/', '/next/path/to/controllers/'),
 *     'Controller/Component'      => array('/path/to/components/', '/next/path/to/components/'),
 *     'Controller/Component/Auth' => array('/path/to/auths/', '/next/path/to/auths/'),
 *     'Controller/Component/Acl'  => array('/path/to/acls/', '/next/path/to/acls/'),
 *     'View'                      => array('/path/to/views/', '/next/path/to/views/'),
 *     'View/Helper'               => array('/path/to/helpers/', '/next/path/to/helpers/'),
 *     'Console'                   => array('/path/to/consoles/', '/next/path/to/consoles/'),
 *     'Console/Command'           => array('/path/to/commands/', '/next/path/to/commands/'),
 *     'Console/Command/Task'      => array('/path/to/tasks/', '/next/path/to/tasks/'),
 *     'Lib'                       => array('/path/to/libs/', '/next/path/to/libs/'),
 *     'Locale'                    => array('/path/to/locales/', '/next/path/to/locales/'),
 *     'Vendor'                    => array('/path/to/vendors/', '/next/path/to/vendors/'),
 *     'Plugin'                    => array('/path/to/plugins/', '/next/path/to/plugins/'),
 * ));
 */

/**
 * Custom Inflector rules can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 */
require_once dirname( __FILE__ ).DS.'inflections.php';
Inflector::rules(
	'plural',
	array(
		'irregular' => $irregularPlural,
		'uninflected' => $uninflectedPlural
	)
);
Inflector::rules(
	'singular',
	array(
		'uninflected' => $uninflectedPlural
	)
);

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. Make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); // Loads a single plugin named DebugKit
 */
CakePlugin::loadAll(
	array(
		'Translator' => array( 'bootstrap' => true ),
		'AnalyseSql' => array( 'bootstrap' => true ),
		'SaveSearch' => array( 'bootstrap' => true ),
		'SuperFixture' => array( 'bootstrap' => true ),
		'Cake2Datepicker' => array( 'bootstrap' => true ),
		'Validation2' => array( 'bootstrap' => true ),
		'Password' => array( 'bootstrap' => true ),
		'Configuration' => array(),
		'Fluxcnaf' => array()
	)
);



/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'File',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'File',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));

// Code propre à web-rsa
define( 'REQUIRED_MARK', '<abbr class="required" title="Champ obligatoire">*</abbr>' );

// Définition de certains répertoires
define( 'MODELESODT_DIR', APP.'Vendor'.DS.'modelesodt'.DS );
define( 'CONFIGS', APP.'Config'.DS );

// @see http://api.cakephp.org/2.7/annotation-group-deprecated.html
if( false === defined( 'NOT_BLANK_RULE_NAME' ) ) {
	define( 'NOT_BLANK_RULE_NAME', version_compare( Configure::version(), '2.7.0', '<' ) ? 'notEmpty' : 'notBlank' );
}

if( false === defined( 'LOG_ERROR' ) ) {
	define( 'LOG_ERROR', LOG_ERR );
}

require_once CONFIGS.'webrsa.inc';
// Lecture de la configuration générale si elle n'a pas été encore lue
App::uses('Configuration', 'Model');
$configuration = new Configuration();
$configFiles = Configure::read('Categorie.General');
foreach ($configFiles as $configFile) {
	$configuration->setAllConfigurations($configFile);
}

if(Configure::read('Module.Ldap.enabled')) {
	require_once CONFIGS.'ldap.php';
	CakePlugin::load('Ldap');
}

// Setup a 'default' cache configuration for use in the application.
Cache::config( 'default', array(
	'engine' => 'File',
	'mask' => 0777,
	'duration' => ( Configure::read( 'debug' ) > 0 ? Configure::read( 'Configuration.cache.debug' ) : Configure::read( 'Configuration.cache.production' ) )
) );

require_once APPLIBS.'basics.php';
require_once APP.DS.'Vendor'.DS.'money_format.php';
require_once APP.'Lib'.DS.'Error'.DS.'rsa_exceptions.php';
App::uses( 'ModelCache', 'Model/Datasource' );
App::uses( 'ControllerCache', 'Model/Datasource' );

App::uses( 'WebrsaPermissions', 'Utility' );

/**
 * Paramétrage iconv.
 */
iconv_set_encoding( 'input_encoding', Configure::read( 'App.encoding' ) );
iconv_set_encoding( 'output_encoding', Configure::read( 'App.encoding' ) );
iconv_set_encoding( 'internal_encoding', Configure::read( 'App.encoding' ) );