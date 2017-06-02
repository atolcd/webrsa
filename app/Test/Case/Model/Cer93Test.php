<?php
	/**
	 * Code source de la classe Cer93Test.
	 *
	 * PHP 5.3
	 *
	 * FIXME: Contratinsertion.dd_ci, Contratinsertion.df_ci, Contratinsertion.duree
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Cer93Test réalise les tests unitaires de la classe Cer93.
	 *
	 * @package app.Test.Case.Model
	 */
	class Cer93Test extends CakeTestCase
	{
		/**
		 * Fixtures utilisées dans ce test unitaire.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Contratinsertion',
			'app.Cer93',
			'app.Cer93Sujetcer93',
			'app.Sujetcer93',
		);

		/**
		 * Méthode exécutée avant chaque test.
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			Configure::write( 'Cg.departement', 93 );
			Configure::write(
				'Cer93.Sujetcer93.Romev3',
				array(
					'path' => 'Sujetcer93.Sujetcer93.{n}.soussujetcer93_id',
					'values' => array( 1 )
				)
			);
			$this->Cer93 = ClassRegistry::init( 'Cer93' );
		}

		/**
		 * Méthode exécutée après chaque test.
		 *
		 * @return void
		 */
		public function tearDown() {
			unset( $this->Cer93 );
		}

		/**
		 * Test de la méthode Cer93::personneId().
		 *
		 * @return void
		 */
		public function testPersonneId() {
			// 1. Avec un CER existant
			$result = $this->Cer93->personneId( 1 );
			$this->assertEqual( $result, 2, var_export( $result, true ) );

			// 1. Avec un CER inexistant
			$result = $this->Cer93->personneId( 666 );
			$this->assertNull( $result );
		}
	}
?>