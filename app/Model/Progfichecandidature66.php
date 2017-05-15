<?php	
	/**
	 * Code source de la classe Progfichecandidature66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Progfichecandidature66 ...
	 *
	 * @package app.Model
	 */
	class Progfichecandidature66 extends AppModel
	{
		public $name = 'Progfichecandidature66';

		public $recursive = -1;

		public $actsAs = array(
			'Formattable',
			'Pgsqlcake.PgsqlAutovalidate'
		);

//		public $hasAndBelongsToMany = array(
//			'ActioncandidatPersonne' => array(
//				'className' => 'ActioncandidatPersonne',
//				'joinTable' => 'candidatures_progs66',
//				'foreignKey' => 'progfichecandidature66_id',
//				'associationForeignKey' => 'actioncandidat_personne_id',
//				'unique' => true,
//				'conditions' => '',
//				'fields' => '',
//				'order' => '',
//				'limit' => '',
//				'offset' => '',
//				'finderQuery' => '',
//				'deleteQuery' => '',
//				'insertQuery' => '',
//				'with' => 'CandidatureProg66'
//			)
//		);
        
        
        public $hasMany = array(
			'ActioncandidatPersonne' => array(
				'className' => 'ActioncandidatPersonne',
				'foreignKey' => 'progfichecandidature66_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Valprogfichecandidature66' => array(
				'className' => 'Valprogfichecandidature66',
				'foreignKey' => 'progfichecandidature66_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);
        
        /**
		 * Retourne une sous-requête permettant d'obtenir la liste des programmes
         * de type région saisis lors de la création de la fiche de candidature
         * Les éléments de la liste sont triés et préfixés par une
		 * chaîne de caractères.
		 *
		 * @param string $ActioncandidatPersonneId
		 * @param string $prefix
		 * @return string
		 */
		public function vfListeProgs( $ActioncandidatPersonneId = 'ActioncandidatPersonne.id', $prefix = '\\n\r-', $suffix = '' ) {
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
							$this->join( 'CandidatureProg66', array( 'type' => 'INNER' ) ),
							array(
								'CandidatureProg66' => 'candidatures_progs66',
								'Progfichecandidature66' => 'progsfichescandidatures66'
							)
						),
					),
					'conditions' => array(
						"candidatures_progs66.actioncandidat_personne_id = {$ActioncandidatPersonneId}"
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