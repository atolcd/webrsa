<?php
	/**
	 * Code source de la classe DepartementUtilityTest.
	 *
	 * @package app.Test.Case.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ValidateAllowEmptyUtility', 'Utility' );
	App::uses( 'AppModel', 'Model' );
	App::uses( 'Adresse', 'Model' );

	class AdresseTest extends Adresse
	{
		public $alias = 'Adresse';
		public $configuredAllowEmptyFields = array( 'libtypevoie', 'canton', 'nomvoie', 'pays' );
		public $validate = array(
			'libtypevoie' => array( NOT_BLANK_RULE_NAME => array( 'rule' => NOT_BLANK_RULE_NAME, 'message' => 'Champ obligatoire' ) ),
			'nomvoie' => array( NOT_BLANK_RULE_NAME => array( 'rule' => NOT_BLANK_RULE_NAME, 'message' => 'Champ obligatoire' ) ),
			'pays' => array( 'inList' => array( 'rule' => array('inList', array('France', 'Autre'), 'allowEmpty' => true ) ) )
		);
	}

	/**
	 * La classe DepartementUtilityTest réalise les tests unitaires de la classe utilitaire DepartementUtility.
	 *
	 * @package app.Test.Case.Utility
	 */
	class ValidateAllowEmptyUtilityTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisées dans ce test unitaire.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Adresse',
		);

		/**
		 * expected des traductions, si une traduction change, la changer également ici
		 *
		 * @var array
		 */
		public $traductions = array(
			'Adresse.libtypevoie' => 'Type de voie',
			'Adresse.canton' => 'Code Canton',
			'Adresse.nomvoie' => 'Nom de voie',
			'Adresse.pays' => 'Code Pays',
		);

		/**
		 * Préparation du test pour le CG 66
		 */
		public function setup() {
			Configure::delete('_ValidationConfiguredAllowEmptyFields');
			Cache::clear();

			Configure::write('ValidateAllowEmpty.Adresse.libtypevoie', true); // Base notEmpty -> allowEmpty
			Configure::write('ValidateAllowEmpty.Adresse.canton', false); // Base null -> notEmpty
			Configure::write('ValidateAllowEmpty.Adresse.nomvoie', false); // Base notEmpty -> notEmpty
			Configure::write('ValidateAllowEmpty.Adresse.pays', false); // Base allowEmpty -> notEmpty

			$this->Adresse = ClassRegistry::init( 'AdresseTest' );
		}

		/**
		 * Effectué après chaque méthode de test.
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->Adresse );
			ClassRegistry::flush();
		}

		/**
		 * Test de la méthode ValidateAllowEmptyUtility::configureKey()
		 */
		public function testConfigureKey() {
			$path = 'Adresse.libtypevoie';
			$result = ValidateAllowEmptyUtility::configureKey( $path );
			$expected = 'ValidateAllowEmpty.Adresse.libtypevoie';

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$path = '0.Adresse.canton';
			$result = ValidateAllowEmptyUtility::configureKey( $path );
			$expected = 'ValidateAllowEmpty.Adresse.canton';

			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ValidateAllowEmptyUtility::allConf()
		 */
		public function testAllConf() {
			$result = ValidateAllowEmptyUtility::allConf();
			$expected = array(
				'Adresse' => array(
					'libtypevoie' => true,
					'canton' => false,
					'nomvoie' => false,
					'pays' => false,
				)
			);

			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ValidateAllowEmptyUtility::initialize()
		 */
		public function testInitialize() {
			$result = $this->Adresse->validate;
			ValidateAllowEmptyUtility::initialize( $this->Adresse );
			$result['libtypevoie'] = array();

			$expected = $this->Adresse->validate;

			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ValidateAllowEmptyUtility::configuredFields()
		 */
		public function testConfiguredFields() {
			$result = ValidateAllowEmptyUtility::configuredFields( $this->Adresse );
			$expected = array(
				array('ValidateAllowEmpty.Adresse.libtypevoie' => array( 'rule' => 'isarray', 'allowEmpty' => true )),
				array('ValidateAllowEmpty.Adresse.canton' => array( 'rule' => 'isarray', 'allowEmpty' => true )),
				array('ValidateAllowEmpty.Adresse.nomvoie' => array( 'rule' => 'isarray', 'allowEmpty' => true )),
				array('ValidateAllowEmpty.Adresse.pays' => array( 'rule' => 'isarray', 'allowEmpty' => true )),
			);

			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode ValidateAllowEmptyUtility::label()
		 */
		public function testlabel() {
			$result = ValidateAllowEmptyUtility::label( 'Adresse.libtypevoie', 'adresse' );
			$expected = $this->traductions['Adresse.libtypevoie'];

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = ValidateAllowEmptyUtility::label( 'Adresse.canton', 'adresse' );
			$expected = $this->traductions['Adresse.canton'] . ' ' . REQUIRED_MARK;

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = ValidateAllowEmptyUtility::label( 'Adresse.nomvoie', 'adresse' );
			$expected = $this->traductions['Adresse.nomvoie'] . ' ' . REQUIRED_MARK;

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = ValidateAllowEmptyUtility::label( 'Adresse.pays', 'adresse' );
			$expected = $this->traductions['Adresse.pays'] . ' ' . REQUIRED_MARK;

			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
