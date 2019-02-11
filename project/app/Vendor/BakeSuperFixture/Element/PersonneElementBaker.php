<?php
	/**
	 * PersonneElementBaker
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('BSFObject', 'SuperFixture.Utility');
	App::uses('FakerManager', 'SuperFixture.Utility');

	/**
	 * Classe PersonneElementBaker, permet d'obtenir un element générique
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class PersonneElementBaker {
		/**
		 * Permet d'obtenir le nom de la personne générée
		 * 
		 * @var String
		 */
		public $nom = null;
		
		/**
		 * Renvoi un BSFObject pour un element générique
		 * 
		 * @param boolean $adulte - defini la date de naissance
		 * @param boolean $male - homme si true, femme si false, automatique si null
		 * @return \BSFObject
		 */
		public function get($adulte = true, $male = null) {
			$Faker = FakerManager::getInstance();
			$Personne = new BSFObject('Personne');
			
			// NIR
			$departementA = '(0[1-9]|1[0-9]|2[1-9]|[3-9][0-9]|2[AB])'; // 01 à 19 | 21 à 95 | 2A | 2B
			$codeCommuneA = '(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990)'; // 001 à 990
			$casA = $departementA.$codeCommuneA;
			$ordreNaissance = '(00[1-9]|0[1-9][0-9]|[1-9][0-9]{2})'; // 001 à 999
			
			$this->nom = $this->nom ?: $Faker->lastName;
			$dtnai = $adulte ? $this->_getDtnai($Faker, $min = 1970, $max = 1995) : $this->_getDtnai($Faker, $min = 1998, $max = 2016);
			$male = $male === null ? $Faker->randomDigit >= 5 : $male;
			$nir = ($male ? '1' : '2').substr($dtnai, 2,2).substr($dtnai, 5,2).$casA.$ordreNaissance;
			
			$nirValue = $Faker->unique()->regexify($nir);
			$cleNir = cle_nir($nirValue);
			
			$prenom = $Faker->firstName($male ? 'male' : 'female');
			
			$Personne->fields = array(
				'qual' => array('value' => $male ? 'MR' : 'MME'),
				'nom' => array('value' => $this->nom),
				'nomnai' => array('value' => $this->nom),
				'prenom' => array('value' => $prenom),
				'nomcomnai' => array('auto' => true, 'faker' => 'city'),
				'dtnai' => array('value' => $dtnai),
				'nir' => array('value' => $nirValue.$cleNir),
			);
			
			if (!$male && $adulte) {
				$Personne->fields['nomnai'] = array('auto' => true, 'faker' => array('rule' => 'lastName'));
			}
			
			return $Personne;
		}
		
		/**
		 * Génère une date de naissance dans une plage de dates données
		 * 
		 * @param Faker $Faker
		 * @param integer $min
		 * @param integer $max
		 * @return type
		 */
		protected static function _getDtnai($Faker, $min, $max) {
			if (!is_numeric($min) || !is_numeric($max) || $min>$max) {
				trigger_error("Une erreur est detecté dans le min/max du générateur de date de naissance !");
			}
			
			while (true) {
				preg_match('/^([0-9]{4}).*$/', $Faker->dateTime($max.'-12-30 00:00:00')->format('Y-m-d'), $matches);
				$year = (integer)$matches[1];
				if ($year >= $min) {
					break;
				}
			}
			
			return $matches[0];
		}
	}
