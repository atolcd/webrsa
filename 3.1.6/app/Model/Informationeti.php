<?php
	/**
	 * Code source de la classe Informationeti.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Informationeti ...
	 *
	 * @package app.Model
	 */
	class Informationeti extends AppModel
	{
		public $name = 'Informationeti';

		protected $_modules = array( 'caf' );

		public $actsAs = array(
			'Allocatairelie',
		);

		public $validate = array(
			'mtbenagri' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'dtbenagri' => array(
				array(
					'rule' => 'date',
					'message' => 'Veuillez vérifier le format de la date.'
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'regfisagri' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			)
		);
		
		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 * 
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'acteti' => array('C', 'A', 'L', 'E'),
            'regfiseti' => array('R', 'S', 'M'),
            'regfisetia1' => array('R', 'S', 'M'),
			'topaccre' => array('1', '0'),
            'topbeneti' => array('1', '0'),
            'topcreaentre' => array('1', '0'),
            'topempl1ax' => array('1', '0'),
            'topevoreveti' => array('1', '0'),
            'topressevaeti' => array('1', '0'),
            'topsansempl' => array('1', '0'),
            'topstag1ax' => array('1', '0'),
		);

		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>
