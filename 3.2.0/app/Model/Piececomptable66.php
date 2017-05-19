<?php
	/**
	 * Code source de la classe Piececomptable66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Piececomptable66 ...
	 *
	 * @package app.Model
	 */
	class Piececomptable66 extends AppModel
	{
		public $name = 'Piececomptable66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $order = 'Piececomptable66.name ASC';

		public $actsAs = array(
			'Occurences',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $hasAndBelongsToMany = array(
			'Aideapre66' => array(
				'className' => 'Aideapre66',
				'joinTable' => 'aidesapres66_piecescomptables66',
				'foreignKey' => 'piececomptable66_id',
				'associationForeignKey' => 'aideapre66_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Aideapre66Piececomptable66'
			),
			'Typeaideapre66' => array(
				'className' => 'Typeaideapre66',
				'joinTable' => 'piecescomptables66_typesaidesapres66',
				'foreignKey' => 'piececomptable66_id',
				'associationForeignKey' => 'typeaideapre66_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Piececomptable66Typeaideapre66'
			)
		);

		/**
		 * Retourne une sous-requête permettant d'obtenir la liste des pièces liées
		 * à une aide APRE du 66. Les éléments de la liste sont triés et préfixés par une
		 * chaîne de caractères.
		 *
		 * @param string $aideapre66Id
		 * @param string $prefix
		 * @return string
		 */
		public function vfListePieces( $typeaideapre66_id = 'Aideapre66.typeaideapre66_id', $prefix = '\\n\r-' ) {
			$alias = Inflector::tableize( $this->alias );

			$sq = $this->sq(
				array(
					'alias' => $alias,
					'fields' => array(
						"'{$prefix}' || \"{$alias}\".\"name\" AS \"{$alias}__name\""
					),
					'contain' => false,
					'joins' => array(
						array_words_replace(
							$this->join( 'Piececomptable66Typeaideapre66', array( 'type' => 'INNER' ) ),
							array(
								'Piececomptable66Typeaideapre66' => 'piecescomptables66_typesaidesapres66',
								'Piececomptable66' => 'piecescomptables66'
							)
						),
					),
					'conditions' => array(
						"piecescomptables66_typesaidesapres66.piececomptable66_id = piecescomptables66.id",
						"piecescomptables66_typesaidesapres66.typeaideapre66_id = {$typeaideapre66_id}"
					),
					'order' => array(
						"{$alias}.name ASC"
					)
				)
			);

			return "ARRAY_TO_STRING( ARRAY( {$sq} ), '' )";
		}
	}
?>
