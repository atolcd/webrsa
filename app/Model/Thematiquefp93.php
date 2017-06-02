<?php
	/**
	 * Code source de la classe Thematiquefp93.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractElementCataloguefp93', 'Model/Abstractclass' );

	/**
	 * La classe Thematiquefp93 ...
	 *
	 * @package app.Model
	 */
	class Thematiquefp93 extends AbstractElementCataloguefp93 implements IElementWithDescendantCataloguefp93
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Thematiquefp93';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Cataloguepdifp93',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable' => array(
				'Validation2.Validation2DefaultFormatter' => array(
					'suffix'  => '/_{0,1}id$/'
				)
			),
			'Validation2.Validation2RulesComparison',
			'Validation2.Validation2RulesFieldtypes'
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Categoriefp93' => array(
				'className' => 'Categoriefp93',
				'foreignKey' => 'thematiquefp93_id',
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
		 * Règles de validation
		 *
		 * @var array
		 */
		public $validate = array(
			'type' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'type', 'name' ) ),
					'message' => 'Ce couple de valeurs de type et de thématique est déjà présent'
				)
			),
			'name' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'type', 'name' ) ),
					'message' => 'Ce couple de valeurs de type et de thématique est déjà présent'
				)
			)
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
			$conditions[] = "Categoriefp93.thematiquefp93_id = {$this->alias}.{$this->primaryKey}";

			$query = array(
				'alias' => 'Categoriefp93',
				'fields' => array( 'Categoriefp93.thematiquefp93_id' ),
				'joins' => array(
					$this->Categoriefp93->join( 'Filierefp93', array( 'type' => 'INNER' ) )
				),
				'conditions' => $conditions
			);

			if( $type === 'pdi' ) {
				$query['joins'][] = $this->Categoriefp93->Filierefp93->join( 'Actionfp93', array( 'type' => 'INNER' ) );
			}

			$replacements = array(
				'Categoriefp93' => 'categoriesfps93',
				'Filierefp93' => 'filieresfps93',
				'Actionfp93' => 'actionsfps93',
			);

			$sql = $this->Categoriefp93->sq( array_words_replace( $query, $replacements ) );
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
				"{$this->alias}.type" => array( 'empty' => true ),
				"{$this->alias}.{$this->displayField}" => array(),
			);

			return $fields;
		}
	}
?>