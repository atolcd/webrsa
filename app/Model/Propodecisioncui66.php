<?php
	/**
	 * Code source de la classe Propodecisioncui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Propodecisioncui66 ...
	 *
	 * @package app.Model
	 */
	class Propodecisioncui66 extends AppModel
	{
		public $name = 'Propodecisioncui66';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $belongsTo = array(
			'Cui' => array(
				'className' => 'Cui',
				'foreignKey' => 'cui_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'cui_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Propodecisioncui66\'',
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
		* Chemin relatif pour les modèles de documents .odt utilisés lors des
		* impressions. Utiliser %s pour remplacer par l'alias.
		*/
//		public $modelesOdt = array(
//			'CUI/notifelucui.odt',
//		);


		/**
		 *
		 * @param integer $id
		 * @param integer $user_id
		 * @return array
		 */
		public function getDataForPdf( $id, $user_id ) {
			$propodecisioncui = $this->find(
				'first',
				array(
					'fields' => array_merge(
						$this->fields(),
						$this->Cui->fields(),
						$this->Cui->Personne->fields(),
						$this->Cui->Referent->fields(),
						$this->Cui->Structurereferente->fields(),
						$this->Cui->Personne->Foyer->fields(),
						$this->Cui->Personne->Foyer->Dossier->fields(),
						$this->Cui->Personne->Foyer->Adressefoyer->Adresse->fields()
					),
					'joins' => array(
						$this->join( 'Cui', array( 'type' => 'INNER' ) ),
						$this->Cui->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Cui->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$this->Cui->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
						$this->Cui->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Cui->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Cui->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Cui->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					),
					'conditions' => array(
						'Propodecisioncui66.id' => $id,
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Cui->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					),
					'contain' => false
				)
			);

			$user = $this->User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => false
				)
			);
			$propodecisioncui = Set::merge( $propodecisioncui, $user );

			return $propodecisioncui;
		}

		/**
		 * Retourne le PDF de notification du CUI.
		 *
		 * @param integer $id L'id du CUI pour lequel on veut générer l'impression
		 * @param integer $user_id L'id de l'utilisateur générant l'impression
		 * @return string
		 */
		public function getNotifelucuiPdf( $id, $user_id ) {

			$propodecisioncui = $this->getDataForPdf( $id, $user_id );

			///Traduction pour les données de la Personne/Contact/Partenaire/Référent
			$Option = ClassRegistry::init( 'Option' );
			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Referent' => array(
					'qual' => $Option->qual()
				)
			);

// debug( $propodecisioncui );
// die();
			return $this->ged(
				$propodecisioncui,
				$modelesOdt,
				false,
				$options
			);
		}

		/**
		 * Retourne l'id de la personne à laquelle est lié un enregistrement.
		 *
		 * @param integer $id L'id de l'enregistrement
		 * @return integer
		 */
		public function personneId( $id ) {
			$querydata = array(
				'fields' => array( "Cui.personne_id" ),
				'joins' => array(
					$this->join( 'Cui', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					"{$this->alias}.id" => $id
				),
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				return $result['Cui']['personne_id'];
			}
			else {
				return null;
			}
		}


        /**
         * Sous-requête permettant de savoir si une entrée existe dans la table proposdecisionscuis66 pour une entrée d'une
         * table d'un autre modèle.
         *
         * @param Model $Model
         * @param string $fieldName Si null, renvoit uniquement la sous-requête,
         * 	sinon renvoit la sous-requête aliasée pour un champ (avec l'alias du
         * 	modèle).
         * @param string $modelAlias Si null, utilise l'alias de la class Propodecisioncui66, sinon la valeur donnée.
         * @return string
         */
        public function sqNbPropositions( Model $Model, $fieldName = null, $modelAlias = null ) {
            $alias = Inflector::underscore( $this->alias );

            $modelAlias = ( is_null( $modelAlias ) ? $this->alias : $modelAlias );

            $sq = $this->sq(
                array(
                    'fields' => array(
                        "COUNT( {$alias}.id )"
                    ),
                    'alias' => $alias,
                    'conditions' => array(
                        "{$alias}.cui_id = {$Model->alias}.{$Model->primaryKey}"
                    )
                )
            );

            if( !is_null( $fieldName ) ) {
                $sq = "( {$sq} ) AS \"{$modelAlias}__{$fieldName}\"";
            }

            return $sq;
        }



		/**
		 * Retourne le PDF de l'impression de l'avis technique du CUI.
		 *
		 * @param integer $id L'id du CUI pour lequel on veut générer l'impression
		 * @param integer $user_id L'id de l'utilisateur générant l'impression
		 * @return string
		 */
		public function getAvistechniquecuiPdf( $id, $user_id ) {

			$propodecisioncui = $this->getDataForPdf( $id, $user_id );

			///Traduction pour les données de la Personne/Contact/Partenaire/Référent
			$Option = ClassRegistry::init( 'Option' );
			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Referent' => array(
					'qual' => $Option->qual()
				)
			);

			return $this->ged(
				$propodecisioncui,
				'CUI/avistechniquecui.odt',
				false,
				$options
			);
		}
	}
?>