<?php
	/**
	 * Code source de la classe Derogation.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Derogation ...
	 *
	 * @package app.Model
	 */
	class Derogation extends AppModel
	{
		public $name = 'Derogation';

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
			'Allocatairelie' => array(
				'joins' => array( 'Avispcgpersonne' )
			),
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
			'typedero' => array('AGE', 'ACT', 'RES', 'NAT'),
			'avisdero' => array('D', 'O', 'N', 'A'),
		);

		public $belongsTo = array(
			'Avispcgpersonne' => array(
				'className' => 'Avispcgpersonne',
				'foreignKey' => 'avispcgpersonne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>