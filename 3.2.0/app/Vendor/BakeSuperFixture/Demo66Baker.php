<?php
	/**
	 * Demo66Baker file
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('BSFObject', 'SuperFixture.Utility');
	App::uses('BakeSuperFixtureInterface', 'SuperFixture.Interface');
	require_once 'DossierBaker.php';

	/**
	 * Generateur de SuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class Demo66Baker extends DossierBaker implements BakeSuperFixtureInterface
	{
		/**
		 * Callback entre initialize et getData
		 */
		public function beforeGetData() {
			$datas = array_merge(
				parent::beforeGetData(),
				$this->getZonesgeographiques()
			);
			
			return $datas;
		}
		
		/**
		 * Callback après getData
		 * @params array BSFOject
		 */
		public function afterGetData(array $datas) {
			$zonegeo = self::findFirst('Zonegeographique', $datas);
			foreach (self::find('Adresse', $datas) as $adresse) {
				$canton = $this->getAdresseCanton($adresse, $zonegeo);
				$datas[] = $canton;
				$datas[] = $this->getAdresseAdresseCanton($adresse, $canton);
			}
			
			$origines = $this->getOriginespdos();
			$types = $this->getTypespdos();
			$poles = $this->getPolesdossierspcgs66(current($origines), current($types));
			
			$datas = array_merge(
				$origines,
				$types,
				$poles,
				parent::afterGetData($datas) // Ajoute Orientstruct
			);
			
			foreach (self::find('User', $datas) as $user) {
				$user->fields['isgestionnaire']['value'] = 'O';
				$user->fields['poledossierpcg66_id']['foreignkey'] = current($poles)->getName();
			}
			
			return $datas;
		}
		
		/**
		 * @param BSFObject $adresse
		 * @param BSFObject $zonegeo
		 * @return \BSFObject
		 */
		public function getAdresseCanton(BSFObject $adresse, BSFObject $zonegeo) {
			return new BSFObject('Canton', array(
				'nomvoie' => array('value' => $adresse->fields['nomvoie']['value']),
				'codepos' => array('value' => $adresse->fields['codepos']['value']),
				'canton' => array('value' => 'Canton de test'),
				'zonegeographique_id' => array('foreignkey' => $zonegeo->getName()),
				'libtypevoie' => array('value' => $adresse->fields['libtypevoie']['value']),
				'nomcom' => array('value' => $adresse->fields['nomcom']['value']),
				'numcom' => array('value' => $adresse->fields['numcom']['value']),
			));
		}
		
		/**
		 * @param BSFObject $adresse
		 * @param BSFObject $canton
		 * @return \BSFObject
		 */
		public function getAdresseAdresseCanton(BSFObject $adresse, BSFObject $canton) {
			return new BSFObject('AdresseCanton', array(
				'adresse_id' => array('foreignkey' => $adresse->getName()),
				'canton_id' => array('foreignkey' => $canton->getName()),
			));
		}
		
		/**
		 * @return array BSFObject
		 */
		public function getZonesgeographiques() {
			return array(
				new BSFObject('Zonegeographique', array(
					'codeinsee' => array('value' => '00001'),
					'libelle' => array('value' => 'Zone de test étendue')
				))
			);
		}
		
		/**
		 * @return array BSFObject
		 */
		public function getPolesdossierspcgs66(BSFObject $originepdo, BSFObject $typepdo) {
			return array(
				new BSFObject('Poledossierpcg66', array(
					'name' => array('value' => 'Pôle PCG Test'),
					'isactif' => array('value' => '1'),
					'originepdo_id' => array('foreignkey' => $originepdo->getName()),
					'typepdo_id' => array('foreignkey' => $typepdo->getName()),
				))
			);
		}
		
		/**
		 * @return array BSFObject
		 */
		public function getOriginespdos() {
			return array(
				new BSFObject('Originepdo', array(
					'libelle' => array('value' => 'Origine Test'),
					'originepcg' => array('value' => 'O'),
					'cerparticulier' => array('value' => 'O'),
				))
			);
		}
		
		/**
		 * @return array BSFObject
		 */
		public function getTypespdos() {
			return array(
				new BSFObject('Typepdo', array(
					'libelle' => array('value' => 'Type Test'),
					'originepcg' => array('value' => 'O'),
					'cerparticulier' => array('value' => 'O'),
				))
			);
		}
	}