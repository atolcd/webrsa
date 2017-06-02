<?php
	/**
	 * BasicsTest file
	 *
	 * PHP 5.3
	 *
	 * @package Appchecks
	 * @subpackage Test.Case
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once CakePlugin::path( 'Appchecks' ).'Lib'.DS.'basics.php';

	/**
	 * La classe BasicsTest est chargée des tests unitaires des fonctions utilitaires
	 * du plugin Appchecks.
	 *
	 * @package Appchecks
	 * @subpackage Test.Case
	 */
	class BasicsTest extends CakeTestCase
	{
		/**
		 * Test de la fonction version_id().
		 */
		public function testVersionId() {
			$result = version_id( '5' );
			$expected = 5000000;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = version_id( '5.6' );
			$expected = 5060000;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = version_id( '5.6.9' );
			$expected = 5060900;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = version_id( '5.6.9.1' );
			$expected = 5060901;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la fonction version_difference().
		 */
		public function testVersionDifference() {
			$result = version_difference( '5', '5.1' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = version_difference( '5.2', '5.1' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = version_difference( '5.2', '5.1', '5.3' );
			$expected = true;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = version_difference( '5.2', '5.1', '5.2' );
			$expected = false;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>