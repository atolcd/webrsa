<?php
	/**
	 * Code source de la classe Requestmanager.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Requestmanager ...
	 *
	 * @package app.Model
	 */
	class Requestmanager extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Requestmanager';

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
		 * Possède des clefs étrangères vers d'autres models
		 * @var array
		 */
        public $belongsTo = array(
			'Requestgroup' => array(
				'className' => 'Requestgroup',
				'foreignKey' => 'requestgroup_id',
			)
		);

		/**
		 * Vérifie la présence d'un Modele dans un enregistrement de Requestmanager
		 *
		 * @param array $result Résultat d'une query
		 * @param string $modelName Nom du modele recherché
		 * @return boolean Présent ou pas
		 */
		public function modelPresence( $result, $modelName ) {
			if ( Hash::get($result, 'Requestmanager.model') === $modelName ) {
				return true;
			}

			$json = json_decode(Hash::get($result, 'Requestmanager.json'), true);
			if ( !Hash::get((array)$json, 'joins') ) {
				return false;
			}

			foreach( (array)Hash::get($json, 'joins') as $jointure ) {
				if ( Hash::get($jointure, 'alias') === $modelName ) {
					return true;
				}
			}

			return false;
		}
	}
?>