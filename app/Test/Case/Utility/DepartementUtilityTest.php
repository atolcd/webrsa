<?php
	/**
	 * Code source de la classe DepartementUtilityTest.
	 *
	 * @package app.Test.Case.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'DepartementUtility', 'Utility' );

	/**
	 * Surcharge de la classe pour pouvoir accéder aux méthodes protégées
	 *
	 * @package app.Test.Case.Utility
	 */
	class DepartementUtilityTestMoi extends DepartementUtility
	{
		public static function compareTypeorient( array $data1, array $data2 ) {
			return self::_compareTypeorient( $data1, $data2 );
		}
	}

	/**
	 * La classe DepartementUtilityTest réalise les tests unitaires de la classe utilitaire DepartementUtility.
	 *
	 * @package app.Test.Case.Utility
	 */
	class DepartementUtilityTest extends CakeTestCase
	{
		protected $type = 'Utility';

		/**
		 * Préparation du test pour le CG 66
		 */
		public function setup() {
			Configure::write( 'Cg.departement', 66 );
			Configure::write( 'Orientstruct.typeorientprincipale', array( 'SOCIAL' => array( 4, 6 ), 'Emploi' => array( 1 ) ) );
		}

		/**
		 * Test de la méthode DepartementUtility::getTypeorientName() pour le CG 66
		 */
		public function testGetTypeorientName() {
			$CG = Configure::read( 'Cg.departement' );
			$domain = 'departement' . $CG;

			$data1 = array(
				array( 'Typeorient' => array( 'id' => 1, 'parentid' => 4 ) ),
				array( 'Typeorient' => array( 'id' => 1, 'parentid' => 4 ) ),
				array( 'Typeorient' => array( 'id' => 2, 'parentid' => 6 ) ),
				array( 'Typeorient' => array( 'id' => 3, 'parentid' => 1 ) ),
				array( 'Typeorient' => array( 'id' => 4, 'parentid' => 1 ) ),
				array( 'Typeorient' => array( 'id' => 5, 'parentid' => 5 ) ),
			);

			$this->_renderGetTypeorientNameTest( $data1, 0, __d( $domain, 'maintien' ) );

			$this->_renderGetTypeorientNameTest( $data1, 1, __d( $domain, 'maintien_changementstruct' ) );

			$this->_renderGetTypeorientNameTest( $data1, 2, __d( $domain, 'reorient' ) );

			$this->_renderGetTypeorientNameTest( $data1, 3, __d( $domain, 'maintien_changementstruct' ) );

			$this->_renderGetTypeorientNameTest( $data1, 4, __d( $domain, 'reorient' ) );

			$this->_renderGetTypeorientNameTest( $data1, 5, __d( $domain, 'premorient' ) );
		}

		/**
		 * Test de la méthode DepartementUtility::compareTypeorient() pour le CG 66
		 */
		public function testCompareTypeorientCg66() {
			// 1. Test de maintien de l'orientation
			$this->_renderCompareTypeorientTest(	array( 'Typeorient' => array( 'id' => 1, 'parentid' => 4 ) ),
								array( 'Typeorient' => array( 'id' => 1, 'parentid' => 4 ) ),
								'maintien'
			);

			// 2. Test de réorientation
			$this->_renderCompareTypeorientTest( array( 'Typeorient' => array( 'id' => 1, 'parentid' => 4 ) ),
								array( 'Typeorient' => array( 'id' => 2, 'parentid' => 5 ) ),
								'reorient'
			);

			// 3. Test de maintien de l'orientation avec changement de structure référente
			$this->_renderCompareTypeorientTest( array( 'Typeorient' => array( 'id' => 1, 'parentid' => 4 ) ),
								array( 'Typeorient' => array( 'id' => 2, 'parentid' => 4 ) ),
								'maintien_changementstruct'
			);

			// 4. Test en cas de structures référente du même groupe (Configure::write)
			$this->_renderCompareTypeorientTest( array( 'Typeorient' => array( 'id' => 1, 'parentid' => 4 ) ),
								array( 'Typeorient' => array( 'id' => 2, 'parentid' => 6 ) ),
								'maintien_changementstruct'
			);
		}

		protected function _renderCompareTypeorientTest( $in1, $in2, $expected ){
			$result = DepartementUtilityTestMoi::compareTypeorient( $in1, $in2 );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}

		protected function _renderGetTypeorientNameTest( $in1, $in2, $expected ){
			$result = DepartementUtilityTestMoi::getTypeorientName( $in1, $in2 );
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>
