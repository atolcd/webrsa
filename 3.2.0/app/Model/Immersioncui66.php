<?php
	/**
	 * Fichier source de la classe Immersioncui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Immersioncui66 est la classe contenant les informations suplémentaire
	 * des adresses du CUI Pour le CG 66.
	 *
	 * @package app.Model
	 */
	class Immersioncui66 extends AppModel
	{
		public $name = 'Immersioncui66';

        public $belongsTo = array(
			'Immersionromev3' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'entreeromev3_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
        );

		public $hasOne = array(
			'Accompagnementcui66' => array(
				'className' => 'Accompagnementcui66',
				'foreignKey' => 'immersioncui66_id',
				'dependent' => true,
			),
		);

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);
	}
?>