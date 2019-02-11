<?php
	/**
	 * Fichier source du modèle Diplomecer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Diplomecer93.
	 *
	 * @package app.Model
	 */
	class Diplomecer93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Diplomecer93';

		/**
		 * Behaviors utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Liaisons "belongsTo" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Cer93' => array(
				'className' => 'Cer93',
				'foreignKey' => 'cer93_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Retourne une sous-requête permettant de récupérer le dernier diplôme
		 * lié à un CER, triée en fonction de l'année et de l'id.
		 *
		 * @param string $joinField Le champ cers93.id sur lequel faire la jointure
		 * @return string
		 */
		public function sqDernier( $joinField = 'Cer93.id' ) {
			$alias = Inflector::tableize( $this->alias );

			return $this->sq(
				array(
					'alias' => $alias,
					'fields' => array(
						$alias.'.'.$this->primaryKey
					),
					'conditions' => array(
						"{$alias}.cer93_id = {$joinField}"
					),
					'order' => array(
						$alias.'.annee' => 'DESC',
						$alias.'.'.$this->primaryKey => 'DESC'
					),
					'limit' => 1
				)
			);
		}
	}
?>