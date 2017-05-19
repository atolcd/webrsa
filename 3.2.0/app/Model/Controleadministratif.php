<?php
	/**
	 * Code source de la classe Controleadministratif.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Controleadministratif ...
	 *
	 * @package app.Model
	 */
	class Controleadministratif extends AppModel
	{
		public $name = 'Controleadministratif';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
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
			'famcibcontro' => array('01', '02', '03', '04', '05', '06', '07'),
            'natcibcontro' => array('RSA', 'AUR', 'SIT'),
            'commacontro' => array('CAF', 'CGA', 'NAT', 'API', 'DEM', 'RMI'),
            'typecontro' => array('AG', 'EE', 'PI'),
            'typeimpaccontro' => array('0', '1', '2'),
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