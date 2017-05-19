<?php
	/**
	 * Code source de la classe Listesanctionep58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Listesanctionep58 ...
	 *
	 * @package app.Model
	 */
	class Listesanctionep58 extends AppModel
	{
		public $name = 'Listesanctionep58';

		public $displayField = 'sanction';

		public $actsAs = array(
			'Autovalidate2',
			'Formattable'
		);

		public $hasMany = array(
			'Decisionsanctionep58' => array(
				'className' => 'Decisionsanctionep58',
				'foreignKey' => 'listesanctionep58_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Decisionsanctionrendezvousep58' => array(
				'className' => 'Decisionsanctionrendezvousep58',
				'foreignKey' => 'listesanctionep58_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		public function checkValideListe() {
			$return = true;

			$sanctions = $this->find(
				'all',
				array(
					'order' => array( 'Listesanctionep58.rang ASC' )
				)
			);

			if ( !empty( $sanctions ) ) {
				$maxRang = 0;
				foreach( $sanctions as $sanction ) {
					if ( $sanction['Listesanctionep58']['rang'] != ( ++$maxRang ) ) {
						$return = false;
					}
				}
				if ( $maxRang != count( $sanctions ) ) {
					$return = false;
				}
			}

			return $return;
		}

		public function listOptions() {
			$listesanctions = $this->find( 'all' );

			$return = array();
			foreach( $listesanctions as $sanction ) {
				$return[$sanction['Listesanctionep58']['rang']] = $sanction['Listesanctionep58']['sanction'];
			}

			return $return;
		}

	}

?>