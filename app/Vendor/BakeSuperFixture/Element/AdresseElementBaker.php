<?php
	/**
	 * AdresseElementBaker
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('BSFObject', 'SuperFixture.Utility');
	App::uses('FakerManager', 'SuperFixture.Utility');

	/**
	 * Classe AdresseElementBaker, permet d'obtenir un element générique
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class AdresseElementBaker
	{
		/**
		 * @var Faker
		 */
		public $Faker;
		
		/**
		 * Constructeur de classe
		 */
		public function __construct() {
			$this->Faker = FakerManager::getInstance();
		}
		
		/**
		 * Renvoi un BSFObject pour un element générique
		 * 
		 * @param string $dp Numéro de département
		 * @return \BSFObject
		 * @throws Exception
		 */
		public function get($dp = null) {
			if ($dp === null || !is_numeric($dp)) {
				$dp = $this->getDepartementNumber();
			} elseif (strlen($dp) > 3) {
				throw new Exception('Un département ne peux pas avoir un numéro supérieur à 3 caractères', 500);
			} elseif (strlen($dp) < 2) {
				throw new Exception('Un département ne peux pas avoir un numéro inferieur à 2 caractères', 500);
			}
			
			$streetName = $this->Faker->streetName;
			$matches = null;
			preg_match('/^([\w\-\']+) /', $streetName, $matches);
			
			return new BSFObject('Adresse', array(
				'numvoie' => array('value' => $this->Faker->regexify('[1-9][0-9]{0,2}')),
				'codepos' => array('value' => $this->Faker->regexify($dp.'[0-9]{'.(5 - strlen($dp)).'}')),
				'pays' => array('value' => 'FRA'),
				'numcom' => array(
					'value' => $this->Faker->regexify($dp.(strlen($dp) === 2 ? '0' : '').'([1-9][0-9]|[0-9][1-9])')
				),
				'nomcom' => array('value' => $this->Faker->city()),
				'nomvoie' => array('value' => strtoupper(substr($streetName, strlen($matches[0])))),
				'libtypevoie' => array('value' => strtoupper($matches[1])),
			));
		}
		
		/**
		 * Donne un numéro de département français au hasard
		 * 
		 * @return string
		 */
		protected function getDepartementNumber() {
			$numDep = array();
			
			for ($i=1; $i<=95; $i++) {
				$numDep[$i] = $i;
			}
			
			// Corse
			unset($numDep[20]);
			$numDep['2A'] = '2A';
			$numDep['2B'] = '2B';
			
			// DOM
			for ($i=971; $i<=976; $i++) {
				$numDep[$i] = $i;
			}
			
			return sprintf('%02d', $this->Faker->randomElement($numDep));
		}
	}
