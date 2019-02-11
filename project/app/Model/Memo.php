<?php
	/**
	 * Code source de la classe Memo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Memo ...
	 *
	 * @package app.Model
	 */
	class Memo extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Memo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Tri par défaut pour ce modèle.
		 *
		 * @var array
		 */
		public $order = array( '%s.created DESC' );

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Allocatairelie',
			'Fichiermodulelie',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
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
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Memo\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);


        /**
		 * Sous-requête permettant de connaître le nombre de Mémos existants
         * pour un allocataire donné
		 *
		 * @param Model $Model
		 * @param string $fieldName Si null, renvoit uniquement la sous-reqête,
		 * 	sinon renvoit la sous-requête aliasée pour un champ (avec l'alias du
		 * 	modèle).
		 * @param string $modelAlias Si null, utilise l'alias de la class Memo, sinon la valeur donnée.
		 * @return string
		 */
		public function sqNbMemosLies( Model $Model, $fieldId = 'Personne.id', $fieldName = null, $modelAlias = null ) {
			$alias = Inflector::underscore( $this->alias );

			$modelAlias = ( is_null( $modelAlias ) ? $this->alias : $modelAlias );

			$sq = $this->sq(
					array(
						'fields' => array(
							"COUNT( {$alias}.id )"
						),
						'alias' => $alias,
						'conditions' => array(
							"{$alias}.personne_id = $fieldId"
						)
					)
			);

			if( !is_null( $fieldName ) ) {
				$sq = "( {$sq} ) AS \"{$modelAlias}__{$fieldName}\"";
			}

			return $sq;
		}

		/**
		 * Retourne une sous-requête permettant de trouver l'id du dernier mémo
		 * d'un allocataire.
		 *
		 * Le dernier étant entendu dans le sens de "dernière modification".
		 *
		 * @param string $personneIdFied
		 * @return string
		 */
		public function sqDernier( $personneIdFied = 'Personne.id' ) {
			return $this->sq(
				array(
					'fields' => array(
						'memos.id'
					),
					'alias' => 'memos',
					'conditions' => array(
						"memos.personne_id = {$personneIdFied}"
					),
					'order' => array( 'memos.modified DESC' ),
					'limit' => 1
				)
			);
		}
	}
?>
