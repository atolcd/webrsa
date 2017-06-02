<?php
	/**
	 * Code source de la classe Piecemodeletypecourrierpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Piecemodeletypecourrierpcg66 ...
	 *
	 * @package app.Model
	 */
	class Piecemodeletypecourrierpcg66 extends AppModel
	{
		public $name = 'Piecemodeletypecourrierpcg66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $order = 'Piecemodeletypecourrierpcg66.name ASC';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'name' => array(
				'checkUnique' => array(
					'rule' => array(
						'checkUnique',
						array( 'name', 'modeletypecourrierpcg66_id' )
					),
					'message' => 'Cet intitulé de pièce est déjà utilisé avec ce modèle de courrier.'
				)
			),
			'modeletypecourrierpcg66_id' => array(
				'checkUnique' => array(
					'rule' => array(
						'checkUnique',
						array( 'name', 'modeletypecourrierpcg66_id' )
					),
					'message' => 'Ce modèle de courrier est déjà utilisé avec cet intitulé de pièce.'
				)
			),
			'isautrepiece' => array(
				'usedValue' => array(
					'rule' => array( 'usedValue' ),
					'message' => 'Valeur déjà utilisée pour ce modèle de courrier.'
				)
			)
		);

		public $belongsTo = array(
			'Modeletypecourrierpcg66' => array(
				'className' => 'Modeletypecourrierpcg66',
				'foreignKey' => 'modeletypecourrierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasAndBelongsToMany = array(
			'Modeletraitementpcg66' => array(
				'className' => 'Modeletraitementpcg66',
				'joinTable' => 'mtpcgs66_pmtcpcgs66',
				'foreignKey' => 'piecemodeletypecourrierpcg66_id',
				'associationForeignKey' => 'modeletraitementpcg66_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Mtpcg66Pmtcpcg66'
			)
		);

		/**
		 *
		 */
		public function usedValue( $data ) {
			if( isset( $this->data[$this->alias]['modeletypecourrierpcg66_id'] ) && !empty( $this->data[$this->alias]['modeletypecourrierpcg66_id'] ) && isset( $this->data[$this->alias]['isautrepiece'] ) && ( $this->data[$this->alias]['isautrepiece'] == '1' ) ) {
				$querydata = array(
					'conditions' => array(
						"{$this->alias}.modeletypecourrierpcg66_id" => $this->data[$this->alias]['modeletypecourrierpcg66_id'],
						"{$this->alias}.isautrepiece" => $this->data[$this->alias]['isautrepiece']
					),
					'recursive' => -1,
					'contain' => false
				);

				if( isset( $this->data[$this->alias][$this->primaryKey] ) && !empty( $this->data[$this->alias][$this->primaryKey] ) ) {
					$querydata["{$this->alias}.{$this->primaryKey} <>"] = $this->data[$this->alias][$this->primaryKey];
				}

				return ( $this->find( 'count', $querydata ) == 0 );
			}

			return true;
		}

		/**
		 * Retourne une sous-requête permettant d'obtenir la liste des pièces liées
		 * au modèle de courrier des traitements PCGs 66.
		 * Les éléments de la liste sont triés et préfixés par une
		 * chaîne de caractères.
		 *
		 * @param string $aideapre66Id
		 * @param string $prefix
		 * @return string
		 */
		public function vfListeMotifs( $modeletraitementpcg66_id = 'Modeletraitementpcg66.id', $prefix = '\\n\r-' ) {
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
							$this->join( 'Mtpcg66Pmtcpcg66', array( 'type' => 'INNER' ) ),
							array(
								'Mtpcg66Pmtcpcg66' => 'mtpcgs66_pmtcpcgs66',
								'Piecemodeletypecourrierpcg66' => 'piecesmodelestypescourrierspcgs66'
							)
						),
					),
					'conditions' => array(
						"mtpcgs66_pmtcpcgs66.modeletraitementpcg66_id = {$modeletraitementpcg66_id}"
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
