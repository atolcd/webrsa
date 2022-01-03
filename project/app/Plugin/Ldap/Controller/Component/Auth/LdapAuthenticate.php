<?php

App::uses('Component', 'Controller');

App::uses('BaseAuthenticate', 'Controller/Component/Auth');
App::uses('ComponentCollection', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Ldap', 'Ldap.Lib/');
App::uses('LdapException', 'Ldap.Lib/Error/Exception');
/**
 * LDAP authentication adapter for AuthComponent
 *
 * Provides LDAP authentication for given username and password
 *
 * ## usage
 * Add LDAP auth to controllers component
 */
class LdapAuthenticate extends BaseAuthenticate
{
    protected $ldap = null;

    /**
     * Constructor
     *
     * {@inheritDoc}
     */
    public function __construct(ComponentCollection $registry, array $config = [])
    {
        $this->_config = array_merge(Configure::read('Ldap'), $config);

        parent::__construct($registry, $config);
    }

    /**
     * Checks the fields to ensure they are supplied.
     *
     * @param CakeRequest $request The request that contains login information.
     * @param string $model The model used for login verification.
     * @param array $fields The fields to be checked.
     * @return bool False if the fields have not been supplied. True if they exist.
     */
	protected function _checkFields(CakeRequest $request, $model, $fields) {
		if (empty($request->data[$model])) {
			return false;
		}
		foreach (array($fields['username'], $fields['password']) as $field) {
			$value = $request->data($model . '.' . $field);
			if (empty($value) && $value !== '0' || !is_string($value)) {
				return false;
			}
		}
		return true;
	}

    /**
     * Authenticate user
     *
     * {@inheritDoc}
     */
    public function authenticate(CakeRequest $request, CakeResponse $response)
    {
        $userModel = $this->settings['userModel'];
		list(, $model) = pluginSplit($userModel);

		$fields = $this->settings['fields'];
		if (!$this->_checkFields($request, $model, $fields)) {
			return false;
		}

        return $this->_findUser($request->data[$model][$fields['username']], $request->data[$model][$fields['password']]);
    }

    /**
     * Find user method
     *
     * @param string $username Username
     * @param string $password Password
     * @return bool|array
     */
    public function _findUser($username, $password = null)
    {
        $this->ldap = new Ldap($this->_config);
        $ldapUserDetails = $this->ldap->authenticateUser($username, $password);
        $this->ldap->close($this->_config);

        if (!$ldapUserDetails || empty($ldapUserDetails[0]['mail'][0])) {
            return false;
        }

        $userEmail = $ldapUserDetails[0]['mail'][0];

        $user = parent::_findUser(['User.email' => $userEmail]);

        //Handle the callback in User Model
        $callback = $this->_config['auth']['callback'] ?? '';
        if (!empty($callback) && empty($user)) {
            return ClassRegistry::init('User')->$callback($username, $userEmail);
        }

        return $user;
    }
}
