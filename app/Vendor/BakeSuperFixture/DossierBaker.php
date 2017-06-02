<?php
	/**
	 * DossierBaker file
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('BSFObject', 'SuperFixture.Utility');
	App::uses('BakeSuperFixtureInterface', 'SuperFixture.Interface');
	require_once 'WebrsaBaker.php';

	/**
	 * Generateur de SuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class DossierBaker extends WebrsaBaker implements BakeSuperFixtureInterface
	{
		/**
		 * Callback après getData
		 * @params array BSFOject
		 */
		public function afterGetData(array $datas) {
			foreach (self::find('Personne', $datas) as $personne) {
				foreach (self::find('Prestation', $personne->contain) as $prestation) {
					if ($prestation->fields['rolepers']['value'] !== 'DEM') {
						continue;
					}
					
					if (!isset($personne->contain)) {
						$personne->contain = array();
					}
					
					$personne->contain[] = $this->getPersonneOrientstruct();
					break;
				}
			}
			
			return parent::afterGetData($datas);
		}
		
		/**
		 * Permet de choisir un "typeorient" au hasard, avec une "structurereferente" et un "referent" correspondant
		 * Typeorient avec "parent_id" (Catégories)
		 * 
		 * @return array BSFObject
		 */
		protected function _choixTypeOrient() {
			$typeorients = array();
			foreach ($this->Typeorients as $Typeorient) {
				// Catégories
				if (!isset($Typeorient->fields['parentid']['value']) || $Typeorient->fields['parentid']['value'] !== null) {
					$typeorients[] = $Typeorient;
				}
			}
			
			$Typeorient = $this->Faker->randomElement($typeorients);
			$fk = $Typeorient->getName();
			
			foreach ($this->Structuresreferentes as $Structurereferente) {
				if ($Structurereferente->fields['typeorient_id']['foreignkey'] === $fk) {
					$fk = $Structurereferente->getName();
					break;
				}
			}
			
			foreach ($this->Referents as $Referent) {
				if ($Referent->fields['structurereferente_id']['foreignkey'] === $fk) {
					break;
				}
			}
			
			return compact('Typeorient', 'Structurereferente', 'Referent');
		}
		
		/**
		 * @return \BSFObject
		 */
		public function getPersonneOrientstruct() {
			$objs = $this->_choixTypeOrient();
			
			$orientstruct = new BSFObject('Orientstruct');
			$orientstruct->fields = array(
			   'typeorient_id' => array('auto' => true, 'foreignkey' => $objs['Typeorient']->getName()),
			   'structurereferente_id' => array('auto' => true, 'foreignkey' => $objs['Structurereferente']->getName()),
			   'date_valid' => array('auto' => true),
			   'statut_orient' => array('value' => 'Orienté'),
			   'referent_id' => array('auto' => true, 'foreignkey' => $objs['Referent']->getName()),
			   'etatorient' => array('value' => 'proposition'),
			   'rgorient' => array('value' => '1'),
			   'referentorientant_id' => array('auto' => true, 'foreignkey' => $objs['Referent']->getName()),
			   'structureorientante_id' => array('auto' => true, 'foreignkey' => $objs['Structurereferente']->getName()),
			   'user_id' => array('auto' => true, 'foreignkey' => $this->Faker->randomElement($this->Users)->getName()),
			   'haspiecejointe' => array('value' => '0'),
			   'origine' => array('value' => 'manuelle'),
			);
			
			return $orientstruct;
		}
		
		/**
		 * @param BSFObject $referent
		 * @param BSFObject $structurereferente
		 * @return \BSFObject
		 */
		public function getPersonnePersonneReferent(BSFObject &$referent, BSFObject &$structurereferente) {
			return new BSFObject('PersonneReferent', array(
				'referent_id' => array('foreignkey' => $referent->getName()),
				'dddesignation' => array('value' => '2016-01-01'),
				'structurereferente_id' => array('foreignkey' => $structurereferente->getName()),
			));
		}
		
		/**
		 * @param BSFObject $dossier
		 * @param string $rolepers
		 * @param boolean $enf
		 * @return array BSFObject
		 */
		public function getPersonneContain(\BSFObject &$dossier, $rolepers = 'DEM') {
			$contain = parent::getPersonneContain($dossier, $rolepers);
			
			if ($rolepers === 'DEM') {
				$objs = $this->_choixTypeOrient();
				$contain[] = $this->getPersonnePersonneReferent($objs['Referent'], $objs['Structurereferente']);
			}
			
			return $contain;
		}
	}