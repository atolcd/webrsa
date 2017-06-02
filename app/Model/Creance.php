<?php
	/**
	 * Code source de la classe Creance.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Creance ...
	 *
	 * @package app.Model
	 */
	class Creance extends AppModel
	{
		public $name = 'Creance';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $validate = array(
			'foyer_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'motiindu' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'10', '11', '12', '20', '21', '30', '31', '32', '33',
							'34', '35', '40', '41', '42', '50', '51', '52', '60',
							'61', '62', '63', '64', '65', '70', '71', '72', '73',
							'74', '75', '76', '80', '81', '82', '90', '91', '92',
							'93', '94', '95', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF',
							'AG', 'AH', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH',
							'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'DD', 'DE', 'DF',
							'DG', 'DH', 'EE', 'EF', 'EG', 'EH', 'FF', 'FG', 'FH',
							'GG', 'GH', 'HH', 'K1', 'K2', 'K3', 'K4'
						)
					)
				)
			),
			'natcre' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'INK', 'ITK', 'INL', 'ITL', 'INM', 'ITM', 'INS', 'ITS', 'ISK', 'ISL', 'ISM', 'ISS'
						)
					)
				)
			),
			'oriindu' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'10', '20', '30', '40', '50', '55', '60', '61', '62', '63', '64', '65', '70', '71', '72', '73', '80',
						)
					)
				)
			),
			'respindu' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'12', '66', '67', '26', '24', '10', '65', '54', '62',
							'13', '64', '51', '52', '50', '61', '20', '41', '31',
							'40', '30', '22', '53', '15', '11', '74', '63', '32',
							'60', '25', '23', '21', '14',
						)
					)
				)
			),
		);

		public $belongsTo = array(
			'Foyer' => array(
				'className' => 'Foyer',
				'foreignKey' => 'foyer_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>