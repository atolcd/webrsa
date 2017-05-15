<?php
	/**
	 * Code source de la classe Filierefp93.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractElementCataloguefp93', 'Model/Abstractclass' );

	/**
	 * La classe Filierefp93 ...
	 *
	 * @package app.Model
	 */
	class Filierefp93 extends AbstractElementCataloguefp93 implements IElementWithDescendantCataloguefp93
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Filierefp93';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Cataloguepdifp93',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);

		/**
		 * Ajout des règles de validation des champs virtuels du formulaire de
		 * paramétrage.
		 *
		 * @var array
		 */
		public $validate = array(
			'typethematiquefp93_id' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				)
			),
			'thematiquefp93_id' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				)
			),
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Categoriefp93' => array(
				'className' => 'Categoriefp93',
				'foreignKey' => 'categoriefp93_id',
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
			'Actionfp93' => array(
				'className' => 'Actionfp93',
				'foreignKey' => 'filierefp93_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
			'Ficheprescription93' => array(
				'className' => 'Ficheprescription93',
				'foreignKey' => 'filierefp93_id',
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
			if( $type === 'horspdi' ) {
				return '1 = 1';
			}

			$conditions[] = "Actionfp93.filierefp93_id = {$this->alias}.{$this->primaryKey}";

			$query = array(
				'alias' => 'Actionfp93',
				'fields' => array( 'Actionfp93.filierefp93_id' ),
				'conditions' => $conditions
			);

			$replacements = array(
				'Actionfp93' => 'actionsfps93',
			);

			$sql = $this->Actionfp93->sq( array_words_replace( $query, $replacements ) );
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
				"{$this->alias}.id" => array(),
				"{$this->alias}.typethematiquefp93_id" => array( 'empty' => true ),
				"{$this->alias}.thematiquefp93_id" => array( 'empty' => true ),
				"{$this->alias}.categoriefp93_id" => array( 'empty' => true ),
				"{$this->alias}.name" => array(),
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
					"Categoriefp93.thematiquefp93_id",
					"{$this->alias}.categoriefp93_id"
				),
				'joins' => array(
					$this->join( 'Categoriefp93', array( 'type' => 'INNER' ) ),
					$this->Categoriefp93->join( 'Thematiquefp93', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					"{$this->alias}.{$this->primaryKey}" => $id
				)
			);

			$result = $this->find( 'first', $query );

			if( !empty( $result ) ) {
				$typethematiquefp93_id = Hash::get( $result, "Thematiquefp93.type" );
				$thematiquefp93_id = Hash::get( $result, "Categoriefp93.thematiquefp93_id" );

				$result = array(
					$this->alias => array(
						$this->primaryKey => Hash::get( $result, "{$this->alias}.{$this->primaryKey}" ),
						'typethematiquefp93_id' => $typethematiquefp93_id,
						'thematiquefp93_id' => $typethematiquefp93_id.'_'.$thematiquefp93_id,
						'categoriefp93_id' => $thematiquefp93_id.'_'.Hash::get( $result, "{$this->alias}.categoriefp93_id" ),
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
			$options = $this->Categoriefp93->getParametrageOptions( true );
			$options[$this->alias] = $options[$this->Categoriefp93->alias];
			unset( $options[$this->Categoriefp93->alias] );

			// Liste des catégories
			$query = array(
				'fields' => array(
					'( "Categoriefp93"."thematiquefp93_id" || \'_\' || "Categoriefp93"."id" ) AS "Categoriefp93__id"',
					'Categoriefp93.name',
				),
				'conditions' => array()
			);

			// ... et qui possède au moins un descendant ?
			if( $hasDescendant ) {
				$this->Categoriefp93->Behaviors->attach( 'LinkedRecords' );
				$query['conditions'][] = $this->Categoriefp93->linkedRecordVirtualField( $this->alias );
			}

			$results = $this->Categoriefp93->find( 'all', $query );
			$options[$this->alias]['categoriefp93_id'] = Hash::combine( $results, '{n}.Categoriefp93.id', '{n}.Categoriefp93.name' );

			return $options;
		}
	}
?>