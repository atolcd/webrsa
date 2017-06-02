<?php
	/**
	 * Code source de la classe Metierromev3.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractSearch', 'Model/Abstractclass' );

	/**
	 * La classe Metierromev3 ...
	 *
	 * @package app.Model
	 */
	class Metierromev3 extends AbstractSearch
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Metierromev3';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Catalogueromev3',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesComparison',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Domaineromev3' => array(
				'className' => 'Domaineromev3',
				'foreignKey' => 'domaineromev3_id',
				'conditions' => null,
				'type' => 'INNER',
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
			'Appellationromev3' => array(
				'className' => 'Appellationromev3',
				'foreignKey' => 'metierromev3_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
			'Correspondanceromev2v3' => array(
				'className' => 'Correspondanceromev2v3',
				'foreignKey' => 'metierromev3_id',
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
		 * Liste des champs devant apparaitre dans les résultats du moteur de
		 * recherche du paramétrage.
		 *
		 * @var array
		 */
		public $searchResultFields = array(
			'Metierromev3.code',
			'Familleromev3.name',
			'Domaineromev3.name',
			'Metierromev3.name',
			'Metierromev3.created',
			'Metierromev3.modified'
		);

		/**
		 * Règles de validation.
		 *
		 * @var array
		 */
		public $validate = array(
			'domaineromev3_id' => array(
				'checkUniqueDomaineromev3IdCode' => array(
					'rule' => array( 'checkUnique', array( 'domaineromev3_id', 'code' ) ),
					'message' => 'Ce couple de valeurs de domaine et de code est déjà présent'
				),
				'checkUniqueDomaineromev3IdName' => array(
					'rule' => array( 'checkUnique', array( 'domaineromev3_id', 'name' ) ),
					'message' => 'Ce couple de valeurs de domaine et de métier est déjà présent'
				)
			),
			'code' => array(
				'minLength' => array(
					'rule' => array( 'minLength', 2 )
				),
				'checkUniqueDomaineromev3IdCode' => array(
					'rule' => array( 'checkUnique', array( 'domaineromev3_id', 'code' ) ),
					'message' => 'Ce couple de valeurs de domaine et de code est déjà présent'
				)
			),
			'name' => array(
				'checkUniqueDomaineromev3IdName' => array(
					'rule' => array( 'checkUnique', array( 'domaineromev3_id', 'name' ) ),
					'message' => 'Ce couple de valeurs de domaine et de métier est déjà présent'
				)
			)
		);

		/**
		 * Retourne le querydata pour le moteur de recherche.
		 *
		 * @param array $types Le nom du modèle => le type de jointure
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			// FIXME: champs virtuels
			$types += array(
				'Familleromev3' => 'INNER',
				'Domaineromev3' => 'INNER'
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = array(
					'fields' => array_merge(
						$this->Domaineromev3->Familleromev3->fields(),
						$this->Domaineromev3->fields(),
						$this->fields(),
						array( "( \"Familleromev3\".\"code\" || \"Domaineromev3\".\"code\" || \"{$this->alias}\".\"code\" ) AS \"{$this->alias}__code\"" )
					),
					'joins' => array(
						$this->join( 'Domaineromev3', array( 'type' => $types['Domaineromev3'] ) ),
						$this->Domaineromev3->join( 'Familleromev3', array( 'type' => $types['Familleromev3'] ) )
					)
				);

				// Enregistrement dans le cache
				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			$query = $this->Domaineromev3->searchConditions( $query, $search );

			$code = Hash::get( $search, "{$this->alias}.code" );
			if( !empty( $code ) ) {
				$query['conditions']["{$this->alias}.code ILIKE"] = $code;
			}

			$name = Hash::get( $search, "{$this->alias}.name" );
			if( !empty( $name ) ) {
				$query['conditions']["{$this->alias}.name ILIKE"] = "%{$name}%";
			}

			return $query;
		}

		/**
		 * Retourne les options nécessaires au formulaire de recherche, aux
		 * impressions, ...
		 *
		 * @todo Cache...
		 *
		 * @param array $params
		 * @return array
		 */
		public function options( array $params = array() ) {
			// FIXME: champs virtuels
			$options = $this->Domaineromev3->options( $params );

			$query = array(
				'fields' => array(
					'( "Domaineromev3"."familleromev3_id" || \'_\' || "Domaineromev3"."id" ) AS "Domaineromev3__id"',
					'( "Familleromev3"."code" || "Domaineromev3"."code" || \' - \' || "Domaineromev3"."name" ) AS "Domaineromev3__name"',
				),
				'joins' => array(
					$this->Domaineromev3->join( 'Familleromev3', array( 'type' => 'INNER' ) )
				),
				'order' => array( '( "Familleromev3"."code" || "Domaineromev3"."code" || \' - \' || "Familleromev3"."name" ) ASC' )
			);

			$domainesromesv3 = $this->Domaineromev3->find( 'all', $query );
			$domainesromesv3 = Hash::combine( $domainesromesv3, '{n}.Domaineromev3.id', '{n}.Domaineromev3.name' );

			$options[$this->alias] = array(
				 'domaineromev3_id' => $domainesromesv3
			);

			return $options;
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
						"\"Domaineromev3\".\"familleromev3_id\" AS \"{$this->alias}__familleromev3_id\"",
						"( \"Domaineromev3\".\"familleromev3_id\" || '_' || \"{$this->alias}\".\"domaineromev3_id\" ) AS \"{$this->alias}__domaineromev3_id\"",
					)
				),
				'conditions' => array(
					"{$this->alias}.{$this->primaryKey}" => $id
				),
				'joins' => array(
					$this->join( 'Domaineromev3', array( 'type' => 'INNER' ) )
				)
			);

			return $this->find( 'first', $query );
		}

		/**
		 * Retourne la liste des champs à utiliser dans le formulaire d'ajout / de
		 * modification de la partie paramétrage.
		 *
		 * @return array
		 */
		public function getParametrageFields() {
			$fields = array(
				"{$this->alias}.familleromev3_id" => array( 'empty' => true, 'required' => true )
			);

			$fields = Hash::merge(
				$fields,
				$this->Behaviors->Catalogueromev3->getParametrageFields( $this )
			);

			return $fields;
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
			$options = $this->Behaviors->Catalogueromev3->getParametrageOptions( $this, $hasDescendant );
			$options = Hash::merge( $options, $this->options() );
			$options[$this->alias]['familleromev3_id'] = $options['Domaineromev3']['familleromev3_id'];
			unset( $options['Domaineromev3']['familleromev3_id'] );
			return $options;
		}
	}
?>