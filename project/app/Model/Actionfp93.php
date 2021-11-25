<?php
	/**
	 * Code source de la classe Actionfp93.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractElementCataloguefp93', 'Model/Abstractclass' );

	/**
	 * La classe Actionfp93 ...
	 *
	 * @package app.Model
	 */
	class Actionfp93 extends AbstractElementCataloguefp93
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Actionfp93';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Cataloguepdifp93',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
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
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'allowEmpty' => false,
					'required' => false
				)
			),
			'thematiquefp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'allowEmpty' => false,
					'required' => false
				)
			),
			'categoriefp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'allowEmpty' => false,
					'required' => false
				)
			),
			'prestatairefp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'allowEmpty' => false,
					'required' => false
				)
			),
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Filierefp93' => array(
				'className' => 'Filierefp93',
				'foreignKey' => 'filierefp93_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Adresseprestatairefp93' => array(
				'className' => 'Adresseprestatairefp93',
				'foreignKey' => 'adresseprestatairefp93_id',
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
			'Ficheprescription93' => array(
				'className' => 'Ficheprescription93',
				'foreignKey' => 'actionfp93_id',
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
		 * Retourne la liste des champs à utiliser dans le formulaire d'ajout / de
		 * modification de la partie paramétrage.
		 *
		 * @return array
		 */
		public function getParametrageFields() {
			$fields = parent::getParametrageFields();

			$virtualFields = array(
				"{$this->alias}.typethematiquefp93_id" => array( 'empty' => true ),
				"{$this->alias}.yearthematiquefp93_id" => array( 'empty' => true ),
				"{$this->alias}.thematiquefp93_id" => array( 'empty' => true ),
				"{$this->alias}.categoriefp93_id" => array( 'empty' => true ),
				"{$this->alias}.filierefp93_id" => array( 'empty' => true ),
				"{$this->alias}.prestatairefp93_id" => array( 'empty' => true )
			);
			$fields = $virtualFields + $fields;

			unset($fields['Thematiquefp93.yearthema']);

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
				'fields' => array_merge(
					$this->fields(),
					array(
						'Adresseprestatairefp93.prestatairefp93_id',
						'Filierefp93.id',
						'Categoriefp93.id',
						'Thematiquefp93.id',
						'Thematiquefp93.type',
						'Thematiquefp93.yearthema'
					)
				),
				'conditions' => array(
					"{$this->alias}.{$this->primaryKey}" => $id
				),
				'joins' => array(
					$this->join( 'Adresseprestatairefp93', array( 'type' => 'INNER' ) ),
					$this->join( 'Filierefp93', array( 'type' => 'INNER' ) ),
					$this->Filierefp93->join( 'Categoriefp93', array( 'type' => 'INNER' ) ),
					$this->Filierefp93->Categoriefp93->join( 'Thematiquefp93', array( 'type' => 'INNER' ) ),
				)
			);

			$return = $this->find( 'first', $query );

			if( !empty( $return ) ) {
				$typethematiquefp93_id = Hash::get( $return, 'Thematiquefp93.type' );
				$yearthematiquefp93_id = $typethematiquefp93_id.Hash::get( $return, "Thematiquefp93.yearthema" );
				$thematiquefp93_id = Hash::get( $return, 'Thematiquefp93.id' );
				$categoriefp93_id = Hash::get( $return, 'Categoriefp93.id' );
				$filierefp93_id = Hash::get( $return, 'Filierefp93.id' );
				$prestatairefp93_id = Hash::get( $return, 'Adresseprestatairefp93.prestatairefp93_id' );
				$adresseprestatairefp93_id = Hash::get( $return, 'Actionfp93.adresseprestatairefp93_id' );

				$return = Hash::merge(
					$return,
					array(
						$this->alias => array(
							'typethematiquefp93_id' => $typethematiquefp93_id,
							'yearthematiquefp93_id' => $typethematiquefp93_id.'_'.$yearthematiquefp93_id,
							'thematiquefp93_id' => $yearthematiquefp93_id.'_'.$thematiquefp93_id,
							'categoriefp93_id' => "{$thematiquefp93_id}_{$categoriefp93_id}",
							'filierefp93_id' => "{$categoriefp93_id}_{$filierefp93_id}",
							'prestatairefp93_id' => $prestatairefp93_id,
							'adresseprestatairefp93_id' => "{$prestatairefp93_id}_{$adresseprestatairefp93_id}",
						)
					)
				);
			}

			return $return;
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
			$parentOptions = parent::getParametrageOptions();
			$filierefp93Options = $this->Filierefp93->getParametrageOptions( true );

			$options = array(
				$this->alias => Hash::merge(
					$parentOptions[$this->alias],
					$filierefp93Options[$this->Filierefp93->alias]
				)
			);

			// Liste des filières
			$query = array(
				'fields' => array(
					'( "Filierefp93"."categoriefp93_id" || \'_\' || "Filierefp93"."id" ) AS "Filierefp93__id"',
					'Filierefp93.name',
				)
			);
			$results = $this->Filierefp93->find( 'all', $query );
			$options[$this->alias]['filierefp93_id'] = Hash::combine( $results, '{n}.Filierefp93.id', '{n}.Filierefp93.name' );

			// Liste des prestataires
			$query = array(
				'fields' => array(
					'Prestatairefp93.id',
					'Prestatairefp93.name',
				)
			);
			$results = $this->Adresseprestatairefp93->Prestatairefp93->find( 'all', $query );
			$options[$this->alias]['prestatairefp93_id'] = Hash::combine( $results, '{n}.Prestatairefp93.id', '{n}.Prestatairefp93.name' );

			// Liste des adresses des prestataires
			$query = array(
				'fields' => array(
					'( "Adresseprestatairefp93"."prestatairefp93_id" || \'_\' || "Adresseprestatairefp93"."id" ) AS "Adresseprestatairefp93__id"',
					'Adresseprestatairefp93.name',
				)
			);
			$results = $this->Adresseprestatairefp93->find( 'all', $query );
			$options[$this->alias]['adresseprestatairefp93_id'] = Hash::combine( $results, '{n}.Adresseprestatairefp93.id', '{n}.Adresseprestatairefp93.name' );

			// On s'arrange pour ne pouvoir ajouter que des actions PDI
			unset( $options[$this->alias]['typethematiquefp93_id']['horspdi'] );

			return $options;
		}

		/**
		 * Retourne la liste des champs dépendants à utiliser dans le formulaire
		 * d'ajout / de modification de la partie paramétrage.
		 *
		 * @return array
		 */
		public function getParametrageDependantFields() {
			$return = array(
				"{$this->alias}.typethematiquefp93_id" => "{$this->alias}.yearthematiquefp93_id",
				"{$this->alias}.yearthematiquefp93_id" => "{$this->alias}.thematiquefp93_id",
				"{$this->alias}.thematiquefp93_id" => "{$this->alias}.categoriefp93_id",
				"{$this->alias}.categoriefp93_id" => "{$this->alias}.filierefp93_id",
				"{$this->alias}.prestatairefp93_id" => "{$this->alias}.adresseprestatairefp93_id",
			);

			return $return;
		}

		/**
		 * Retourne les options utilisées par le formulaire de recherche
		 * @return array
		 */
		public function getSearchOptions() {
			$options = $this->Filierefp93->getSearchOptions();

			$listFiliere = $this->Filierefp93->find('all', array(
				'order' => array(
					'Filierefp93.name ASC',
				)
			));

			foreach( $listFiliere as $filiere ) {
				$filiereId = $filiere['Filierefp93']['categoriefp93_id'] . '_' . $filiere['Filierefp93']['id'];
				$options['Filierefp93']['id'][$filiereId] = $filiere['Filierefp93']['name'];
			}

			return $options;
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
				$value = (string)Hash::get( $search, "{$this->alias}.{$field}" );
				if( '' !== $value ) {
					$query['conditions'][] = "{$this->alias}.{$field} ILIKE '{$this->wildcard( $value )}'";
				}
			}
			// 2. Valeurs exactes
			$fieldsValues = array(
				'Thematiquefp93.yearthema',
				'Thematiquefp93.type',
				'Thematiquefp93.id',
			);
			foreach( $fieldsValues as $field ) {
				$value = (string)Hash::get( $search, $field );
				if( '' !== $value ) {
					$query['conditions'][] = array( $field => $value );
				}
			}

			// 3. Valeurs de select dépendants
			$pathsToExplode = array(
				'Categoriefp93.id',
				'Filierefp93.id',
			);

			foreach( $pathsToExplode as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' && strpos($value, '_') > 0 ) {
					list(,$value) = explode('_', $value);
					$query['conditions'][$path] = $value;
				}
			}

			return $query;
		}
	}
?>