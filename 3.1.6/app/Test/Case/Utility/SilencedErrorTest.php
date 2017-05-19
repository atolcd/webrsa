<?php
	/**
	 * Code source de la classe SilencedErrorTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'SilencedError', 'Utility' );

	/**
	 * La classe SilencedErrorTest réalise les tests unitaires de la classe SilencedError.
	 *
	 * @package app.Test.Case.Utility
	 */
	class SilencedErrorTest extends CakeTestCase
	{
		/**
		 * Test de la méthode SilencedError::name()
		 *
		 * @covers SilencedError::name
		 */
		public function testName() {
			$severities = array(
				E_ERROR => 'E_ERROR',
				E_WARNING => 'E_WARNING',
				E_PARSE => 'E_PARSE',
				E_NOTICE => 'E_NOTICE',
				E_CORE_ERROR => 'E_CORE_ERROR',
				E_CORE_WARNING => 'E_CORE_WARNING',
				E_COMPILE_ERROR => 'E_COMPILE_ERROR',
				E_COMPILE_WARNING => 'E_COMPILE_WARNING',
				E_USER_ERROR => 'E_USER_ERROR',
				E_USER_WARNING => 'E_USER_WARNING',
				E_USER_NOTICE => 'E_USER_NOTICE',
				E_STRICT => 'E_STRICT',
				E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
				E_DEPRECATED => 'E_DEPRECATED',
				E_USER_DEPRECATED => 'E_USER_DEPRECATED',
				null => null
			);

			foreach( $severities as $param => $expected ) {
				$result = SilencedError::name( $param );
				$this->assertEqual( $result, $expected, var_export( $result, true ) );
			}
		}

		/**
		 * Test de la méthode SilencedError::call() sans erreur.
		 *
		 * @covers SilencedError::call
		 */
		public function testCallNoError() {
			$result = SilencedError::call( 'preg_match', array( '/foo/', 'foo' ) );
			$expected = 1;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SilencedError::call() avec une erreur rendue
		 * silencieuse.
		 *
		 * @covers SilencedError::call
		 * @covers SilencedError::handler
		 */
		public function testCallCatchedError() {
			$result = SilencedError::call( 'preg_match', array( 'Bar', '' ) );
			$expected = null;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SilencedError::call() avec une erreur transformée
		 * en exception.
		 *
		 * @expectedException ErrorException
		 * @expectedExceptionMessage E_WARNING: preg_match(): Delimiter must not be alphanumeric or backslash
		 *
		 * @covers SilencedError::call
		 * @covers SilencedError::handler
		 */
		public function testCallWithException() {
			SilencedError::call( 'preg_match', array( 'Bar', '' ), true );
		}

		/**
		 * Test de la méthode SilencedError::call() avec une erreur transformée
		 * en exception mais rendue silencieuse.
		 *
		 * @covers SilencedError::call
		 * @covers SilencedError::handler
		 */
		public function testCallCatchedErrorArobase() {
			$result = @SilencedError::call( 'preg_match', array( 'Bar', '' ), true );
			$expected = null;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SilencedError::call() avec une erreur transformée
		 * en exception et attrapée dans un block try / catch en mode debug > 0.
		 *
		 * @covers SilencedError::call
		 * @covers SilencedError::handler
		 */
		public function testCallWithExceptionMessage() {
			try {
				SilencedError::call( 'preg_match', array( 'Bar', '' ), true );
				$result = null;
			} catch( Exception $exception ) {
				$result = $exception->getMessage();
			}
			$expected = 'E_WARNING: preg_match(): Delimiter must not be alphanumeric or backslash';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode SilencedError::call() avec une erreur transformée
		 * en exception et attrapée dans un block try / catch en mode debug = 0.
		 *
		 * @covers SilencedError::call
		 * @covers SilencedError::handler
		 */
		public function testCallWithExceptionMessage2() {
			$debug = Configure::read( 'debug' );
			Configure::write( 'debug', 0 );
			try {
				SilencedError::call( 'preg_match', array( 'Bar', '' ), true );
				$result = null;
			} catch( Exception $exception ) {
				$result = $exception->getMessage();
			}
			Configure::write( 'debug', $debug );
			$expected = 'E_WARNING: preg_match(): Delimiter must not be alphanumeric or backslash';
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
