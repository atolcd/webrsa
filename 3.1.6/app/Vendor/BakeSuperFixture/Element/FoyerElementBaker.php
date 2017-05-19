<?php
	/**
	 * FoyerElementBaker
	 *
	 * PHP 5.3
	 *
	 * @package SuperFixture
	 * @subpackage Test.Case.Utility.SuperFixture
	 */

	App::uses('BSFObject', 'SuperFixture.Utility');

	/**
	 * Classe FoyerElementBaker, permet d'obtenir un element générique
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class FoyerElementBaker {
		/**
		 * Renvoi un BSFObject pour un element générique
		 * 
		 * @return \BSFObject
		 */
		public function get() {
			return new BSFObject('Foyer',
				array(
					'sitfam' => array('auto' => true, 'in_array' => array(
						'ABA', 'CEL', 'DIV', 'ISO', 'MAR', 'PAC', 'RPA', 'RVC', 'RVM', 'SEF', 'SEL', 'VEU', 'VIM'
					)),
					'typeocclog' => array('auto' => true, 'in_array' => array(
						'ACC', 'BAL', 'HCG', 'HCO', 'HGP', 'HOP', 'LOC', 'OLI', 'PRO', 'SRG', 'SRO'
					)),
					'haspiecejointe' => array('value' => 0),
				)
			);
		}
	}
