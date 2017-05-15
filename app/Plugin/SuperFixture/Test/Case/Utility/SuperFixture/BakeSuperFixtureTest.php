<?php
	/**
	 * SuperFixtureWithFixtureTest file
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('SuperFixture', 'SuperFixture.Utility');
	App::uses('BSFObject', 'SuperFixture.Utility');
	App::uses('BakeSuperFixture', 'SuperFixture.Utility');
	require_once 'SuperFixtureTestParent.php';

	/**
	 * BakeSuperFixtureTest class
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */
	class BakeSuperFixtureTest extends SuperFixtureTestParent
	{
		/**
		 * Test de la disponnibilité de Faker
		 *
		 * @see https://github.com/fzaninotto/Faker
		 */
		public function testFaker() {
			$Faker = Faker\Factory::create('fr_FR');

			$Faker->seed(1234);
			$this->assertEquals( 'Jeannine Vallee', $Faker->name );
			$this->assertEquals( 'Louis Martel', $Faker->name );
			$this->assertEquals( 'Marion', $Faker->city );
			$this->assertEquals( 'Gautierboeuf', $Faker->city );
			$this->assertEquals( "82, place de Morin\n64474 Chevallier", $Faker->address );
			$this->assertEquals( "0131397292", $Faker->phoneNumber );
			$this->assertEquals( "sit", $Faker->unique()->word );		// Ideal pour les index unique
			$this->assertEquals( "veniam", $Faker->unique()->word );
		}

		/**
		 * Test de la fonction BakeSuperFixture::create
		 */
		public function testCreate() {
			/**
			 * Initialisation des variables, des tables et des classes
			 */
			SuperFixture::load($this, 'FooBar'); // On créer les tables pour le test
			$BakeSuperFixture = new BakeSuperFixture(); // Utilitaire

			// Definition des rêgles comme dans le cadre d'un BakeSuperFixtureInterface
			$SuperFixtureBaz = new BSFObject('SuperFixtureBaz');

			$SuperFixtureBar = new BSFObject('SuperFixtureBar');
			$SuperFixtureBar->fields = array(
				'super_fixture_baz_id' => array('foreignkey' => $SuperFixtureBaz->getName())	// Assignera l'id de SuperFixtureBaz
			);

			$SuperFixtureFoo = new BSFObject('SuperFixtureFoo');
			$SuperFixtureFoo->fields = array(
				'super_fixture_bar_id' => array('auto' => true)	// Assignera l'id de SuperFixtureBar
			);

			// Remplissage des tables...
			$data = compact('SuperFixtureBaz', 'SuperFixtureBar', 'SuperFixtureFoo'); // L'ordre est important
			$BakeSuperFixture->create($data, $sauvegarde_en_base = true);

			$Foo = ClassRegistry::init('SuperFixtureFoo');
			$add = array('order' => array('SuperFixtureFoo.id' => 'DESC'));

			/**
			 * Test 1 : definition automatique des champs obligatoires
			 */
			$result = $Foo->find('first', $this->_query + $add);
			$expected = array(
				'SuperFixtureFoo' => array(
					'name' => 'Id nisi qui id.',
					'integer_field' => (int) 78873,
					'text_field' => 'Ut iusto iusto accusamus iusto similique. Et a qui ducimus. Laudantium nihil autem omnis cum molestiae vel natus.',
					'boolean_field' => true,
					'date_field' => '2015-01-28'
				),
				'SuperFixtureBar' => array(
					'name' => 'Id et necessitatibus architecto aut consequatur debitis.'
				),
				'SuperFixtureBaz' => array(
					'name' => 'Perferendis voluptatibus incidunt nostrum quia possimus.'
				)
			);
			$this->assertEquals( $expected, $result, "Test 1");

			/**
			 * Test 2 : changement des valeurs en cas de rappel de la fonction
			 */
			$BakeSuperFixture->create($data, $sauvegarde_en_base);
			$result = $Foo->find('first', $this->_query + $add);
			$expected = array(
				'SuperFixtureFoo' => array(
					'name' => 'Debitis autem eveniet quis labore vel autem deleniti ut.',
					'integer_field' => (int) 90653093,
					'text_field' => 'Illo dolorum omnis repellendus voluptatibus nihil aut nisi. Rerum id tempore voluptate sit rem quia odit. Voluptas quasi ut qui.',
					'boolean_field' => false,
					'date_field' => '2008-09-10'
				),
				'SuperFixtureBar' => array(
					'name' => 'Provident quia et perferendis fuga.'
				),
				'SuperFixtureBaz' => array(
					'name' => 'Nostrum et voluptas consequatur delectus autem nam.'
				)
			);
			$this->assertEquals( $expected, $result, "Test 2");

			/**
			 * Test 3 : Ajout de rêgles pour générer les données
			 */
			// Rêgle in_array
			$SuperFixtureFoo->fields += array(
				'name' => array('in_array' => array('val1', 'val2', 'val3'))
			);

			// Valeur forcée
			$SuperFixtureBar->fields += array(
				'name' => array('value' => 'Une valeur forcée')
			);

			// Appel à la library Faker
			$SuperFixtureBaz->fields += array(
				'name' => array('faker' => array('rule' => 'regexify', '(Une|Des) valeur(|s) generée(|s) [0-9]{1,10}'))
			);

			$data = compact('SuperFixtureBaz', 'SuperFixtureBar', 'SuperFixtureFoo'); // L'ordre est important
			$BakeSuperFixture->create($data, $sauvegarde_en_base);
			$result = $Foo->find('first', $this->_query + $add);
			$expected = array(
				'SuperFixtureFoo' => array(
					'name' => 'val3',
					'integer_field' => (int) 796867180,
					'text_field' => 'Iste similique sint et libero consequatur enim. Qui et omnis pariatur.',
					'boolean_field' => true,
					'date_field' => '2016-06-12'
				),
				'SuperFixtureBar' => array(
					'name' => 'Une valeur forcée'
				),
				'SuperFixtureBaz' => array(
					'name' => 'Une valeur generée 429'
				)
			);
			$this->assertEquals( $expected, $result, "Test 3");

			/**
			 * Test 4 : mention "unique" NOTE : un 4e appel dans ce cas lance une exception car
			 * il n'y a plus de valeur unique (un reset est possible cela dit).
			 */
			$SuperFixtureFoo->fields['name'] = array('in_array' => array('val1', 'val2', 'val3'), 'unique' => true);
			$SuperFixtureBar->fields['name'] = array('in_array' => array('val1', 'val2', 'val3'), 'unique' => true);
			$SuperFixtureBaz->fields['name'] = array('in_array' => array('val1', 'val2', 'val3'), 'unique' => true);

			$data = compact('SuperFixtureBaz', 'SuperFixtureBar', 'SuperFixtureFoo');
			$BakeSuperFixture->create($data, true);
			$result = $Foo->find('first', $this->_query + $add);
			$expected = array(
				'SuperFixtureFoo' => array(
					'name' => 'val3',
					'integer_field' => (int) 41,
					'text_field' => 'Deserunt nihil quidem commodi quia vel accusamus quam temporibus. Quaerat deserunt consequatur eius et rem numquam modi cumque.',
					'boolean_field' => false,
					'date_field' => '2015-11-04'
				),
				'SuperFixtureBar' => array(
					'name' => 'val2'
				),
				'SuperFixtureBaz' => array(
					'name' => 'val1'
				)
			);
			$this->assertEquals( $expected, $result, "Test 4");

			/**
			 * Test 5 : contain et auto foreignkey
			 */
			$SuperFixtureBaz->fields['name'] += array('reset' => true); // Permet de faire un reset sur la mention unique
			$SuperFixtureBar->contain = array($SuperFixtureFoo);
			$SuperFixtureBaz->contain = array($SuperFixtureBar);
			$data = array($SuperFixtureBaz);

			$BakeSuperFixture->create($data, $sauvegarde_en_base);
			$result = $Foo->find('first', $this->_query + $add);
			$expected = array(
				'SuperFixtureFoo' => array(
					'name' => 'val1',
					'integer_field' => (int) 1,
					'text_field' => 'Ea et unde est dolor porro. Ipsa sed iste quidem veniam molestiae libero.
Aut ut accusamus molestias. Distinctio excepturi et qui et. Unde ipsum esse consectetur deleniti aut voluptatibus dicta quis.',
					'boolean_field' => false,
					'date_field' => '2015-04-25'
				),
				'SuperFixtureBar' => array(
					'name' => 'val2'
				),
				'SuperFixtureBaz' => array(
					'name' => 'val3'
				)
			);
			$this->assertEquals( $expected, $result, "Test 5");

			/**
			 * Test 6 : création des données en une seule ligne
			 */
			$result = $BakeSuperFixture->create(
				array(new BSFObject('SuperFixtureBaz', array('name' => array('faker' => array('rule' => 'regexify', 'generated_[0-9]{5}')))))
			);
			$expected = array(
				'name' => 'generated_84994',
				'created' => null,
				'updated' => null
			);
			$this->assertEquals( $expected, end($result['SuperFixtureBaz']), "Test 6" );

			/**
			 * Test 7 : autres parametres
			 */
			$result = $BakeSuperFixture->create(
				array(new BSFObject('SuperFixtureFoo',
					array(
						'name' => array('type' => 'datetime'), // Changement de type
						'text_field' => array('faker' => 'address'), // faker sans array
						'boolean_field' => array('default' => '0'), // valeur par defaut
					)
				))
			);
			$expected = array(
				'name' => '2010-11-13 05:25:35',
				'text_field' => '25, chemin Devaux
05 839 Legerdan',
				'super_fixture_bar_id' => '6',
				'integer_field' => (int) 9,
				'date_field' => '2015-12-05',
				'created' => null,
				'updated' => null
			);
			$this->assertEquals( $expected, end($result['SuperFixtureFoo']), "Test 7" );
		}

		/**
		 * Test de la fonction BakeSuperFixture::create
		 * On fait appel 3 fois à "unique" avec un array de seulement 2 éléments
		 *
		 * @expectedException OverflowException
		 */
		public function testCreateException() {
			SuperFixture::load($this, 'FooBar');
			$BakeSuperFixture = new BakeSuperFixture();
			$SuperFixtureBaz = new BSFObject('SuperFixtureBaz');
			$SuperFixtureBaz->fields['name'] = array('unique' => true, 'in_array' => array('val1', 'val2'));
			$SuperFixtureBaz->runs = 3;
			$BakeSuperFixture->create(array($SuperFixtureBaz));
		}

		/**
		 * Test de la fonction BakeSuperFixture::create
		 * On a fait une erreur dans la synthaxe de fields
		 *
		 * Message spécial foreignkey
		 *
		 * @expectedException PHPUnit_Framework_Error_Notice
		 */
		public function testCreateException2() {
			SuperFixture::load($this, 'FooBar');
			$BakeSuperFixture = new BakeSuperFixture();

			// Au lieu de 'name' => array('foreignkey' => $SuperFixtureBar->getName())
			$SuperFixtureBaz = new BSFObject('SuperFixtureFoo', array('super_fixture_bar_id' => 'Test_123'));
			$BakeSuperFixture->create(array($SuperFixtureBaz));
		}

		/**
		 * Test de la fonction BakeSuperFixture::create
		 * On a fait une erreur dans la synthaxe de fields
		 *
		 * Message spécial value
		 *
		 * @expectedException PHPUnit_Framework_Error_Notice
		 */
		public function testCreateException3() {
			SuperFixture::load($this, 'FooBar');
			$BakeSuperFixture = new BakeSuperFixture();
			$SuperFixtureBaz = new BSFObject('SuperFixtureBaz', array('name' => 'Test')); // Au lieu de 'name' => array('value' => 'Test')
			$BakeSuperFixture->create(array($SuperFixtureBaz));
		}

		/**
		 * Test de la fonction BakeSuperFixture::create
		 * On a fait une erreur dans la synthaxe de fields
		 *
		 * @expectedException PHPUnit_Framework_Error_Notice
		 */
		public function testCreateException4() {
			SuperFixture::load($this, 'FooBar');
			$BakeSuperFixture = new BakeSuperFixture();
			$SuperFixtureBaz = new BSFObject('SuperFixtureBaz');
			$SuperFixtureBaz->fields = new BSFObject(); // On envoi quelque chose d'inatendu
			$BakeSuperFixture->create(array($SuperFixtureBaz));
		}

		/**
		 * Test de la fonction BakeSuperFixture::create
		 * On a fait une erreur dans la synthaxe de fields
		 *
		 * @expectedException PHPUnit_Framework_Error_Notice
		 */
		public function testCreateException5() {
			SuperFixture::load($this, 'FooBar');
			$BakeSuperFixture = new BakeSuperFixture();
			$SuperFixtureBaz = new BSFObject('SuperFixtureBaz', array('name' => new BSFObject()));  // On envoi quelque chose d'inatendu
			$BakeSuperFixture->create(array($SuperFixtureBaz));
		}

		/**
		 * Test de la fonction BakeSuperFixture::create
		 * On a fait une erreur dans la synthaxe de fields
		 *
		 * @expectedException PHPUnit_Framework_Error_Notice
		 */
		public function testCreateException6() {
			SuperFixture::load($this, 'FooBar');
			$BakeSuperFixture = new BakeSuperFixture();
			$SuperFixtureBaz = new BSFObject('SuperFixtureBaz', array('un_champ_qui_nexiste_pas' => array('value' => '1')));
			$BakeSuperFixture->create(array($SuperFixtureBaz));
		}

		/**
		 * Test de la fonction BakeSuperFixture::create
		 * On a fait une erreur dans la synthaxe de date
		 *
		 * @expectedException PDOException
		 */
		public function testCreateException7() {
			SuperFixture::load($this, 'FooBar');
			$BakeSuperFixture = new BakeSuperFixture();
			$SuperFixtureBaz = new BSFObject('SuperFixtureFoo', array('date_field' => array('value' => '1')));
			$BakeSuperFixture->create(array($SuperFixtureBaz), true);
		}

		/**
		 * Test de la fonction BakeSuperFixture::create
		 * On a fait une erreur dans la synthaxe de date
		 *
		 * @expectedException PDOException
		 */
		public function testCreateException8() {
			SuperFixture::load($this, 'FooBar');
			$BakeSuperFixture = new BakeSuperFixture();
			$SuperFixtureBaz = new BSFObject('SuperFixtureFoo', array('date_field' => array('value' => '2015-02-31')));
			$BakeSuperFixture->create(array($SuperFixtureBaz), true);
		}

		/**
		 * Test de la fonction BakeSuperFixture::create
		 * On a fait une erreur dans la synthaxe de date
		 *
		 * @expectedException PHPUnit_Framework_Error_Notice
		 */
		public function testCreateException9() {
			$Model = ClassRegistry::init('SuperFixtureFoo');
			$Model->validate = array(
				'name' => 'email'
			);
			SuperFixture::load($this, 'FooBar');
			$BakeSuperFixture = new BakeSuperFixture();
			$BakeSuperFixture->saveParams['validate'] = true;
			$SuperFixtureBaz = new BSFObject('SuperFixtureFoo', array('name' => array('value' => 'test')));
			$BakeSuperFixture->create(array($SuperFixtureBaz), true);
		}

		/**
		 * Obtien la liste des champs d'un Model
		 *
		 * @param Model $Model
		 * @return array
		 */
		protected function _getFields(Model $Model) {
			$results = array();
			foreach (array_keys($Model->schema()) as $fieldName) {
				$results[] = $Model->alias.'.'.$fieldName;
			}
			return $results;
		}
	}
?>