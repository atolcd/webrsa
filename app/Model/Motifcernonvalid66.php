<?php
	/**
	 * Code source de la classe Motifcernonvalid66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Motifcernonvalid66 ...
	 *
	 * @package app.Model
	 */
	class Motifcernonvalid66 extends AppModel
	{
		public $name = 'Motifcernonvalid66';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $hasAndBelongsToMany = array(
			'Propodecisioncer66' => array(
				'className' => 'Propodecisioncer66',
				'joinTable' => 'motifscersnonvalids66_proposdecisionscers66',
				'foreignKey' => 'motifcernonvalid66_id',
				'associationForeignKey' => 'propodecisioncer66_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Motifcernonvalid66Propodecisioncer66'
			)
		);

		/**
		 * Retourne une sous-requête permettant d'obtenir la liste des motifs
         * de non validation de propositions de décisions d'un CER du 66.
         * Les éléments de la liste sont triés et préfixés par une
		 * chaîne de caractères.
		 *
		 * @param string $propodecisioncer66Id
		 * @param string $prefix
		 * @return string
		 */
		public function vfListeMotifs( $propodecisioncer66Id = 'Propodecisioncer66.id', $prefix = '\\n\r-', $suffix = '' ) {
			$alias = Inflector::tableize( $this->alias );

			$sq = $this->sq(
				array(
					'alias' => $alias,
					'fields' => array(
						"'{$prefix}' || \"{$alias}\".\"name\" || '{$suffix}' AS \"{$alias}__name\""
					),
					'contain' => false,
					'joins' => array(
						array_words_replace(
							$this->join( 'Motifcernonvalid66Propodecisioncer66', array( 'type' => 'INNER' ) ),
							array(
								'Motifcernonvalid66Propodecisioncer66' => 'motifscersnonvalids66_proposdecisionscers66',
								'Motifcernonvalid66' => 'motifscersnonvalids66'
							)
						),
					),
					'conditions' => array(
						"motifscersnonvalids66_proposdecisionscers66.propodecisioncer66_id = {$propodecisioncer66Id}"
					),
					'order' => array(
						"{$alias}.name ASC"
					)
				)
			);

			return "TRIM( TRAILING '{$suffix}' FROM ARRAY_TO_STRING( ARRAY( {$sq} ), '' ) )";
		}
	}
?>