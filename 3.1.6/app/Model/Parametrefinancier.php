<?php	
	/**
	 * Code source de la classe Parametrefinancier.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Parametrefinancier ...
	 *
	 * @package app.Model
	 */
	class Parametrefinancier extends AppModel
	{
		public $name = 'Parametrefinancier';

		public $validate = array(
			'entitefi' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'tiers' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'codecdr' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'libellecdr' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'natureanalytique' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'lib_natureanalytique' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'programme' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'lib_programme' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'apreforfait' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'natureimput' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
		);
	}
?>