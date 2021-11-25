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
					'rule' => array( 'checkUnique', array( 'type', 'name','yearthema'  ) ),
					'message' => 'Ce groupe de valeurs de type et de thématique est déjà présent'
				)
			),
			'name' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'type', 'name','yearthema'  ) ),
					'message' => 'Ce groupe de valeurs de type et de thématique est déjà présent'
				)
			),
			'yearthema' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'type', 'name','yearthema'  ) ),
					'message' => 'Ce groupe de valeurs de type et de thématique est déjà présent'
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

			$fields = parent::getParametrageFields();

			return $fields;
		}

		/**
		 * Retourne la liste des catégories liées à l'id de la thématique éditée
		 * avec leur liaison aux tableaux 4 & 5
		 *
		 * @param integer $id
		 * @return array
		 */
		public function getListCategories( $id ) {
			return $this->Categoriefp93->find('all', array(
				'fields' => array(
					'Categoriefp93.name',
					'Categoriefp93.tableau4_actif',
					'Categoriefp93.tableau5_actif',
				),
				'recursive' => -1,
				'conditions' => array(
					'Categoriefp93.thematiquefp93_id' => $id
				)
			));
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
			$options = $this->enums();

			$startDate = date('Y', strtotime('+1 year')) ;
			$arrayYears = array();
			for( $year = (INT)$startDate; $year >= 2017 ; $year -- ){
				$arrayYears["$year"] = strval($year) ;
			}

			$options[$this->alias]['yearthema'] = $arrayYears;

			return $options;
		}

		/**
		 * Retourne les options utilisées par le formulaire de recherche
		 * @return array
		 */
		public function getSearchOptions() {
			return array_merge(
				$this->getParametrageOptions(),
				$this->Categoriefp93->options()
			);
		}

		/**
		 * Applique les conditions envoyées par le moteur de recherche au querydata.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			// 1. Valeurs approchantes
			foreach( array( 'name' ) as $field ) {
				$value = (string)Hash::get( $search, "Thematiquefp93.{$field}" );
				if( '' !== $value ) {
					$query['conditions'][] = 'Thematiquefp93.'.$field.' ILIKE \''.$this->wildcard( $value ).'\'';
				}
			}

			// 2. Valeurs exactes
			$fieldsValues = array(
				'Thematiquefp93.yearthema',
				'Thematiquefp93.type',
				'Categoriefp93.tableau4_actif',
				'Categoriefp93.tableau5_actif',
			);
			foreach( $fieldsValues as $field ) {
				$value = (string)Hash::get( $search, $field );
				if( '' !== $value ) {
					// Conditions particulières sur la notion d'actif / inactif des tableaux 4 & 5
					if( in_array($field, array('Categoriefp93.tableau4_actif', 'Categoriefp93.tableau5_actif') ) ) {
						$field = str_replace( 'Categoriefp93.', '', $field );
						$actifValue = $value == '1' ? 'TRUE' : 'FALSE';
						$query['conditions'][] = "Thematiquefp93.id IN (
							SELECT DISTINCT thematiquefp93_id
							FROM categoriesfps93
							WHERE {$field} = {$actifValue}
							)";
					} else {
						$query['conditions'][] = array( $field => $value );
					}
				}
			}

			return $query;
		}
	}
?>