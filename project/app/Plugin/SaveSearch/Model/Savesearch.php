<?php
	/**
	 * Code source de la classe Savesearch.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Savesearch ...
	 *
	 * @package app.Model
	 */
	class Savesearch extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Savesearch';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

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
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'type' => 'INNER',
			),
			'Group' => array(
				'className' => 'Group',
				'foreignKey' => 'group_id',
				'type' => 'INNER',
			),
		);

		/**
		 * Permet d'obtenir les options pour un utilisateur est une action en particulier
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getAvailablesSearchs(array $conditions) {
			if (Hash::get($conditions, 'user_id') === false) {
				return array();
			}

			$results = $this->find('all',
				array(
					'conditions' => array(
						'Savesearch.controller' => strtolower(Hash::get($conditions, 'controller')),
						'Savesearch.action' => Hash::get($conditions, 'action'),
						'OR' => array(
							'Savesearch.user_id' => Hash::get($conditions, 'user_id'),
							array(
								'Savesearch.isforgroup' => 1,
								'Savesearch.group_id' => Hash::get($conditions, 'group_id'),
							)
						)
					)
				)
			);

			$options = array();
			foreach ($results as $result) {
				if (Hash::get($result, 'Savesearch.user_id') === Hash::get($conditions, 'user_id')) {
					$options['Sauvegardes personnelles'][Hash::get($result, 'Savesearch.id')] = Hash::get($result, 'Savesearch.name');
				} else {
					$options['Sauvegardes de groupe'][Hash::get($result, 'Savesearch.id')] = Hash::get($result, 'Savesearch.name');
				}
			}

			return $options;
		}
	}
?>