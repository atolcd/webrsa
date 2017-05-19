<?php
	/**
	 * DossierElementBaker
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('BSFObject', 'SuperFixture.Utility');

	/**
	 * Classe DossierElementBaker, permet d'obtenir un dossier générique
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class DossierElementBaker {
		/**
		 * Renvoi un BSFObject pour un dossier générique
		 * 
		 * @return \BSFObject
		 */
		public function get() {
			return new BSFObject('Dossier',
				array(
					'numdemrsa' => array('auto' => true, 'unique' => true, 'faker' => array('rule' => 'regexify', '[0-9]{11}')),
					'matricule' => array('auto' => true, 'unique' => true, 'faker' => array('rule' => 'regexify', '[0-9]{15}')),
					'dtdemrsa' => array('auto' => true, 'minYear' => 2009),
					'fonorg' => array('auto' => true, 'in_array' => array('CAF', 'MSA')),
					'fonorgcedmut' => array('auto' => true, 'in_array' => array('CAF', 'MSA')),
					'fonorgprenmut' => array('auto' => true, 'in_array' => array('CAF', 'MSA')),
					'statudemrsa' => array('auto' => true, 'in_array' => array('N', 'C', 'A', 'M', 'S')),
				)
			);
		}
	}
