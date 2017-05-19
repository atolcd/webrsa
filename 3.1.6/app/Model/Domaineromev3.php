<?php
	/**
	 * Code source de la classe Domaineromev3.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractSearch', 'Model/Abstractclass' );

	/**
	 * La classe Domaineromev3 ...
	 *
	 * @package app.Model
	 */
	class Domaineromev3 extends AbstractSearch
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Domaineromev3';

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
			'Catalogueromev3',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Familleromev3' => array(
				'className' => 'Familleromev3',
				'foreignKey' => 'familleromev3_id',
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
			'Metierromev3' => array(
				'className' => 'Metierromev3',
				'foreignKey' => 'domaineromev3_id',
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
		 * Règles de validation.
		 *
		 * @var array
		 */
		public $validate = array(
			'code' => array(
				'minLength' => array(
					'rule' => array( 'minLength', 2 )
				)
			)
		);

		/**
		 * Liste des champs devant apparaitre dans les résultats du moteur de
		 * recherche du paramétrage.
		 *
		 * @var array
		 */
		public $searchResultFields = array(
			'Domaineromev3.code',
			'Familleromev3.name',
			'Domaineromev3.name',
			'Domaineromev3.created',
			'Domaineromev3.modified'
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
				'Familleromev3' => 'INNER'
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = array(
					'fields' => array_merge(
						$this->Familleromev3->fields(),
						$this->fields(),
						array( "( \"Familleromev3\".\"code\" || \"{$this->alias}\".\"code\" ) AS \"{$this->alias}__code\"" )
					),
					'joins' => array(
						$this->join( 'Familleromev3', array( 'type' => $types['Familleromev3'] ) )
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
			$query = $this->Familleromev3->searchConditions( $query, $search );

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
			$query = array(
				'fields' => array(
					'Familleromev3.id',
					'( "Familleromev3"."code" || \' - \' || "Familleromev3"."name" ) AS "Familleromev3__name"'
				),
				'order' => array( '( "Familleromev3"."code" || \' - \' || "Familleromev3"."name" ) ASC' )
			);

			$famillesromesv3 = $this->Familleromev3->find( 'all', $query );
			$famillesromesv3 = Hash::combine( $famillesromesv3, '{n}.Familleromev3.id', '{n}.Familleromev3.name' );

			 $options = array(
				 $this->alias => array(
					'familleromev3_id' => $famillesromesv3
				 )
			 );

			 return $options;
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
			return $options;
		}
	}
?>