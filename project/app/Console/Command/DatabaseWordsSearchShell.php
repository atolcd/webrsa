<?php
	/**
	 * Code source de la classe DatabaseWordsSearchShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppShell', 'Console/Command' );
	App::uses( 'ConnectionManager', 'Model' );

	/**
	 * La classe DatabaseWordsSearchShell permet de chercher des mots dans tous
	 * les champs texte de la base de données.
	 *
	 * @package app.Console.Command
	 */
	class DatabaseWordsSearchShell extends AppShell
	{

		/**
		 * La constante à utiliser dans la méthode _stop() en cas de succès.
		 */
		const SUCCESS = 0;

		/**
		 * La constante à utiliser dans la méthode _stop() en cas d'erreur.
		 */
		const ERROR = 1;

		/**
		 * Description courte du shell
		 *
		 * @var string
		 */
		public $description = 'Le shell DatabaseWordsSearch permet de chercher des mots dans tous les champs texte de la base de données.';

		/**
		 * Liste des sous-commandes et de leur description.
		 *
		 * @var array
		 */
		public $commands = array();

		/**
		 * Liste des options et de leur description.
		 *
		 * @var array
		 */
		public $options = array(
			'connection' => array(
				'short' => 'c',
				'help' => 'Le nom de la connection à la base de données',
				'default' => 'default',
			),
			'ignoreCase' => array(
				'short' => 'i',
				'help' => 'La recherche doit-elle être non sensible à la casse ?',
				'boolean' => true,
				'default' => 'false'
			)
		);

		/**
		 * La connexion vers la base de données.
		 *
		 * @var DboSource
		 */
		public $Dbo = null;

		/**
		 * La longueur minimale du mot à rechercher.
		 *
		 * @var integer
		 */
		public $minLength = PHP_INT_MAX;

		/**
		 * Démarrage du shell
		 */
		public function startup() {
			parent::startup();

			$this->params['connection'] = ( isset( $this->params['connection'] ) ? $this->params['connection'] : 'default' );

			try {
				$this->Dbo = ConnectionManager::getDataSource( $this->params['connection'] );
			} catch( Exception $Exception ) {
				 $this->log( $Exception->getMessage(), LOG_ERR );
			}

			if( !is_a( $this->Dbo, 'DataSource' ) || !$this->Dbo->connected ) {
				$this->err( "Impossible de se connecter avec la connexion {$this->params['connection']}" );
				$this->_stop( self::ERROR );
				return;
			}

			if( !( $this->Dbo instanceof Postgres ) ) {
				$this->err( "La connexion {$this->params['connection']} n'utilise pas le driver postgres" );
				$this->_stop( self::ERROR );
				return;
			}

			if( 0 === count( $this->args ) ) {
				$this->err( "Vous devez renseigner au moins un mot à rechercher." );
				$this->_stop( self::ERROR );
			}

			foreach($this->args as $arg) {
				$length = strlen( $arg );
				if( $this->minLength > $length ) {
					$this->minLength = $length;
				}
			}
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			$tables = $this->Dbo->listSources();
			sort( $tables );

			foreach( $tables as $tableName ) {
				$this->out( sprintf( "<info>Analyse de la table %s</info>", $tableName ) );

				$modelName = Inflector::classify( $tableName );
				$model = ClassRegistry::init(
					array(
						'class' => $modelName,
						'table' => $tableName,
						'ds' => $this->params['connection']
					)
				);
				$model->Behaviors->attach( 'DatabaseTable' );

				$fields = $model->schema();
				$targets = array();

				foreach( $fields as $fieldName => $params ) {
					if( true === in_array( $params['type'], array( 'string', 'text' ) ) && $this->minLength <= $params['length'] ) {
						$targets[] = $fieldName;
					}
				}

				if( false === empty( $targets ) ) {
					$query = array(
						'fields' => $model->fields(),
						'contain' => false,
						'conditions' => array( 'OR' => array() )
					);

					foreach( $targets as $target ) {
						$or = array();
						foreach( $this->args as $word ) {
							$operator = true === $this->params['ignoreCase'] ? 'ILIKE' : 'LIKE';
							$condition = array( "{$modelName}.{$target} {$operator}" => "%{$word}%" );
							$query['conditions']['OR'][] = $condition;
							$or[] = $condition;
						}
						$condition = $this->Dbo->conditions( array( 'OR' => $or ), true, false, $model);
						$query['fields'][] = "{$condition} AS \"In__{$target}\"";
					}
					$results = $model->find( 'all', $query );

					if( false === empty( $results ) ) {
						$targets = Hash::filter( Hash::extract( $results, '{n}.In' ) );
						$targets = array_keys( call_user_func_array( 'array_merge', $targets ) );
						$this->out( sprintf( "\t<success>%d enregistrement(s) trouvé(s)</success> dans le(s) champ(s) <success>%s</success> de la table <success>%s</success>", count( $results ), implode( ', ', $targets ), $tableName ) );
						$this->out( $model->sq( $query ) );
					}
					else {
						$this->out( sprintf( "\taucun enregistrement trouvé dans la table <info>%s</info>", $tableName ) );
					}
				}
				else {
					$this->out( sprintf( "\taucun champ à analyser dans la table <info>%s</info>", $tableName ) );
				}
			}

			$this->_stop( self::SUCCESS );
		}

		/**
		 * Paramétrages et aides du shell.
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();

			$Parser->description( $this->description );
			$Parser->addSubcommands( $this->commands );
			$Parser->addOptions( $this->options );

			return $Parser;
		}
	}
?>