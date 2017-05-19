<?php	
	/**
	 * Code source de la classe Creancealimentaire.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Creancealimentaire ...
	 *
	 * @package app.Model
	 */
	class Creancealimentaire extends AppModel
	{
		public $name = 'Creancealimentaire';

		protected $_modules = array( 'caf' );

		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
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
			'etatcrealim' => array(
				'SA', 'DD', 'AT', 'DS', 'SF', 'PS', 'DA', 'PE', 'DR',
				'RM', 'MS', 'SI', 'RE', 'TR', 'AA', 'AC'
			),
			'orioblalim' => array('CJT', 'PAR'),
			'motidiscrealim' => array('AVA', 'LOG', 'PAM', 'PHE', 'DCG', 'AUT'),
			'engproccrealim' => array('O', 'N', 'R'),
            'topdemdisproccrealim' => array('1', '0'),
            'topjugpa' => array('1', '0'),
            'verspa' => array('N', 'O', 'P'),
		);
	}
?>