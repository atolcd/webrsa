<?php
	/**
	 * Fichier source de la classe PersonneReferent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe PersonneReferent ...
	 *
	 * @package app.Model
	 */
	class PersonneReferent extends AppModel
	{
		public $name = 'PersonneReferent';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Allocatairelie',
			'Dependencies',
			'Fichiermodulelie',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Règles de validation.
		 *
		 * @var array
		 */
		public $validate = array(
			'referent_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
				'checkReferentUnique' => array(
					'rule' => array( 'checkReferentUnique' ),
					'message' => 'Le bénéficiaire possède déjà un référent unique, merci de le clôturer avant d\'en ajouter un nouveau',
					'allowEmpty' => true,
				),
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Referent', 'Structurereferente' ),
					'message' => 'Le référent n\'appartient pas à la structure référente',
				)
			),
			'dfdesignation' => array(
				'compareDates' => array(
					'rule' => array( 'compareDates', 'dddesignation', '>=' ),
					'message' => 'La date de fin de désignation doit être au moins la même que la date de début de désignation'
				)
			)
		);

		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'PersonneReferent\'',
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


		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Retourne une sous-requête permettant de connaître le dernier référent de parcours pour un
		 * allocataire donné.
		 *
		 * @param string $field Le champ Personne.id sur lequel faire la sous-requête
		 * @return string
		 */
		public function sqDerniere( $field, $cloture = null ) {
			$dbo = $this->getDataSource( $this->useDbConfig );
			$table = $dbo->fullTableName( $this, false, false );
			$conditionCloture = null;
			if( !is_null( $cloture ) ) {
				if( $cloture ) {
					$conditionCloture = "AND {$table}.dfdesignation IS NOT NULL";
				}
				else {
					$conditionCloture = "AND {$table}.dfdesignation IS NULL";
				}

			}
			return "SELECT {$table}.id
					FROM {$table}
					WHERE
						{$table}.personne_id = ".$field."
						$conditionCloture
					ORDER BY
						{$table}.dddesignation DESC,
						{$table}.id DESC
					LIMIT 1";
		}

		/**
		 * Lors de l'ajout d'une orientation ou  d'un référent ($modelName), on
		 * ajoute un nouveau référent de parcours si celui-ci a été précisé lors
		 * de la création.
		 *
		 * @param array $data
		 * @param string $modelName
		 * @param string $datefindesignation
		 * @return boolean
		 */
		public function referentParModele( $data, $modelName, $datefindesignation ) {
			$saved = true;

			$last_referent = $this->find(
				'first',
				array(
					'conditions' => array(
						'PersonneReferent.personne_id'=> $data[$modelName]['personne_id']
					),
					'order' => array(
						'PersonneReferent.dddesignation DESC',
						'PersonneReferent.id DESC'
					),
					'contain' => false
				)
			);

			list( $structurereferente_id, $referent_id ) = explode( '_', $data[$modelName]['referent_id'] );

			if ( !empty( $referent_id ) && ( empty( $last_referent ) || ( isset( $last_referent['PersonneReferent']['referent_id'] ) && !empty( $last_referent['PersonneReferent']['referent_id'] ) && $last_referent['PersonneReferent']['referent_id'] != $referent_id ) ) ) {
				if ( !empty( $last_referent ) && empty( $last_referent['PersonneReferent']['dfdesignation'] ) ) {
					$last_referent['PersonneReferent']['dfdesignation'] = $data[$modelName][$datefindesignation];
					$this->create( $last_referent );
					$saved = $this->save( $last_referent , array( 'atomic' => false ) ) && $saved;
				}

				$personnereferent['PersonneReferent'] = array(
					'personne_id' => $data[$modelName]['personne_id'],
					'referent_id' => $referent_id,
					'structurereferente_id' => $structurereferente_id,
					'dddesignation' => $data[$modelName][$datefindesignation]
				);
				$this->create( $personnereferent );
				$saved = $this->save( $personnereferent , array( 'atomic' => false ) ) && $saved;
			}

			return $saved;
		}


		/**
		 * Sous-requête permettant de savoir si une entrée existe dans la table personnes_referents
		 * pour une entrée de la table referents, et que la date de fin de désignation du référent
		 * n'est pas remplie.
		 *
		 * @param Model $Model
		 * @param string $fieldName Si null, renvoit uniquement la sous-reqête,
		 * 	sinon renvoit la sous-requête aliasée pour un champ (avec l'alias du
		 * 	modèle).
		 * @param string $modelAlias Si null, utilise l'alias de la class PersonneReferent, sinon la valeur donnée.
		 * @return string
		 */
		public function sqNbLiesActifs( Model $Model, $fieldId = 'Referent.id', $fieldName = null, $modelAlias = null ) {
			$alias = Inflector::underscore( $this->alias );

			$modelAlias = ( is_null( $modelAlias ) ? $this->alias : $modelAlias );

			$sq = $this->sq(
					array(
						'fields' => array(
							"COUNT( {$alias}.id )"
						),
						'alias' => $alias,
						'conditions' => array(
							"{$alias}.referent_id = $fieldId",
							"{$alias}.dfdesignation IS NULL"
						)
					)
			);

			if( !is_null( $fieldName ) ) {
				$sq = "( {$sq} ) AS \"{$modelAlias}__{$fieldName}\"";
			}

			return $sq;
		}

		/**
		 * Si je possédais un référent et que l'actuel n'existe pas -> clôture
		 * Si je possédais un référent et qu'il est différent de l'actuel -> clôture, création
		 * Si je possédais un référent et qu'il est le même que l'actuel -> rien ne se passe
		 * Si je ne possédais pas de référent et qu'il y en a un -> création
		 * Si je ne possédais pas de référent et qu'il n'y en pas -> rien ne se passe
		 *
		 * @param integer $personne_id
		 * @param integer $referent_id
		 * @param array $personne_referent
		 * @return boolean
		 */
		public function changeReferentParcours( $personne_id, $referent_id, $personne_referent ) {
			$success = true;

			if( !empty( $personne_referent ) ) {
				$personne_referent[$this->alias]['structurereferente_id'] = suffix( $personne_referent[$this->alias]['structurereferente_id'] );
				$personne_referent[$this->alias]['referent_id'] = suffix( $personne_referent[$this->alias]['referent_id'] );
			}

			$personne_referent_actuel = $this->find(
				'first',
				array(
					'conditions' => array(
						'PersonneReferent.personne_id' => $personne_id,
						'PersonneReferent.dfdesignation IS NULL'
					),
					'contain' => false
				)
			);

			// Si je ne possédais pas de référent et qu'il n'y en pas -> rien ne se passe
			if( empty( $personne_referent_actuel ) && empty( $personne_referent ) ) {
				return $success;
			}

			// Si je possédais un référent et qu'il est le même que l'actuel -> rien ne se passe
			$referentDejaAssigne = (
				!empty( $personne_referent_actuel )
				&& !empty( $personne_referent )
				&& ( $personne_referent_actuel['PersonneReferent']['structurereferente_id'] == $personne_referent['PersonneReferent']['structurereferente_id'] )
				&& ( $personne_referent_actuel['PersonneReferent']['referent_id'] == $personne_referent['PersonneReferent']['referent_id'] )
			);
			if( $referentDejaAssigne ) {
				return $success;
			}

			if( !empty( $personne_referent_actuel ) ) {
				$dfdesignation = (
					( isset( $personne_referent['PersonneReferent']['dddesignation'] ) && !empty( $personne_referent['PersonneReferent']['dddesignation'] ) )
					? $personne_referent['PersonneReferent']['dddesignation']
					: date( 'Y-m-d' )
				);

				$this->id = $personne_referent_actuel['PersonneReferent']['id'];
				$success = $this->saveField( 'dfdesignation', $dfdesignation ) && $success;
			}

			if( !empty( $referent_id ) ) {
				$this->create( $personne_referent );
				return $this->save( null, array( 'atomic' => false ) ) && $success;
			}

			return $success;
		}

		/**
		 * Complète le querydata avec les jointures, champs (PersonneReferent.referent_id,
		 * Referentparcours.nom_complet, Structurereferenteparcours.lib_struc avec
		 * les données concernant le référent du parcours actuel de l'allocataire.
		 *
		 * @param array $query Le querydata à compléter
		 * @return array
		 */
		public function completeSearchQueryReferentParcours( array $query ) {
			$replacement = array(
				'Referent' => 'Referentparcours',
				'Structurereferente' => 'Structurereferenteparcours',
			);

			$sqEnCours = $this->sqDerniere( 'Personne.id', false );
			$conditions = array(
				'OR' => array(
					'PersonneReferent.id IS NULL',
					"PersonneReferent.id IN ( {$sqEnCours} )"
				)
			);

			$query['joins'][] = $this->Personne->join( 'PersonneReferent', array( 'type' => 'LEFT OUTER', 'conditions' => $conditions ) );
			$query['joins'][] = array_words_replace( $this->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ), $replacement );
			$query['joins'][] = array_words_replace( $this->Referent->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ), $replacement );

			$query['fields']['PersonneReferent.referent_id'] = 'PersonneReferent.referent_id';
			$query['fields']['Referentparcours.nom_complet'] = str_replace( 'Referent', 'Referentparcours', $this->Referent->sqVirtualField( 'nom_complet' ) );
			$query['fields']['Structurereferenteparcours.lib_struc'] = 'Structurereferenteparcours.lib_struc';

			return $query;
		}

		/**
		 * Complète le querydata avec les conditions sur la structure référente
		 * du référent de parcours et sur le référent du parcours actuel de
		 * l'allocataire.
		 *
		 * @param array $query Le querydata à compléter
		 * @param array $search Les filtres de recherche
		 *	clés: PersonneReferent.structurereferente_id et PersonneReferent.referent_id
		 * @return array
		 */
		public function completeSearchConditionsReferentParcours( array $query, array $search = array() ) {
			$departement = Configure::read( 'Cg.departement' );

			// Condition sur le référent du parcours
			$referent_id = suffix( Hash::get( $search, 'PersonneReferent.referent_id' ) );
			if( !empty( $referent_id ) ) {
				$query['conditions'][] = array( 'PersonneReferent.referent_id' => $referent_id );
			}

			// Condition sur la structure référente du référent du parcours
			$structurereferente_id = suffix( Hash::get( $search, 'PersonneReferent.structurereferente_id' ) );
			if( !empty( $structurereferente_id ) ) {
				$query['conditions'][] = array( 'Referentparcours.structurereferente_id' => $structurereferente_id );
			}

			// Condition sur le projet insertion emploi territorial du référent du parcours
			$communautesr_id = suffix( Hash::get( $search, 'PersonneReferent.communautesr_id' ) );
			if( 93 == $departement && !empty( $communautesr_id ) ) {
				$sql = $this->Referent->Structurereferente->Communautesr->sqStructuresreferentes( $communautesr_id );
				$query['conditions'][] = array( "Referentparcours.structurereferente_id IN ({$sql})" );
			}

			return $query;
		}


		/**
		 * Complète le querydata avec les jointures, champs (PersonneReferent.referent_id,
		 * Referentparcours.nom_complet, Structurereferenteparcours.lib_struc) et
		 * conditions sur la structure référente du référent de parcours et sur
		 * le référent du parcours actuel de l'allocataire.
		 *
		 * @see PersonneReferent::completeSearchQueryReferentParcours()
		 * @see PersonneReferent::completeSearchConditionsReferentParcours()
		 *
		 * @param array $query Le querydata à compléter
		 * @param array $search Les filtres de recherche
		 *	clés: PersonneReferent.structurereferente_id et PersonneReferent.referent_id
		 * @return array
		 */
		public function completeQdReferentParcours( array $query, array $search ) {
			$query = $this->completeSearchQueryReferentParcours( $query );
			$query = $this->completeSearchConditionsReferentParcours( $query, $search );
			// TODO: communautesr

			return $query;
		}

		/**
		 * Retourne les informations du référent de parcours actuel de l'allocataire.
		 *
		 * Les modèles utilisés dans la requête sont: PersonneReferent, Referent
		 * et Structurereferente.
		 *
		 * @param integer $personne_id L'id technique de la personne
		 * @param array $fields Les champs à retourner
		 *	Par défaut, les champs Referent.id et Referent.structurereferente_id
		 * @return array
		 */
		public function referentParcoursActuel( $personne_id, array $fields = array() ) {
			if( empty( $fields ) ) {
				$fields = array(
					'Referent.id',
					'Referent.structurereferente_id',
				);
			}

			$query = array(
				'fields' => $fields,
				'contain' => false,
				'joins' => array(
					$this->join( 'Referent', array( 'type' => 'INNER' ) ),
					$this->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
				),
				'conditions' => array(
					'PersonneReferent.personne_id' => $personne_id,
					'PersonneReferent.dfdesignation IS NULL',
					'Referent.actif' => 'O',
					'Structurereferente.actif' => 'O',
				),
				'order' => array(
					'PersonneReferent.dddesignation DESC'
				)
			);

			return $this->find( 'first', $query );
		}

		/**
		 * Règle de validation: vérifie à l'ajout, si pour une personne donnée
		 * il existe déjà un référent du parcours actif (sans date de fin de
		 * désignation).
		 *
		 * @param string|array $check Les données à enregistrer
		 * @return boolean true s'il n'existe pas encore de référent actif
		 */
		public function checkReferentUnique( $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$id = Hash::get( $this->data, "{$this->alias}.{$this->primaryKey}" );
			$personne_id = Hash::get( $this->data, "{$this->alias}.personne_id" );
			$dfdesignation = Hash::get( $this->data, "{$this->alias}.dfdesignation" );

			if( empty( $id ) && !empty( $personne_id ) && empty( $dfdesignation ) ) {
				$query = array(
					'fields' => array( "{$this->alias}.{$this->primaryKey}" ),
					'recursive' => -1,
					'contain' => false,
					'conditions' => array(
						"{$this->alias}.personne_id" => $personne_id,
						"{$this->alias}.dfdesignation IS NULL"
					)
				);

				$result = $this->find( 'first', $query );
				$found = Hash::get( $result, "{$this->alias}.{$this->primaryKey}" );
				return empty( $found );
			}

			return true;
		}
	}
?>