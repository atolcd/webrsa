<?php
	/**
	 * Code source de la classe SuperFixture.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	App::uses('FakerManager', 'SuperFixture.Utility');
	
	/**
	 * La classe BSFObject (Bake Super Fixture Object) permet la créations des composants
	 * pour la génération de SuperFixtures
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class BSFObject
	{
		/**
		 * Nombre de fois que l'objet doit effectuer une création
		 * 
		 * @var integer
		 */
		public $runs = 1;
		
		/**
		 * Nom du modèle sur lequel se base l'objet
		 * 
		 * @var String
		 */
		public $modelName;
		
		/**
		 * Liste des champs et leurs attributs
		 * 
		 * @var array
		 */
		public $fields = array();
		
		/**
		 * Liste de BSFObject sur lesquels faire une jointure
		 * 
		 * @var array BSFObject
		 */
		public $contain = array();
		
		/**
		 * Identifiant unique de l'objet
		 * 
		 * @var String
		 */
		protected $_name;
		
		/**
		 * Foreign keys
		 * ex: array('Monautreobjet_45897366' => 1, 'Monautreobjet_869431330' => 2)
		 * 
		 * @var array
		 */
		protected $_foreignkey = array();

		/**
		 * Constructeur de classe, defini le modele sur lequel se base l'objet
		 * 
		 * @param string $modelName
		 * @param array $fields
		 * @param array $contain
		 */
		public function __construct($modelName = null, array $fields = array(), array $contain = array()) {
			$Faker = FakerManager::getInstance('BSFObject.'.$modelName);
			
			$this->modelName = $modelName;
			$this->_name = $modelName.'_'.$Faker->unique()->randomNumber();
			$this->fields = $fields;
			$this->contain = $contain;
		}
		
		/**
		 * Permet d'obtenir l'identifiant de l'objet
		 * 
		 * @return array
		 */
		public function getName() {
			return $this->_name;
		}
		
		/**
		 * Permet d'obtenir les foreignkeys de l'objet
		 * 
		 * @return String
		 */
		public function getForeignkey() {
			return $this->_foreignkey;
		}
		
		/**
		 * Setter dynamique
		 * Si on fait appel à setMaVar(5) ou set_maVar(5), l'attribut de classe maVar = 5
		 * 
		 * @param String $name
		 * @param array $arguments
		 * @return \BSFObject
		 */
		public function __call($name, $arguments) {
			if (!empty($arguments) && preg_match('/^set([\w]+)$/', $name, $matches)) {
				$varName = trim(strtolower(substr($name, 3, 1)).substr($name, 4), '_');
				$this->{$varName} = $arguments[0];
				return $this;
			} else {
				trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);
			}
		}
	}