<?php
	/**
	 * Code source de la classe Aideapre66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Aideapre66 ...
	 *
	 * @package app.Model
	 */
	class Aideapre66 extends AppModel
	{
		public $name = 'Aideapre66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Apre66' => array(
				'className' => 'Apre66',
				'foreignKey' => 'apre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Themeapre66' => array(
				'className' => 'Themeapre66',
				'foreignKey' => 'themeapre66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typeaideapre66' => array(
				'className' => 'Typeaideapre66',
				'foreignKey' => 'typeaideapre66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasOne = array(
			'Fraisdeplacement66' => array(
				'className' => 'Fraisdeplacement66',
				'foreignKey' => 'aideapre66_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		public $hasAndBelongsToMany = array(
			'Pieceaide66' => array(
				'className' => 'Pieceaide66',
				'joinTable' => 'aidesapres66_piecesaides66',
				'foreignKey' => 'aideapre66_id',
				'associationForeignKey' => 'pieceaide66_id',
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
			'Piececomptable66' => array(
				'className' => 'Piececomptable66',
				'joinTable' => 'aidesapres66_piecescomptables66',
				'foreignKey' => 'aideapre66_id',
				'associationForeignKey' => 'piececomptable66_id',
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
			)
		);

		public $validate = array(
			'montantaide' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
				'plafondMontantAideapre' => array(
					'rule' => array( 'plafondMontantAideapre' ),
					'message' => 'Plafond dépassé'
				)
			),
			'montantpropose' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
				'plafondMontantAideapre' => array(
					'rule' => array( 'plafondMontantAideapre' ),
					'message' => 'Plafond dépassé'
				),
				'plafondMontantGlobalApre66' => array(
					'rule' => array( 'plafondMontantGlobalApre66' ),
					'message' => 'Le montant proposé va provoquer un dépassement du plafond ( %.2f €) autorisé sur l\'année %d. Montant maximal autorisé: %.2f €'
				),
				'plafondMontantAideapre66' => array(
					'rule' => array( 'plafondMontantAideapre66' ),
					'message' => 'Le montant proposé va provoquer un dépassement du plafond de l\'aide sélectionnée ( %.2f €) sur l\'année %d. Montant maximal autorisé: %.2f €'
				)
			),
			'montantaccorde' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
				'plafondMontantAideapre' => array(
					'rule' => array( 'plafondMontantAideapre' ),
					'message' => 'Plafond dépassé'
				),
				'plafondMontantGlobalApre66' => array(
					'rule' => array( 'plafondMontantGlobalApre66' ),
					'message' => 'Le montant proposé va provoquer un dépassement du plafond ( %.2f €) autorisé sur l\'année %d. Montant maximal autorisé: %.2f €'
				),
				'plafondMontantAideapre66' => array(
					'rule' => array( 'plafondMontantAideapre66' ),
					'message' => 'Le montant proposé va provoquer un dépassement du plafond de l\'aide sélectionnée ( %.2f €) sur l\'année %d. Montant maximal autorisé: %.2f €'
				)
			),
			'virement' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'versement' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'creancier' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'motivdem' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			)
		);

		/**
		* Vérification du montant demandé pour une aide APRE
		* Ce montant doit être inférieur au plafond de cette aide
		*
		* FIXME: signature + retour
		*
		* @param string $montantaide Value to check
		* @param integer $plafond Valeur à ne pas dépasser
		*
		* @return boolean Success
		* @access public
		*/
		public function plafondMontantAideapre( $check ) {
			$return = true;
			$typeaideapre66_id = Set::classicExtract( $this->data, 'Aideapre66.typeaideapre66_id' );
			if (!empty($typeaideapre66_id)) {
				$qd_typeaideapre66 = array(
					'conditions' => array(
						'Typeaideapre66.id' => $typeaideapre66_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$typeaideapre66 = $this->Typeaideapre66->find('first', $qd_typeaideapre66);

				$plafond = Hash::get($this->data, 'Apre66.isapre')
					? Hash::get($typeaideapre66, 'Typeaideapre66.plafond')
					: Hash::get($typeaideapre66, 'Typeaideapre66.plafondadre')
				;

				foreach( $check as $field => $value ) {
					$return = ( $value <= $plafond ) && $return;
				}
			}
			return $return;
		}

		/**
		*   Récupération du nombre de pièces liées aux types d'aides d'une APRE
		*/

		protected function _nbrNormalPieces() {
			$nbNormalPieces = array();

			$typeaideapre66_id = Set::classicExtract( $this->data, 'Aideapre66.typeaideapre66_id' );

			$qd_typeaide = array(
				'conditions' => array(
					'Typeaideapre66.id' => $typeaideapre66_id
				),
				'fields' => null,
				'order' => null,
				'contain' => array(
					'Pieceaide66'
				)
			);
			$typeaide = $this->Typeaideapre66->find('first', $qd_typeaide);


			$nbNormalPieces['Typeaideapre66'] = count( Set::extract( $typeaide, '/Pieceaide66/id' ) );
			return $nbNormalPieces;
		}

		/**
		*   Détails des APREs afin de récupérer les pièces liés à cette APRE ainsi que les aides complémentaires avec leurs pièces
		*   @param int $id
		*/

		public function _details( $aideapre66_id ) {
			$nbNormalPieces = $this->_nbrNormalPieces();
			$details['Piecepresente'] = array();
			$details['Piecemanquante'] = array();

			// Nombre de pièces trouvées par-rapport au nombre de pièces prévues - Apre
			$details['Piecepresente']['Typeaideapre66'] = $this->Aideapre66Pieceaide66->find( 'count', array( 'conditions' => array( 'aideapre66_id' => $aideapre66_id ) ) );

			$details['Piecemanquante']['Typeaideapre66'] = abs( $details['Piecepresente']['Typeaideapre66'] - $nbNormalPieces['Typeaideapre66'] );

			$piecesPresentes = array();
			// Quelles sont les pièces manquantes
			$piecesPresentes = Set::extract( $this->Aideapre66Pieceaide66->find( 'all', array( 'conditions' => array( 'aideapre66_id' => $aideapre66_id ) ) ), '/Aideapre66Pieceaide66/pieceaide66_id' );

			$typeaideapre66_id = Set::classicExtract( $this->data, 'Aideapre66.typeaideapre66_id' );
			$piecesParType = $this->Typeaideapre66->Pieceaide66Typeaideapre66->find(
				'list',
				array(
					'fields' => array( 'id', 'pieceaide66_id' ),
					'conditions' => array(
						'Pieceaide66Typeaideapre66.typeaideapre66_id' => $typeaideapre66_id
					)
				)
			);
			$piecesAbsentes = array_diff( $piecesParType, $piecesPresentes );

			return $details;
		}

		/**
		*
		*/
		public function validationDecisionAllowEmpty( $allowEmpty ){
			foreach( $this->validate['decisionapre'] as $i => $rule ){
				foreach( $rule as $key => $value ){
					if( is_array( $value ) ){
					 	foreach( $value as $inList ){
							if( $inList == 'inList' ){
								$this->validate['decisionapre'][$i]['allowEmpty'] = $allowEmpty;
							}
						}
					}
				}
			}
		}

		/**
		*
		*/

		public function afterSave( $created, $options = array() ) {
			parent::afterSave( $created, $options );
			$details = $this->_details( $this->id );

			$qd_aideapre = array(
				'conditions' => array(
					'Aideapre66.id' => $this->id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$aideapre = $this->find('first', $qd_aideapre);

			$decisionapre = Set::classicExtract( $aideapre, 'Aideapre66.decisionapre');

			if( !empty( $decisionapre ) ){
				$this->Apre66->updateAllUnBound(
					array(
						'"etatdossierapre"' =>  '\'VAL\'' ,
						'"isdecision"' =>  '\'O\'',
					),
					array(
						'"Apre66"."id"' => Set::classicExtract( $aideapre, 'Aideapre66.apre_id')
					)
				);
			}
		}



		/**
		* Vérification du montant accordé pour les APREs
		* Ce montant doit être inférieur au plafond défini en paramétrage (3000 € par défaut)
		*	sur l'année calendaire ( 01/01 au 31/12 )
		*
		* @param string $montantpropose Value to check
		* @param integer $plafond Valeur à ne pas dépasser
		*
		* @return boolean Success
		* @access public
		*/
		public function plafondMontantGlobalApre66( $check, $rule ) {

			$return = true;
			$plafondGlobal = Configure::read( 'Apre.montantMaxComplementaires' );
// 			$montantpropose = $check['montantpropose'];

			list( $year, $month, $day ) = explode( '-',  $this->data['Aideapre66']['datemontantpropose'] );
			$yearMax = $year + Configure::read( 'Apre.periodeMontantMaxComplementaires' ) - 1;

			$conditions = array(
				'Aideapre66.decisionapre' => 'ACC',
				"Aideapre66.datemontantpropose BETWEEN '{$year}-01-01' AND '{$yearMax}-12-31'"
			);

			if( isset( $this->data['Apre66']['personne_id'] ) ) {
				$conditions['Apre66.personne_id'] = $this->data['Apre66']['personne_id'];
			}
			else if( isset( $this->data['Aideapre66']['apre_id'] ) ) {
				$conditions[] = 'Apre66.personne_id IN ('
					.$this->Apre66->sq(
						array(
							'alias' => 'apres66',
							'fields' => array( 'apres66.personne_id' ),
							'contain' => false,
							'conditions' => array(
								'apres66.id' => $this->data['Aideapre66']['apre_id']
							)
						)
					)
				.')';
			}
			else {
				return false;//FIXME: message
			}

			$querydata = array(
				'fields' => array(
					'SUM("Aideapre66"."montantaccorde") AS "Aideapre66__montanttotal"'
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->join( 'Apre66', array( 'INNER JOIN' ) )
				),
				'contain' => false
			);

			$apres = $this->find( 'all', $querydata );
			$montanttotal = ( is_null( $apres[0]['Aideapre66']['montanttotal'] ) ? 0 : $apres[0]['Aideapre66']['montanttotal'] );

			foreach( $check as $field => $value ) {
				if( ( $montanttotal + $value ) <= $plafondGlobal   ) {
					return true;
				}
				return sprintf( $rule['message'], $plafondGlobal, $year, ( $plafondGlobal - $montanttotal ) );
			}

		}


		/**
		* 	Vérification du montant accordé pour un type d'aide dans les APREs
		* 	Pour une aide donnée, le montant accordé ne doit pas dépasser le plafond, même cumulé sur
		*	plusieurs demandes sur l'année calendaire ( 01/01 au 31/12 )
		*
		* @param string $montantpropose Value to check
		* @param integer $plafond Valeur à ne pas dépasser
		*
		* @return boolean Success
		* @access public
		*/
		public function plafondMontantAideapre66( $check, $rule ) {
			$return = true;
			$typeaideapre66_id = Set::classicExtract( $this->data, 'Aideapre66.typeaideapre66_id' );

			$typeaideapre66 = $this->Typeaideapre66->find(
				'first',
				array(
					'conditions' => array(
						'Typeaideapre66.id' => $typeaideapre66_id
					),
					'contain' => false
				)
			);
			$plafondAide = Hash::get($this->data, 'Apre66.isapre')
				? Hash::get($typeaideapre66, 'Typeaideapre66.plafond')
				: Hash::get($typeaideapre66, 'Typeaideapre66.plafondadre')
			;


			list( $year, $month, $day ) = explode( '-',  $this->data['Aideapre66']['datemontantpropose'] );
			$yearMax = $year + Configure::read( 'Apre.periodeMontantMaxComplementaires' ) - 1;

			$conditions = array(
				'Aideapre66.decisionapre' => 'ACC',
				"Aideapre66.datemontantpropose BETWEEN '{$year}-01-01' AND '{$yearMax}-12-31'",
				'Aideapre66.typeaideapre66_id' => $typeaideapre66_id
			);

			if( isset( $this->data['Apre66']['personne_id'] ) ) {
				$conditions['Apre66.personne_id'] = $this->data['Apre66']['personne_id'];
			}
			else if( isset( $this->data['Aideapre66']['apre_id'] ) ) {
				$conditions[] = 'Apre66.personne_id IN ('
					.$this->Apre66->sq(
						array(
							'alias' => 'apres66',
							'fields' => array( 'apres66.personne_id' ),
							'contain' => false,
							'conditions' => array(
								'apres66.id' => $this->data['Aideapre66']['apre_id']
							)
						)
					)
				.')';
			}
			else {
				return false;//FIXME: message
			}

			$querydata = array(
				'fields' => array(
					'SUM("Aideapre66"."montantaccorde") AS "Aideapre66__montanttotal"'
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->join( 'Apre66', array( 'INNER JOIN' ) )
				),
				'contain' => false
			);

			$apres = $this->find( 'all', $querydata );


			$montanttotal = ( is_null( $apres[0]['Aideapre66']['montanttotal'] ) ? 0 : $apres[0]['Aideapre66']['montanttotal'] );

			foreach( $check as $field => $value ) {
				if( ( $montanttotal + $value ) <= $plafondAide   ) {
					return true;
				}
				return sprintf( $rule['message'], $plafondAide, $year, ( $plafondAide - $montanttotal ) );
			}
		}



	}
?>