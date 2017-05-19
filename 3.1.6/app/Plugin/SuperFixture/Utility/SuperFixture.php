<?php
	/**
	 * Code source de la classe SuperFixture.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	
	App::uses('CakeFixtureManager', 'TestSuite/Fixture');
	
	/**
	 * La classe SuperFixtureManager permet d'étendre artificiellement CakeFixtureManager
	 */
	class SuperFixtureManager extends CakeFixtureManager
	{
		/**
		 * Garde en mémoire le FixtureManager d'origine
		 * 
		 * @var CakeFixtureManager
		 */
		protected $_oldFixtureManager;
		
		/**
		 * Clone le FixtureManager
		 * 
		 * @param CakeFixtureManager $CakeFixtureManager
		 */
		public function __construct(CakeFixtureManager $CakeFixtureManager) {
			foreach (get_class_vars(get_class($CakeFixtureManager)) as $key => $value) {
				$this->{$key} = $value;
			}
			
			if (empty($this->_oldFixtureManager)) {
				$this->_oldFixtureManager = $CakeFixtureManager;
			}
			
			$this->_initialized = false;
			$this->_initDb();
		}
		
		/**
		 * Permet d'ajouter/remplacer une fixture dans la liste du FixtureManager
		 * 
		 * @param String $name
		 */
		public function loadFixtureClass($name) {
			$parts = explode('.', $name);
			$fixtureName = end($parts);
			
			if ($parts[0] === 'plugin') {
				$pluginPath = CakePlugin::path(Inflector::camelize($parts[1])).'Test'.DS.'Fixture'.DS;
				App::build(array('Fixture' => array($pluginPath)));
			}
			
			$className = Inflector::camelize($fixtureName);
			$fixtureClass = $className . 'Fixture';
			
			App::uses($fixtureClass, 'Fixture');
			
			if (!class_exists($fixtureClass)) {
				throw new NotFoundException("La classe {$fixtureClass} n'existe pas!");
			}
			
			$this->_loaded[$fixtureName] = new $fixtureClass();
			$this->_fixtureMap[$fixtureClass] = $this->_loaded[$fixtureName];
		}
		
		/**
		 * Remplace les records existant dans une fixture et insert dans 
		 * la base de donnée les valeurs.
		 * 
		 * @param String $name
		 * @param array $records
		 */
		public function setFixtureRecords($name, array $records) {
			$parts = explode('.', $name);
			$fixtureName = end($parts);
			
			$className = Inflector::camelize($fixtureName);
			$fixtureClass = $className . 'Fixture';
			
			$this->_loaded[$fixtureName]->records = $records;
			$this->_fixtureMap[$fixtureClass] = $this->_loaded[$fixtureName];
			
			$this->loadSingle($fixtureName);
		}
		
		/**
		 * Delete the fixtures tables
		 *
		 * @param CakeTestCase $test the test to inspect for fixture unloading
		 * @return void
		 */
		public function unload(CakeTestCase $test) {
			// On détruit toutes les tables du test
			// ATTENTION: la méthode shutDown de la SuperFixture n'est jamais
			// appelée automatiquemnt par CakePHP à la fin de la classe de tests
			$this->shutDown();
			
			// On récupére les anciennes fixtures si besoin
			$this->_loaded = $this->_oldFixtureManager->_loaded;
			
			// On recréer les tables
			foreach ($this->_loaded as $fixture) {
				$fixture->created = array();
				$this->_setupTable($fixture);
			}
			
			// On s'assure qu'elles sont vides
			parent::unload($test);
		}
	}

	/**
	 * La classe SuperFixture permet le chargement de "Super Fixtures", des fixtures 
	 * regroupant les données nécésaire à un test.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	abstract class SuperFixture
	{	
		/**
		 * Permet de charger une super fixture
		 * 
		 * @param CakeTestCase $CakeTestCase Classe de test où charger les fixtures
		 * @param String $className Nom de la class de la super fixture à charger
		 */
		public static function load(CakeTestCase $CakeTestCase, $className) {
			// On vérifie que la classe de SuperFixture existe
			$fullClassName = $className.'SuperFixture';
			App::uses($fullClassName, 'SuperFixture');
			
			if (!class_exists($fullClassName)) {
				throw new NotFoundException("La classe {$fullClassName} n'existe pas!");
			}
			
			// On utilise SuperFixtureManager plutôt que CakeFixtureManager
			$Manager = $CakeTestCase->fixtureManager;
			if (!method_exists($Manager, 'unprocessed')) {
				$CakeTestCase->fixtureManager = new SuperFixtureManager($Manager);
			}
			
			// On charge les fixtures additionnelles si elles existent
			$additionnalFixtures = array();
			if (isset($fullClassName::$fixtures)) {
				$additionnalFixtures = $fullClassName::$fixtures;
			}
			
			// On ajoute les fixtures supplémentaire
			$data = $fullClassName::getData();
			foreach ($additionnalFixtures as $fixturePath) {
				$isDefined = false;
				$parts = explode('.', $fixturePath);
				$fixtureName = end($parts);
				
				// On cherche à savoir si la fixture additionnelle n'a pas été déja définie
				foreach (array_keys($data) as $SuperFixturePath) {
					$parts = explode('.', $SuperFixturePath);
					if (end($parts) === $fixtureName) {
						$isDefined = true;
						break;
					}
				}
				
				if (!$isDefined) {
					$data += array($fixturePath => array());
				}
			}
			
			// On charge les nouvelles valeurs dans les fixtures
			foreach ($data as $modelName => $records) {
				$CakeTestCase->fixtureManager->loadFixtureClass($modelName);
				$CakeTestCase->fixtureManager->setFixtureRecords($modelName, $records);
			}
		}
	}