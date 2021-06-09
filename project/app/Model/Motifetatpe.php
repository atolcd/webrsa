<?php
	/**
	 * Code source de la classe Motifetatpe.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	class Motifetatpe extends AppModel
	{
		public $name = 'Motifetatpe';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'motifsetatspe';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Has Many".
		 * @var array
		 */
		public $hasMany = array(
		);

		/**
		 * Retourne la liste des motifs paramétrés et activés
		 *
		 * @return array
		 */
		public function listOptions() {
			return $this->find('list', array(
				'fields' => array(
					'Motifetatpe.id',
					'Motifetatpe.lib_motif'
				),
				'conditions' => array(
					'Motifetatpe.actif' => 1
				)
			));
		}

	}
