<?php
	/**
	 * Code source de la classe Evenement.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Evenement ...
	 *
	 * @package app.Model
	 */
	class Evenement extends AppModel
	{
		public $name = 'Evenement';

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
			'fg' => array(
				'SUS', 'DESALL', 'SITPRO', 'INTGRO', 'ETACIV', 'SITENFAUT',
				'RESTRIRSA', 'SITFAM', 'DECDEMPCG', 'CARRSA', 'PROPCG',
				'HOSPLA', 'CIRMA', 'SUIRMA', 'RECPEN', 'TITPEN', 'REA',
				'DERPRE', 'ABANEURES', 'DEMRSA', 'CREALI', 'ASF', 'EXCPRE',
				'ADR', 'RAD', 'MUT', 'JUSRSAJEU', 'AIDFAM', 'ENTDED',
				'JUSACT', 'SURPONEXP'
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