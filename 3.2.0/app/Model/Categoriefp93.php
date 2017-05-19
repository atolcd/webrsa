<?php
	/**
	 * Code source de la classe Categoriefp93.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractElementCataloguefp93', 'Model/Abstractclass' );

	/**
	 * La classe Categoriefp93 ...
	 *
	 * @package app.Model
	 */
	class Categoriefp93 extends AbstractElementCataloguefp93 implements IElementWithDescendantCataloguefp93
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Categoriefp93';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Cataloguepdifp93',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesComparison',
			'Validation2.Validation2RulesFieldtypes'
		);

		/**
		 * Ajout des règles de validation des champs virtuels du formulaire de
		 * paramétrage.
		 *
		 * @var array
		 */
		public $validate = array(
			'typethematiquefp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'thematiquefp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				),
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'thematiquefp93_id', 'name' ) ),
					'message' => 'Ce couple de valeurs de thématique et de catégorie est déjà présent'
				)
			),
			'name' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'thematiquefp93_id', 'name' ) ),
					'message' => 'Ce couple de valeurs de thématique et de catégorie est déjà présent'
				)
			)
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Thematiquefp93' => array(
				'className' => 'Thematiquefp93',
				'foreignKey' => 'thematiquefp93_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Filierefp93' => array(
				'className' => 'Filierefp93',
				'foreignKey' => 'categoriefp93_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
		);

		/**
		 * Retourne une condition qui est en fait une sous-requête, avec les
		 * jointures nécessaires pour atteindre soit le modèle Filierefp93 (en
		 * cas d'action hors pdi), soit le modèle Actionfp93 (en cas d'action PDI).
		 *
		 * Cela permet de s'assurer d'une part qu'il n'y aura pas d'enregistrement
		 * orphelin dans les listes déroulantes, et d'autre part d'ajouter des conditions.
		 *
		 * @param string Le type d'action ("pdi" ou "hors pdi")
		 * @param array $conditions Les conditions supplémentaires à appliquer
		 * @return string
		 */
		public function getDependantListCondition( $type, array $conditions ) {
			$conditions[] = "Filierefp93.categoriefp93_id = {$this->alias}.{$this->primaryKey}";

			$query = array(
				'alias' => 'Filierefp93',
				'fields' => array( 'Filierefp93.categoriefp93_id' ),
				'joins' => array(),
				'conditions' => $conditions
			);

			if( $type === 'pdi' ) {
				$query['joins'][] = $this->Filierefp93->join( 'Actionfp93', array( 'type' => 'INNER' ) );
			}

			$replacements = array(
				'Filierefp93' => 'filieresfps93',
				'Actionfp93' => 'actionsfps93',
			);

			$sql = $this->Filierefp93->sq( array_words_replace( $query, $replacements ) );
			$condition = "{$this->alias}.{$this->primaryKey} IN ( {$sql} )";

			return $condition;
		}

		/**
		 * Retourne la liste des champs à utiliser dans le formulaire d'ajout / de
		 * modification de la partie paramétrage.
		 *
		 * @return array
		 */
		public function getParametrageFields() {
			$fields = array(
				"{$this->alias}.{$this->primaryKey}" => array(),
				"{$this->alias}.typethematiquefp93_id" => array( 'empty' => true ),
				"{$this->alias}.thematiquefp93_id" => array( 'empty' => true ),
				"{$this->alias}.{$this->displayField}" => array(),
			);

			return $fields;
		}

		/**
		 * Retourne les données à utiliser dans le formulaire de modification de
		 * la partie paramétrage.
		 *
		 * @param integer $id
		 * @return array
		 */
		public function getParametrageFormData( $id ) {
			$query = array(
				'fields' => array(
					"Thematiquefp93.type",
					"{$this->alias}.{$this->primaryKey}",
					"{$this->alias}.{$this->displayField}",
					"{$this->alias}.thematiquefp93_id",
				),
				'joins' => array(
					$this->join( 'Thematiquefp93', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					"{$this->alias}.{$this->primaryKey}" => $id
				)
			);

			$result = $this->find( 'first', $query );

			if( !empty( $result ) ) {
				$typethematiquefp93_id = Hash::get( $result, "Thematiquefp93.type" );

				$result = array(
					$this->alias => array(
						$this->primaryKey => Hash::get( $result, "{$this->alias}.{$this->primaryKey}" ),
						'typethematiquefp93_id' => $typethematiquefp93_id,
						'thematiquefp93_id' => $typethematiquefp93_id.'_'.Hash::get( $result, "{$this->alias}.thematiquefp93_id" ),
						$this->displayField => Hash::get( $result, "{$this->alias}.{$this->displayField}" ),
					)
				);
			}

			return $result;
		}

		/**
		 * Retourne les options à utiliser dans le formulaire d'ajout / de
		 * modification de la partie paramétrage.
		 *
		 * @param boolean Permet de s'assurer que l'on possède au moins un
		 *	enregistrement au niveau inférieur.
		 * @return array
		 */
		public function getParametrageOptions( $hasDescendant = false ) {
			$options = $this->Thematiquefp93->getParametrageOptions( true );
			$options = array(
				$this->alias => array(
					'typethematiquefp93_id' => (array)Hash::get( $options, 'Thematiquefp93.type' )
				)
			);

			// Liste des thématiques
			$query = array(
				'fields' => array(
					'( "Thematiquefp93"."type" || \'_\' || "Thematiquefp93"."id" ) AS "Thematiquefp93__id"',
					'Thematiquefp93.name',
				),
				'conditions' => array()
			);

			// ... et qui possède au moins un descendant ?
			if( $hasDescendant ) {
				$this->Thematiquefp93->Behaviors->attach( 'LinkedRecords' );
				$query['conditions'][] = $this->Thematiquefp93->linkedRecordVirtualField( $this->alias );
			}

			$results = $this->Thematiquefp93->find( 'all', $query );
			$options[$this->alias]['thematiquefp93_id'] = Hash::combine( $results, '{n}.Thematiquefp93.id', '{n}.Thematiquefp93.name' );

			return $options;
		}
	}
?>