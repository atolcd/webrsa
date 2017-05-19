<?php
	/**
	 * Dossierpcg66Baker file
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('BSFObject', 'SuperFixture.Utility');
	App::uses('BakeSuperFixtureInterface', 'SuperFixture.Interface');

	/**
	 * Generateur de SuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class Dossierpcg66Baker implements BakeSuperFixtureInterface {
		/**
		 * Permet d'obtenir les informations nécéssaire pour générer la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData() {
			/*
			 * Regex pour nir
			 */
			
			// Debut
			$sexe = '[12]';
			$anneeNaissance = '[0-9]{2}';
			$moisNaissance = '(0[0-9]|1[0-2])';
			$debut = $sexe.$anneeNaissance.$moisNaissance;
			
			// Cas A
			$departementA = '(0[1-9]|1[0-9]|2[1-9]|[3-9][0-9]|2[AB])'; // 01 à 19 | 21 à 95 | 2A | 2B
			$codeCommuneA = '(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990)'; // 001 à 990
			$casA = $departementA.$codeCommuneA;
			
			// Cas B (outre mer)
			$departementB = '9[78][0-9]'; // 790 à 978
			$codeCommuneB = '(0[1-9]|[1-8][0-9]|90)'; // 01 à 90
			$casB = $departementB.$codeCommuneB;
			
			// Cas C (etranger)
			$departementC = '99';
			$codePaysC = $codeCommuneA;
			$casC = $departementC.$codePaysC;
			
			$ordreNaissance = '(00[1-9]|0[1-9][0-9]|[1-9][0-9]{2})'; // 001 à 999
			$clefControle = '(0[1-9]|[1-8][0-9]|)'; // 01 à 97
			
			$nir13A = $debut.$casA.$ordreNaissance;
			
			$Dossier = new BSFObject('Dossier');
			$Dossier->fields = array(
				'numdemrsa' => array('auto' => true, 'faker' => array('rule' => 'regexify', '[0-9]{11}')),
				'matricule' => array('auto' => true, 'faker' => array('rule' => 'regexify', '[0-9]{15}')),
				'dtdemrsa' => array('auto' => true, 'minYear' => 2009),
				'fonorg' => array('auto' => true, 'in_array' => array('CAF', 'MSA')),
				'fonorgcedmut' => array('auto' => true, 'in_array' => array('CAF', 'MSA')),
				'fonorgprenmut' => array('auto' => true, 'in_array' => array('CAF', 'MSA')),
				'statudemrsa' => array('auto' => true, 'in_array' => array('N', 'C', 'A', 'M', 'S')),
			);
			
			$Foyer = new BSFObject('Foyer');
			$Foyer->fields = array(
				'dossier_id' => array('foreignkey' => $Dossier->getName()),
				'sitfam' => array('auto' => true, 'in_array' => array(
					'ABA', 'CEL', 'DIV', 'ISO', 'MAR', 'PAC', 'RPA', 'RVC', 'RVM', 'SEF', 'SEL', 'VEU', 'VIM'
				)),
				'typeocclog' => array('auto' => true, 'in_array' => array(
					'ACC', 'BAL', 'HCG', 'HCO', 'HGP', 'HOP', 'LOC', 'OLI', 'PRO', 'SRG', 'SRO'
				)),
				'haspiecejointe' => array('value' => 0),
			);
			
			$Personne1 = new BSFObject('Personne');
			$Personne1->fields = array(
				'qual' => array('value' => 'MR'),
				'nom' => array('auto' => true, 'faker' => 'lastName'),
				'prenom' => array('auto' => true, 'faker' => array('rule' => 'firstName', 'male')),
				'nomcomnai' => array('auto' => true, 'faker' => 'city'),
				'dtnai' => array('auto' => true, 'minYear' => 1970, 'maxYear' => 1995),
				'nir' => array('auto' => true, 'faker' => array('rule' => 'regexify', $nir13A)),
				'foyer_id' => array('foreignkey' => $Foyer->getName())
			);
			
			$Personne2 = new BSFObject('Personne');
			$Personne2->fields = array(
				'qual' => array('value' => 'MME'),
				'nom' => array('auto' => true, 'faker' => 'lastName'),
				'prenom' => array('auto' => true, 'faker' => array('rule' => 'firstName', 'female')),
				'nomcomnai' => array('auto' => true, 'faker' => 'city'),
				'dtnai' => array('auto' => true, 'minYear' => 1970, 'maxYear' => 1995),
				'nir' => array('auto' => true, 'faker' => array('rule' => 'regexify', $nir13A)),
				'foyer_id' => array('foreignkey' => $Foyer->getName())
			);
			
			$Prestation1 = new BSFObject('Prestation');
			$Prestation1->fields = array(
				'personne_id' => array('foreignkey' => $Personne1->getName()),
				'natprest' => array('value' => 'RSA'),
				'rolepers' => array('value' => 'DEM'),
			);
			
			$Prestation2 = new BSFObject('Prestation');
			$Prestation2->fields = array(
				'personne_id' => array('foreignkey' => $Personne2->getName()),
				'natprest' => array('value' => 'RSA'),
				'rolepers' => array('value' => 'CJT'),
			);
			
			
			// Paramêtrage
			$Typepdo = new BSFObject('Typepdo');
			
			$Dossierpcg66 = new BSFObject('Dossierpcg66');
			$Dossierpcg66->fields = array(
				'foyer_id' => array('foreignkey' => $Foyer->getName()),
				'typepdo_id' => array('foreignkey' => $Typepdo->getName()),
				'haspiecejointe' => array('value' => 0),
				'etatdossierpcg' => array('auto' => true, 'in_array' => array(
					'attaffect', 'attinstr', 'instrencours', 'attval', 'decisionvalid', 'decisionnonvalidretouravis', 
					'decisionvalidretouravis', 'transmisop', 'atttransmisop', 'annule', 'attinstrattpiece', 'attinstrdocarrive', 'arevoir'
				))
			);
			
			$Personnepcg66 = new BSFObject('Personnepcg66');
			$Personnepcg66->fields = array(
				'dossierpcg66_id' => array('foreignkey' => $Dossierpcg66->getName()),
				'personne_id' => array('foreignkey' => $Personne1->getName()),
			);
			
			
			$Foyer->contain = array($Personne1, $Personne2, $Dossierpcg66);
			$Dossier->contain = array(
				$Foyer,
				new BSFObject('Avispcgdroitrsa'),
				new BSFObject('Detaildroitrsa', array('topsansdomfixe' => array('auto' => true), 'topfoydrodevorsa' => array('auto' => true))),
				new BSFObject('Situationdossierrsa'),
			);
			$Personne1->contain = array($Prestation1);
			$Personne2->contain = array($Prestation2);
			$Dossierpcg66->contain = array(
				new BSFObject('Personnepcg66'),
			);
			
			$Dossier->runs = 10;
			$Dossierpcg66->runs = 3;
			
			return compact('Typepdo', 'Dossier');
		}
	}