<?php
	/**
	 * Code source de la classe Pieceaide66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Pieceaide66 ...
	 *
	 * @package app.Model
	 */
	class Pieceaide66 extends AppModel
	{
		public $name = 'Pieceaide66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $order = 'Pieceaide66.name ASC';

		public $actsAs = array(
			'Occurences',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $hasAndBelongsToMany = array(
			'Aideapre66' => array(
				'className' => 'Aideapre66',
				'joinTable' => 'aidesapres66_piecesaides66',
				'foreignKey' => 'pieceaide66_id',
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
				'with' => 'Aideapre66Pieceaide66'
			),
			'Typeaideapre66' => array(
				'className' => 'Typeaideapre66',
				'joinTable' => 'piecesaides66_typesaidesapres66',
				'foreignKey' => 'pieceaide66_id',
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
				'with' => 'Pieceaide66Typeaideapre66'
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
		public function vfListePieces( $aideapre66Id = 'Aideapre66.id', $prefix = '\\n\r-' ) {
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
							$this->join( 'Aideapre66Pieceaide66', array( 'type' => 'INNER' ) ),
							array(
								'Aideapre66Pieceaide66' => 'aidesapres66_piecesaides66',
								'Pieceaide66' => 'piecesaides66'
							)
						),
					),
					'conditions' => array(
						"aidesapres66_piecesaides66.aideapre66_id = {$aideapre66Id}"
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
