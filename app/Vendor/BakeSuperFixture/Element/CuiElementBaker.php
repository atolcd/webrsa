<?php
	/**
	 * CuiElementBaker
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('BSFObject', 'SuperFixture.Utility');
	App::uses('FakerManager', 'SuperFixture.Utility');

	/**
	 * Classe CuiElementBaker, permet d'obtenir un element générique
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class CuiElementBaker {
		/**
		 * Renvoi un BSFObject pour un element générique
		 * 
		 * @return \BSFObject
		 */
		public function get() {
			$Faker = FakerManager::getInstance();
			
			// Permet de faire un lien cohérent entre la durée sans emploi et l'inscription à pole emploi
			$inscritpoleemploi = array();
			switch ($rolldice1 = $Faker->randomDigit) {
				case 0: case 1: 
					$sansemploidepui = '0_5'; $inscritpoleemploi[] = '0_5'; break;
				
				case 2:	$inscritpoleemploi[] = '0_5';
				case 3:	$inscritpoleemploi[] = '6_11'; $sansemploidepui = '6_11'; break;
				
				case 4:
					$inscritpoleemploi[] = '0_5';
					$inscritpoleemploi[] = '6_11';
				case 5:	$inscritpoleemploi[] = '12_23'; $sansemploidepui = '12_23'; break;
					
				case 6:	$inscritpoleemploi[] = '0_5';
				case 7:	$inscritpoleemploi[] = '6_11';
				case 8:	$inscritpoleemploi[] = '12_23';
				case 9: $inscritpoleemploi[] = '24_999'; $sansemploidepui = '24_999'; break;
			}
			
			$typecontrat = $Faker->randomDigit >= 5 ? 'CDD' : 'CDI';
			
			// On génère une date d'embauche
			$min = date('Y');
			$max = date('Y')+1;
			while (true) {
				preg_match('/^([0-9]{4})\-[0-9]{2}\-[0-9]{2}$/', $Faker->dateTime($max.'-12-30 00:00:00')->format('Y-m-d'), $matches);
				$year = (integer)$matches[1];
				if ($year >= $min) {
					break;
				}
			}
			$dateembauche = $matches[0];
			
			// On ajoute entre 3 et 24 mois pour la date de fin de contrat
			$date = new DateTime($dateembauche);
			$date->add(new DateInterval('P'.$Faker->numberBetween(3, 24).'M'));
			$findecontrat = $typecontrat === 'CDD' ? $date->format('Y-m-d') : '';
			
			$salairebrut = $Faker->numberBetween(500, 1500);
			$dureehebdo = $Faker->numberBetween(20, 39) * 60;
			
			$autre = $Faker->randomDigit >= 5 ? array(1, 2, 3) : array('');
			$autrecommentaire = count($autre) > 1 ? array('auto' => true) : array();
			$finpriseencharge = $date->format('Y-m-d');
			
			return new BSFObject('Cui',
				array(
					'secteurmarchand' => array('in_array' => array(0, 1)),
					'numconventionobjectif' => array('value' => Configure::read('Cui.Numeroconvention')),
					'niveauformation' => array('auto' => true, 'in_array' => array('00', '10', '20', '30', '40', '41', '50', '51', '60', '70')),
					'niveauqualif' => array('auto' => true, 'in_array' => array('00', '10', '20', '30', '40', '41', '50', '51', '60', '70')),
					'inscritpoleemploi' => array('auto' => true, 'in_array' => $inscritpoleemploi),
					'sansemploi' => array('value' => $sansemploidepui),
					'majorationrsa' => array('auto' => true, 'in_array' => array(0, 1)),
					'rsadepuis' => array('auto' => true, 'in_array' => array('0_5', '6_11', '12_23', '24_999')),
					'travailleurhandicape' => array('auto' => true, 'in_array' => array('', '', '', 1)), // 1 chance sur 4
					'typecontrat' => array('value' => $typecontrat),
					'dateembauche' => array('value' => $dateembauche),
					'findecontrat' => array('value' => $findecontrat),
					'salairebrut' => array('value' => $salairebrut),
					'dureehebdo' => array('value' => $dureehebdo),
					'modulation' => array('auto' => true, 'in_array' => array(0, 1)),
					'dureecollectivehebdo' => array('auto' => true, 'in_array' => array(35*60, 39*60)),
					'nomtuteur' => array('auto' => true, 'faker' => 'name'),
					'organismedesuivi' => array('auto' => true),
					'nomreferent' => array('auto' => true, 'faker' => 'name'),
					'actionaccompagnement' => array('auto' => true, 'in_array' => array(0, 1)),
					'remobilisationemploi' => array('auto' => true, 'in_array' => array('', '', '', 1, 2, 3)),
					'aidepriseposte' => array('auto' => true, 'in_array' => array('', '', '', 1, 2, 3)),
					'elaborationprojet' => array('auto' => true, 'in_array' => array('', '', '', 1, 2, 3)),
					'evaluationcompetences' => array('auto' => true, 'in_array' => array('', '', '', 1, 2, 3)),
					'aiderechercheemploi' => array('auto' => true, 'in_array' => array('', '', '', 1, 2, 3)),
					'autre' => array('auto' => true, 'in_array' => $autre),
					'autrecommentaire' => $autrecommentaire,
					'adaptationauposte' => array('auto' => true, 'in_array' => array('', '', '', 1, 2, 3)),
					'remiseaniveau' => array('auto' => true, 'in_array' => array('', '', '', 1, 2, 3)),
					'prequalification' => array('auto' => true, 'in_array' => array('', '', '', 1, 2, 3)),
					'acquisitioncompetences' => array('auto' => true, 'in_array' => array('', '', '', 1, 2, 3)),
					'formationqualifiante' => array('auto' => true, 'in_array' => array('', '', '', 1, 2, 3)),
					'formation' => array('auto' => true, 'in_array' => array('interne', 'externe')),
					'effetpriseencharge' => array('value' => $dateembauche),
					'decisionpriseencharge' => array('value' => $dateembauche),
					'finpriseencharge' => array('value' => $finpriseencharge),
					'dureehebdoretenu' => array('value' => $dureehebdo),
					'faitle' => array('value' => $dateembauche),
					'signaturele' => array('value' => $dateembauche),
					'haspiecejointe' => array('value' => 0),
					'beneficiaire_ass' => array('auto' => true, 'in_array' => array(0, 1)),
					'beneficiaire_aah' => array('auto' => true, 'in_array' => array(0, 1)),
					'beneficiaire_ata' => array('auto' => true, 'in_array' => array(0, 1)),
					'beneficiaire_rsa' => array('auto' => true, 'in_array' => array(0, 1)),
					'decision_cui' => array('auto' => true, 'in_array' => array('A', 'E', 'V', 'R')),
					'organismepayeur' => array('auto' => true, 'in_array' => array('CG', 'CAF', 'MSA', 'ASP', 'AUTRE')),
				)
			);
		}
	}
