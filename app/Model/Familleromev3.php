<?php
	/**
	 * Code source de la classe Familleromev3.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractSearch', 'Model/Abstractclass' );

	/**
	 * La classe Familleromev3 ...
	 *
	 * @package app.Model
	 */
	class Familleromev3 extends AbstractSearch
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Familleromev3';

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
		 * Tri par défaut des enregistrements.
		 *
		 * @var array
		 */
		public $order = array( '"%s"."code" ASC' );

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Domaineromev3' => array(
				'className' => 'Domaineromev3',
				'foreignKey' => 'familleromev3_id',
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
			'Familleromev3.code',
			'Familleromev3.name',
			'Familleromev3.created',
			'Familleromev3.modified'
		);

		/**
		 * Surcharge du constructeur pour les champs virtuels.
		 * Si un driver a été fourni, on utilise la sous-requête correspondante.
		 *
		 * @param integer|string|array $id Set this ID for this model on startup, can also be an array of options, see above.
		 * @param string $table Name of database table to use.
		 * @param string $ds DataSource connection name.
		 */
		public function __construct( $id = false, $table = null, $ds = null ) {
			parent::__construct( $id, $table, $ds );

			// TODO: factoriser, si ce n'est pas un array, ...
			if( isset( $this->order ) && !empty( $this->order ) ) {
				foreach( $this->order as $key => $value ) {
					unset( $this->order[$key] );
					$this->order[str_replace( '%s', $this->alias, $key )] = str_replace( '%s', $this->alias, $value );
				}
			}
		}

		/**
		 * Retourne le querydata pour le moteur de recherche.
		 *
		 * @param array $types Le nom du modèle => le type de jointure
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array();

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = array(
					'fields' => $this->fields()
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
		 * @param array $params
		 * @return array
		 */
		public function options( array $params = array() ) {
			return array();
		}
	}
?>