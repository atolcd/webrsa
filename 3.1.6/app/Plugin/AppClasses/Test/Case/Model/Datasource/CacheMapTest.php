<?php
	/**
	 * Code source de la classe CacheMapTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model.Datasource
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'CacheMap', 'AppClasses.Model/Datasource' );

	/**
	 * La classe CacheMapTest réalise les tests unitaires de la classe CacheMap.
	 *
	 * @package app.Test.Case.Model.Datasource
	 */
	class CacheMapTest extends CakeTestCase
	{
		/**
		 * Test des méthodes CacheMap::read(), CacheMap::write() et
		 * CacheMap::delete().
		 *
		 * @return void
		 */
		public function testAllMethods() {
			$key = 'lalala';
			$modelNames = array( 'Apple', 'User' );

			CacheMap::write( $key, $modelNames );

			$result = CacheMap::read( 'Apple' );
			$expected = array( 'lalala' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = CacheMap::delete( $key );
			$expected = 2;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test des méthodes CacheMap::read(), CacheMap::write() et
		 * CacheMap::delete() dans le cadre du code qui irait dans la méthode
		 * Structurereferente::_onChange()
		 *
		 * @return void
		 */
		public function testStructurereferenteOnChange() {
			CacheMap::write( 'Structurereferente_listoptions', array( 'Typeorient', 'Structurereferente' ) );
			CacheMap::write( 'Typeorient_listoptions', array( 'Typeorient' ) );
			CacheMap::write( 'User_listoptions', array( 'User', 'Group' ) );
			CacheMap::write( 'Group_listoptions', array( 'Group' ) );

			// Structurereferente::_onChange()
			$result = CacheMap::read( 'Structurereferente' );
			$expected = array( 'Structurereferente_listoptions' );
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
			//if( !empty( $result) ) {
				//foreach( $result as $cacheKey ) {
					//Cache::delete( $cacheKey );
				//}
			//}
			$result = CacheMap::delete( 'Structurereferente' );
			$expected = 1;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = CacheMap::read();
			$expected = array(
				'Typeorient' => array(
					1 => 'Typeorient_listoptions',
				),
				'Typeorient_listoptions' => array(
					0 => 'Typeorient',
				),
				'User_listoptions' => array(
					0 => 'User',
					1 => 'Group',
				),
				'User' => array(
					0 => 'User_listoptions',
				),
				'Group' => array(
					0 => 'User_listoptions',
					1 => 'Group_listoptions',
				),
				'Group_listoptions' => array(
					0 => 'Group',
				),
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}
	}
?>
