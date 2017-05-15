<?php
	/**
	 * Ce composant permet d'obtenir un "aperçu" de la base de données (au format XML)  et de lister (au
	 * format XML) les différences entre deux aperçus.
	 *
	 * Ce composant nécessite le plugin Pgsqlcake.
	 */
	class PgDbSnapshotComponent extends Component
	{

		/**
		 * Controller using this component.
		 *
		 * @var Controller
		 */
		public $controller = null;

		/**
		 * Les options par défaut utilisées pour la comparaison entre deux "aperçus" XML.
		 * Par défaut, on ne rapporte pas les tables ni les fonctions existant en plus dans l'"aperçu"
		 * que l'on teste.
		 *
		 * @var array
		 */
		public $defaultCompareOptions = array(
			'functions' => array(
				'add' => false,
				'remove' => true,
				'change' => true,
			),
			'tables' => array(
				'add' => false,
				'remove' => true,
				'change' => true,
			),
			'fields' => array(
				'add' => true,
				'remove' => true,
				'change' => true,
			),
			'indexes' => array(
				'add' => true,
				'remove' => true,
				'change' => true,
			),
			'foreignkeys' => array(
				'add' => true,
				'remove' => true,
				'change' => true,
			),
			'checks' => array(
				'add' => true,
				'remove' => true,
				'change' => true,
			),
		);

		/**
		 * Called before the Controller::beforeFilter().
		 *
		 * @param Controller $controller Controller with components to initialize
		 * @return void
		 */
		public function initialize( &$controller ) {
			$this->controller = $controller;
		}

		/**
		 * Retourne une partie d'un "aperçu" XML concernant une table spécifique.
		 *
		 * @param DataSource $ds La connection à la base de données que l'on utilise
		 * @param string $tableName La table pour laquelle on vaut obtenir l'"aperçu"
		 * @return string
		 */
		protected function _tableXmlDumpPart( $ds, $tableName ) {
			// On instancie le modèle lié à la table
			$Model = ClassRegistry::init( Inflector::classify( $tableName ) );

			// Description des champs de la table
			$schema = $Model->schema();
			debug( var_export( $schema, true ) );

			// Indexes liés à la table
			$indexes = $ds->index( $Model );
			if( !empty( $indexes ) ) {
				debug( var_export( $indexes, true ) );
			}

			// Utilisation de SchemaBehavior situé dans le plugin Pgsqlcake
			$Model->Behaviors->attach( 'Pgsqlcake.PgsqlSchema' );

			// Liste des clés étrangères définies depuis la table concernée vers d'autres tables
			$foreignKeys = $Model->foreignKeysFrom();
			if( !empty( $foreignKeys ) ) {
				debug( var_export( $foreignKeys, true ) );
			}

			// Liste des contraintes de check définies sur la table concernée
			$checks = $Model->pgCheckConstraints();
			if( !empty( $checks ) ) {
				debug( var_export( $checks, true ) );
			}
		}

		/**
		 * Retourne un "aperçu" XML de la base de données à laquelle on est connecté, false en cas d'erreur.
		 *
		 * L'aperçu contiendra les informations suivantes:
		 *   - liste des fonctions pl/pgsql contenues dans le schéma actuellement utilisé
		 *   - liste des tables contenues dans le schéma actuellement utilisé, avec pour chacune d'entre elles:
		 *     * liste des champs
		 *     * liste des contraintes de clés étrangères
		 *     * liste des indexes
		 *     * liste des contraintes de type check
		 *
		 * @param Model $Model Une instance d'un modèle, lié à une table de la base de données
		 *	à laquelle on est connectés
		 * @return string
		 */
		public function makeXmlDump( &$Model ) {
			// Général à la base de données
			$ds = $Model->getDataSource();

			// Liste des tables, triées par ordre alphabétique
			$tables = $ds->listSources();
			sort( $tables );
			debug( var_export( $tables, true ) );


			// Utilisation de SchemaBehavior situé dans le plugin Pgsqlcake
			$Model->Behaviors->attach( 'Pgsqlcake.PgsqlSchema' );

			// Liste des fonctions postgresql disponibles, propres à WebRSA, dans le schéma utilisé par la connexion (public)
			$functions = $Model->pgFunctions( array(), array( "namespace.nspname = '{$ds->config['schema']}'" ) );
			debug( var_export( $functions, true ) );

			// FIXME: pour tester/accélérer la boucle ci-dessous, on ne garde qu'une table
			$tables =array( 'dossiers' );

			// Informations propres à chacune des tables
			foreach( $tables as $tableName ) {
				$this->_tableXmlDumpPart( $ds, $tableName );
			}
		}

		/**
		 * Retourne les différences, au format XML, entre deux '"aperçus" XML de la base de données.
		 * Rertourne false en cas d'erreur.
		 *
		 * @param string $referenceXml L'aperçu XML de référence.
		 * @param string $testXml L'aperçu XML que l'on souhaite comparer à la référence.
		 * @param array $options Les options de comparaison.
		 * @return string
		 */
		public function compareXmlDumps( $referenceXml, $testXml, $options = array() ) {
			$options = Set::merge( $this->defaultCompareOptions, $options );
		}

		/**
		 * Lit et retourne le contenu d'un fichier d'"aperçu" XML.
		 * Renvoie false en cas d'erreur.
		 *
		 * @param string $filename Le chemin vers le fichier
		 * @return string
		 */
		public function readXmlDump( $filename ) {
		}

		/**
		 * Écriture du contenu d'un fichier d'"aperçu" XML.
		 * Retourn false en cas d'erreur, true en cas de succès.
		 *
		 * @param string $filename Le chemin vers le fichier dans lequel écrire
		 * @param string $xmlDump Le contenu XML à écrire dans le fichier
		 * @return boolean
		 */
		public function writeXmlDump( $filename, $xmlDump ) {
		}

		/**
		 * Retourne la version courante de la base de données (ex. 2.3).
		 * Il s'agit de la valeur du champ current ayant ll'entrée ayant l'id le plus élevé de la table
		 * webrsaversions.
		 *
		 * @return string
		 */
		public function currentDbVersion() {
		}

		/**
		 * Called before Controller::redirect().  Allows you to replace the url that will
		 * be redirected to with a new url. The return of this method can either be an array or a string.
		 *
		 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::beforeRedirect
		 */
		public function beforeRedirect( &$controller, $url, $status = null, $exit = true ) {
			return array( 'url' => $url, 'status' => $status, 'exit' => $exit );
		}
	}
?>