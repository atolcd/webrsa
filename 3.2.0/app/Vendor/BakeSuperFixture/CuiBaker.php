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
	App::uses('BSFObjectUtility', 'SuperFixture.Utility');
	App::uses('BakeSuperFixtureInterface', 'SuperFixture.Interface');
	require_once 'DossierBaker.php';
	
	$requires = array(
		'Dossier', 'Foyer', 'Personne', 'Cui'
	);
	foreach ($requires as $require) {
		require_once 'Element'.DS.$require.'ElementBaker.php';
	}

	/**
	 * Generateur de SuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class CuiBaker extends DossierBaker implements BakeSuperFixtureInterface {
		/**
		 * Permet d'obtenir les informations nécéssaire pour générer la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData() {
			// Objets stockés
			$dossiers = array();
			$adresses = array();
			$globals = self::initializeGlobals();
			
			$runs = 10;
			
			for ($i=0; $i<$runs; $i++) {
				$data = self::completeDossier();
				$dossiers = array_merge($dossiers, $data['dossiers']);
				$adresses = array_merge($adresses, $data['adresses']);
				
				$dossier =& $dossiers[count($dossiers)-1];
				$foyer =& $dossier->contain[BSFObjectUtility::extractKey($dossier, 'Foyer')];
				$personne =& $foyer->contain[BSFObjectUtility::extractKey($foyer, 'Personne')];
				
				$Cui = new CuiElementBaker();
				$personne->contain[] = $Cui->get();
			}
			
			return array_merge($globals, $adresses, $dossiers);
		}
	}