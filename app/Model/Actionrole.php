<?php
	/**
	 * Code source de la classe Actionrole.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Actionrole ...
	 *
	 * @package app.Model
	 */
	class Actionrole extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Actionrole';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * @var array
		 */
		public $belongsTo = array(
			'Role' => array(
				'className' => 'Role',
				'foreignKey' => 'role_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Categorieactionrole' => array(
				'className' => 'Categorieactionrole',
				'foreignKey' => 'categorieactionrole_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Relations hasMany
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Actionroleresultuser' => array(
				'className' => 'Actionroleresultuser',
				'foreignKey' => 'actionrole_id',
				'dependent' => false,
				'conditions' => '',
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
		 * Retourne le nombre de résultats d'un moteur de recherche pour
		 * l'utilisateur connecté à partir d'une URL contenant les critères de
		 * recherche en paramètres nommés (à la CakePHP).
		 *
		 * @param string $url
		 * @return array
		 */
		public function count( $url ) {
			$request = parseSearchUrl( $url, array( 'sessionKey' ) );
			if( true === empty( $request ) ) {
				return null;
			}

			$request['controller'] = Inflector::camelize( Inflector::underscore( $request['controller'] ) );
			$request['named'] = Hash::expand( $request['named'], '__' );

			$searchKey = "{$request['controller']}.{$request['action']}";
			$WebrsaRecherche = ClassRegistry::init( 'WebrsaRecherche' );
			if( false === isset( $WebrsaRecherche->searches[$searchKey] ) ) {
				return null;
			}

			$params = $WebrsaRecherche->searches[$searchKey];
			$params['jetons'] = false;

			$modelRecherche = ClassRegistry::init($params['modelRechercheName']);
			$query = $modelRecherche->searchConditions(
				$modelRecherche->searchQuery(),
				(array)Hash::get( $request, 'named.Search' )
			);

			$Component = $WebrsaRecherche->component($searchKey);
			$query = $Component->query($params, $request['named']);

			return ClassRegistry::init( $params['modelName'] )->find( 'count', $query );
		}
	}
?>