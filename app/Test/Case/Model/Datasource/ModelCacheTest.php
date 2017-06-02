<?php
	/**
	 * Code source de la classe ModelCacheTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model.Datasource
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ModelCacheTest réalise les tests unitaires de la classe ModelCache.
	 *
	 * @package app.Test.Case.Model.Datasource
	 */
	class ModelCacheTest extends CakeTestCase
	{
		/**
		 * Test des méthodes ModelCache::read(), ModelCache::write() et
		 * ModelCache::delete().
		 *
		 * @return void
		 */
		public function testAllMethods() {
			$key = 'lalala';
			$modelNames = array( 'Apple', 'User' );

			ModelCache::write( $key, $modelNames );

			$result = ModelCache::read( 'Apple' );
			$expected = array( 'lalala' );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			$result = ModelCache::delete( $key );
			$expected = 2;
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
