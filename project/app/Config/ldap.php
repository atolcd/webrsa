<?php

/**
 * LDAP Configuration.
 *
 * Contains an array of settings to use for the LDAP configuration.
 *
 * To modify these parameters, copy this file into your own CakePHP APP/Config directory.
 *
 */


$config = array(
    /*
     * - `host` - The domain controller hostname. This can be a closure or a string.
     *    The closure allows you to modify the rules in the configuration without the
     *    need to modify the LDAP plugin. One host (string) should be returned when
     *    using closure.
     * - `baseDN` - The base DN for directory
     * - `port` - The port to use. Default is 389 and is not required.
     * - `hideErrors` - flag to show/hide errors
     *    message. Set in session to Flash.ldap for flashing
     * - `startTls` - enable TLS
     * - `commonBindDn` - common bind Dn
     * - `commonBindPassword` - Common bind password
     * - `auth` - auth options
     */
    'syncInterval' => 12,
    'host' => env('LDAP_CONFIG_HOST'),
    'port' => env('LDAP_CONFIG_PORT'),
    'baseDn' => env('LDAP_CONFIG_BASEDN'),
    'startTLS' => false,
    'hideErrors' => false,
    'commonBindDn' => '',
    'commonBindPassword' => '',
    'auth' => array(
        'searchFilter' => '(' . env("LDAP_USER_TYPE") . '={username})',
        'bindDn' => env("LDAP_USER_TYPE") . '={username},' . env('LDAP_CONFIG_BINDDN'),
        'callback' => 'ldapAuthCallback'
    )
);

Configure::write('Ldap', $config);
