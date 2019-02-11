<?php
	/**
	 * Code source de la classe FichiersmodulesShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppShell', 'Console/Command' );
	App::uses( 'Translator', 'Translator.Utility' );
	require_once  APPLIBS.'cmis.php' ;

	/**
	 * La classe FichiersmodulesShell ...
	 *
	 * @package app.Console.Command
	 */
	class FichiersmodulesShell extends AppShell
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
		public $description = array(
			"Shell de maintenance des fichiers liés aux enregistrements de l'application.",
			"Les actions de maintenance sont:",
			"- récupération d'informations concernant les enregistrements liés (dans tous les cas)",
			"- correction des enregistrements métiers dont le statut « avec / sans fichier lié » est faux (avec l'option --repair)",
			"- suppression en base de données et sur serveur CMIS des fichiers liés orphelins (avec l'option --repair)",
		);

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
			'log' => array(
				'short' => 'l',
				'help' => 'Exporte un fichier de log CSV avec le nombre de résultats pour chaque module ainsi qu\'une liste de chemins CMIS qui n\'ont pu être supprimés.',
				'choices' => array( 'true', 'false' ),
				'default' => 'true'
			),
			'repair' => array(
				'short' => 'r',
				'help' => 'Effectue les corrections lorsque c\'est nécessaire.',
				'boolean' => true,
				'default' => 'false'
			),
			'sql' => array(
				'short' => 's',
				'help' => 'Affiche les requêtes SQL effectuées.',
				'boolean' => true,
				'default' => 'false'
			),
			'test' => array(
				'short' => 't',
				'help' => 'Annule la transaction pour que les corrections ne soient pas effectives.',
				'boolean' => true,
				'default' => 'false'
			)
		);

		/**
		 * Modèles utilisés par ce shell.
		 *
		 * @var array
		 */
		public $uses = array( 'Fichiermodule' );

		/**
		 * Alias entre les noms des modèles CakePHP et le nom des tables
		 * correspondantes.
		 *
		 * @var array
		 */
		public $aliases = array( 'Fichiermodule' => 'fichiersmodules' );

		/**
		 * Correspondances entre le nom du module stocké dans Fichiermodule et
		 * le nom du modèle CakePHP correspondant.
		 *
		 * @var array
		 */
		public $correspondances = array( 'Dsp' => 'DspRev' );

		/**
		 * Le DataSource lié aux fichiers liés.
		 *
		 * @var DataSource
		 */
		public $Dbo = null;

		/**
		 * Liste des chemins CMS orphelins à supprimer à la fin de la transaction
		 * le cas échéant.
		 *
		 * @var array
		 */
		protected $_cmspaths = array();

		/**
		 * Le chemin vers le fichier de log CSV des métriques pour les différents
		 * modules.
		 *
		 * @var string
		 */
		protected $_csvLogFile = null;

		/**
		 * Le chemin vers le fichier de log des chemins CMIS qui n'ont pu être
		 * supprimés.
		 *
		 * @var string
		 */
		protected $_cmisErrorsLogFile = null;

		/**
		 * Démarrage et configuration du shell.
		 */
		public function startup() {
			parent::startup();

			// Base de données
			$this->Dbo = $this->Fichiermodule->getDataSource();
			$this->Dbo->fullDebug = true;
			$this->out( sprintf( '<info>Base de données:</info> %s', $this->Dbo->config['database'] ) );

			// Traductions
			$shell = Inflector::underscore( $this->name );
			$model = Inflector::underscore( Inflector::classify( $this->name ) );
			$suffix = rtrim('_' . Configure::read('WebrsaTranslator.suffix'), '_');
			Translator::domains(
				array(
					$shell.$suffix,
					$shell,
					$model.$suffix,
					$model,
					'default'.$suffix,
					'default',
				)
			);

			// Journalisation
			$this->params['log'] = 'true' === $this->params['log'];

			$date = date( 'Ymd-His' );
			$database = $this->Fichiermodule->getDataSource()->config['database'];
			$this->_csvLogFile = LOGS.$this->name.'-'.$database.'-metriques-'.$date.'.csv';
			$this->_cmisErrorsLogFile = LOGS.$this->name.'-'.$database.'-erreurs_cmis-'.$date.'.log';

			// Configuration CMIS si l'on répare sans être en mode test
			if( true === $this->params['repair'] && false === $this->params['test'] ) {
				$config = (array)Configure::read( 'Cmis' );
				if(array() === $config) {
					$this->out( "<alert>Attention:</alert>" );
					$this->out( "- aucune connexion au serveur CMIS configurée" );
					$this->out();
				}
				else {
					$configured = Cmis::configured();

					$this->out( sprintf( '<info>Connexion CMIS:</info> %s', $configured ? 'oui' : 'non' ) );
					$this->out( sprintf( '<info>URL CMIS:</info> %s', $config['url'] ) );
					$this->out( sprintf( '<info>Chemin CMIS:</info> %s', $config['prefix'] ) );
					$this->hr();

					$this->out( "<alert>Attention</alert>" );
					$this->out( "- toute suppression sur le serveur CMIS sera <critical>irréversible</critical>, veuillez vérifier la configuration (URL et chemin) ci-dessus" );
					$this->out( sprintf( "- la configuration %s de se connecter au serveur CMIS", true === $configured ? '<success>permet</success>' : '<error>ne permet pas</error>' ) );
					$this->out();
				}

				$continue = $this->in( 'Souhaitez-vous continuer ?', array( 'O', 'N' ), 'N' );
				if( 'o' !== mb_convert_case( $continue, MB_CASE_LOWER ) ) {
					$this->_stop( self::SUCCESS );
				}
				$this->hr();
			}
		}

		/**
		 * Retourne la liste des modules utilisés dans les fichiers liés, triés
		 * par ordre alphabétique.
		 *
		 * @return array
		 */
		protected function _modules() {
			$query = array(
				'fields' => array( 'Fichiermodule.modele' ),
				'contain' => false,
				'order' => array( 'Fichiermodule.modele' ),
				'group' => array( 'Fichiermodule.modele' )
			);
			$modules = $this->Fichiermodule->find( 'all', $query );
			return Hash::extract( $modules, '{n}.Fichiermodule.modele' );
		}

		/**
		 * Retourne le nom de modèle CakePHP utilisé dans le cadre d'un module.
		 *
		 * @param string $module Le nom du module.
		 */
		protected function _className( $module ) {
			if( true === isset( $this->correspondances[$module] ) ) {
				$className = $this->correspondances[$module];
			}
			else {
				$className = $this->Fichiermodule->belongsTo[$module]['className'];
			}

			return $className;
		}

		/**
		 * Retourne un querydata permettant de cibler les enregistrements dont la
		 * valeur de la colonne "haspiecejointe" est erroné par-rapport au nombre
		 * de fichiers réellement liés à l'enregistrement.
		 *
		 * @param string $module Le module à traiter
		 * @param boolean $haspiecejointe Le statut "haspiecejointe" que l'on souhaite corriger
		 * @return array
		 */
		protected function _wrongHasPieceJointeQuery( $module, $haspiecejointe = null ) {
			$className = $this->_className( $module );

			$query = array(
				'alias' => 'fichiersmodules',
				'fields' => array( 'Fichiermodule.fk_value' ),
				'contain' => false,
				'conditions' => array(
					'Fichiermodule.modele' => $module,
					"Fichiermodule.fk_value = {$className}.id"
				)
			);

			$exists = $this->Fichiermodule->sq( alias( $query, $this->aliases ) );

			$case = "( CASE WHEN EXISTS( {$exists} ) THEN '1' ELSE '0' END )";

			$query = array(
				'fields' => array(
					"{$className}.id",
					"{$case} {$this->Dbo->alias} {$this->Dbo->startQuote}{$className}__target{$this->Dbo->endQuote}"
				),
				'conditions' => array(
					"{$className}.haspiecejointe <> {$case}"
				)
			);

			if( null !== $haspiecejointe ) {
				$query['conditions']["{$className}.haspiecejointe"] = (bool)$haspiecejointe ? '1' : '0';
			}

			return $query;
		}

		/**
		 * Retourne un querydata permettant de trouver les enregistrements des
		 * fichiers liés orphelins, c'est-à-dire dont l'enregistrement auquel
		 * ils sont liés n'existe plus.
		 *
		 * @param string $module Le module à traiter
		 * @return array
		 */
		protected function _orphansQuery( $module ) {
			$className = $this->_className( $module );
			$tableName = Inflector::tableize( $className );

			$subQuery = array(
				'alias' => $tableName,
				'fields' => array( "{$tableName}.{$this->Fichiermodule->{$className}->primaryKey}" ),
				'conditions' => array(
					"{$tableName}.{$this->Fichiermodule->{$className}->primaryKey} = Fichiermodule.fk_value"
				),
				'contain' => false
			);
			$sql = $this->Fichiermodule->{$className}->sq( $subQuery );

			// Détection
			$query = array(
				'fields' => array( 'Fichiermodule.id' ),
				'conditions' => array(
					'Fichiermodule.modele' => $module,
					"Fichiermodule.fk_value NOT IN ( {$sql} )"
				),
				'contain' => false
			);

			return $query;
		}

		/**
		 * Transforme un querydata en requête SQL.
		 *
		 * @param Model $Model Le modèle sur lequel exécuter le querydata
		 * @param array $query Le querydata
		 * @return string
		 */
		protected function _queryToSql( Model $Model, array $query ) {
			$alias = $Model->alias;
			$primaryKey = $Model->primaryKey;

			return preg_replace(
				"/^SELECT +(\"{$alias}\"\.\"{$primaryKey}\") +AS +\"{$alias}__{$primaryKey}\" +/",
				'SELECT \1 ',
				$Model->sq(
					array(
						'fields' => array( "{$alias}.{$primaryKey}" )
					) + $query
				)
			).';';
		}

		/**
		 * Retourne un array contenant:
		 *	- le querydata
		 *	- le querydata transformé en requête SQL
		 *	- le nombre de résultats retournés par l'exécution du querydata
		 *
		 * @param Model $Model Le modèle sur lequel exécuter le querydata
		 * @param array $query Le querydata à exécuter
		 * @return array
		 */
		protected function _query(Model $Model, array $query) {
			return array(
				'query' => $query,
				'sql' => $this->_queryToSql( $Model, $query ),
				'results' => $Model->find( 'count', $query )
			);
		}

		/**
		 * Retourne un array contenant les métriques d'un module:
		 *	- enregistrements du module
		 *	- enregistrements du module ayant des fichiers liés
		 *	- fichiers liés au module
		 *	- enregistrements du module avec une valeur de 0 pour haspiecejointe erronée
		 *	- enregistrements du module avec une valeur de 1 pour haspiecejointe erronée
		 *	- fichiers liés au module orphelins
		 *
		 * @param string $module
		 * @return array
		 */
		protected function _queries($module) {
			$result = array();
			$modelClass = $this->_className( $module );

			// Nombre d'enregistrements du module
			$query = array( 'contain' => false );
			$result['count'] = $this->_query( $this->Fichiermodule->{$modelClass}, $query );

			// Nombre d'enregistrements du module ayant des fichiers liés
			$query = array(
				'contain' => false,
				'conditions' => array( 'Fichiermodule.modele' => $modelClass ),
				'group' => array( 'Fichiermodule.fk_value' )
			);
			$result['linked'] = $this->_query( $this->Fichiermodule, $query );

			// Nombre de fichiers liés au module
			$query = array(
				'contain' => false,
				'conditions' => array( 'Fichiermodule.modele' => $modelClass )
			);
			$result['files'] = $this->_query( $this->Fichiermodule, $query );

			// Nombre d'enregistrements du module avec 0 erroné
			$query = $this->_wrongHasPieceJointeQuery( $module, false );
			$result['error_has_0'] = $this->_query( $this->Fichiermodule->{$modelClass}, $query );

			// Nombre d'enregistrements du module avec 1 erroné
			$query = $this->_wrongHasPieceJointeQuery( $module, true );
			$result['error_has_1'] = $this->_query( $this->Fichiermodule->{$modelClass}, $query );

			// Nombre de fichiers liés orphelins
			$query = $this->_orphansQuery( $module );
			$result['error_orphans'] = $this->_query( $this->Fichiermodule, $query );

			return $result;
		}

		/**
		 * Retourne la sous-requête permettant de savoir si un enregistrement
		 * existe dnas la table fichiersmodules pour un module donné.
		 *
		 * @param string $module Le nom du module.
		 */
		protected function _existsSubQuery( $module ) {
			$className = $this->_className( $module );

			$existsQuery = array(
				'alias' => 'fichiersmodules',
				'fields' => array( 'Fichiermodule.fk_value' ),
				'contain' => false,
				'conditions' => array(
					'Fichiermodule.modele' => $module,
					"Fichiermodule.fk_value = {$className}.id"
				)
			);

			return $this->Fichiermodule->sq( alias( $existsQuery, $this->aliases ) );
		}

		/**
		 * Corrige les enregistrements dont la valeur de haspiecejointe est
		 * erronée.
		 *
		 * @param string $module
		 * @param array $params
		 * @return false|integer
		 */
		protected function _repairHas( $module, array $params ) {
			$query = $params['query'];
			$className = $this->_className( $module );

			$exists = $this->_existsSubQuery( $module );
			$case = "( CASE WHEN EXISTS( {$exists} ) THEN '1' ELSE '0' END )";

			$alias = "{$this->Dbo->startQuote}{$className}{$this->Dbo->endQuote}";
			$params = array(
				'alias' => $alias,
				'joins' => null,
				'table' => $this->Dbo->fullTableName( $this->Fichiermodule->{$className} ),
				'fields' => str_replace(
						"{$alias}.{$this->Dbo->startQuote}haspiecejointe{$this->Dbo->endQuote}", "{$this->Dbo->startQuote}haspiecejointe{$this->Dbo->endQuote}", $this->Dbo->conditions( "{$className}.haspiecejointe = {$case}", true, false )
				),
				'conditions' => $this->Dbo->conditions( $query['conditions'], true, true, $this->Fichiermodule->{$className} )
			);
			$sqlResolution = $this->Dbo->renderStatement( 'update', $params ).';';

			if( true === $this->params['sql'] ) {
				$this->out( sprintf( "\t\t<notice>SQL de correction:</notice> %s", $sqlResolution, 1, Shell::VERBOSE ) );
			}

			// Effacement des logs pour être certain de n'avoir que la requête d'UPDATE plus bas
			$this->Dbo->getLog();
			$result = false !== $this->Dbo->query( $sqlResolution );
			$this->out( sprintf( "\t\t<notice>Correction:</notice> %s", $result ? '<success>Oui</success>' : '<error>Non</error>'  ) );

			// Récupération des logs
			$logs = Hash::get( (array)$this->Dbo->getLog(), 'log' );
			$affected = $logs[count( $logs ) - 1]['affected'];

			if( true === $this->params['test'] ) {
				$msg = __n(
					"\t\t<notice>%d</notice> enregistrement aurait dû être corrigé",
					"\t\t<notice>%d</notice> enregistrements auraient dû être corrigés",
					$affected
				);
			}
			else {
				$msg = __n(
					"\t\t<notice>%d</notice> enregistrement corrigé",
					"\t\t<notice>%d</notice> enregistrements corrigés",
					$affected
				);
			}
			$this->out( sprintf( $msg, $affected ) );

			return false === $result ? false : $affected;
		}

		/**
		 * Supprime les fichiers liés orphelins (qui sont liés à un enregistrement
		 * ayant disparu) au moyen de requêtes SQL pour éviter de passer dans
		 * les méthodes delete et deleteAll de la classe Fichiermodule afin que
		 * la suppression sur le serveur CMIS ne casse pas la transaction et
		 * peuple l'attribut _cmspaths avec les chemins CMIS pour tenter de les
		 * supprimer à la fin du shell.
		 *
		 * @see Fichiermodule::delete
		 * @see Fichiermodule::deleteAll
		 *
		 * @param string $module
		 * @param array $params
		 * @return boolean
		 */
		protected function _repairOrphans( $module, array $params ) {
			// CMS paths à sauvegarder
			$query = $params['query'];
			$query['fields'] = array( 'Fichiermodule.cmspath' );
			$query['conditions'][] = 'Fichiermodule.cmspath IS NOT NULL';
			$results = $this->Fichiermodule->find( 'all', $query );

			$cmspaths = Hash::extract( $results, '{n}.Fichiermodule.cmspath' );

			$this->_cmspaths = array_merge( $this->_cmspaths, $cmspaths );

			// Requête de suppression
			$alias = "{$this->Dbo->startQuote}{$this->Fichiermodule->alias}{$this->Dbo->endQuote}";
			$params = array(
				'alias' => $alias,
				'joins' => null,
				'table' => $this->Dbo->fullTableName( $this->Fichiermodule ),
				'fields' => null,
				'conditions' => $this->Dbo->conditions( $params['query']['conditions'], true, true, $this->Fichiermodule )
			);
			$sql = $this->Dbo->renderStatement( 'delete', $params ).';';
			$sql = preg_replace( "/DELETE +{$alias} +FROM/", 'DELETE FROM', $sql );

			if( true === $this->params['sql'] ) {
				$this->out( sprintf( "\t\t<notice>SQL de suppression:</notice> %s", $sql ) );
			}

			// Effacement des logs pour être certain de n'avoir que la requête de DELETE plus bas
			$this->Dbo->getLog();
			$result = false !== $this->Dbo->query( $sql );
			$this->out( sprintf( "\t\t<notice>Correction:</notice> %s", $result ? '<success>Oui</success>' : '<error>Non</error>'  ) );

			// Récupération des logs
			$logs = Hash::get( (array)$this->Dbo->getLog(), 'log' );
			$affected = $logs[count( $logs ) - 1]['affected'];

			if( true === $this->params['test'] ) {
				$msg = __n(
					"\t\t<notice>%d</notice> enregistrement aurait dû être supprimé",
					"\t\t<notice>%d</notice> enregistrements auraient dû être supprimés",
					$affected
				);
			}
			else {
				$msg = __n(
					"\t\t<notice>%d</notice> enregistrement supprimé",
					"\t\t<notice>%d</notice> enregistrements supprimés",
					$affected
				);
			}
			$this->out( sprintf( $msg, $affected ) );

			return false === $result ? false : $affected;
		}

		/**
		 * Exporte un fichier CSV comportant, pour chacun des modules, le nombre
		 * d'enregistrements affichés à l'écran au cours du traitement.
		 *
		 * @param array $queries
		 * @return boolean
		 */
		protected function _exportCsv( array $queries ) {
			$result = true;
			$output = array();

			$this->out();
			if(false === empty($queries)) {
				$this->out( "<info>Fichier de log CSV: </info>".'APP/'.preg_replace( '/^'.str_replace( '/', '\/', APP ).'/', '', $this->_csvLogFile ) );

				// En-tête
				$firstKey = Hash::get( array_keys( $queries ), '0' );
				$headers = array_keys( $queries[$firstKey] );
				$row = array(
					__m( 'module' ),
					__m( 'module_long' )
				);
				foreach($headers as $header) {
					$row[] = __m($header);
				}
				$output[] = str_putcsv( $row );

				foreach($queries as $module => $values) {
					$row = array(
						$module,
						__m( $module )
					);
					foreach($values as $value) {
						$row[] = $value['results'];
					}
					$output[] = str_putcsv( $row );
				}

				$result = file_put_contents( $this->_csvLogFile, implode( "\n", $output ) );
			}
			else {
				$this->out( "<info>Rien à écrire dans le fichier de log CSV</info>" );
			}

			return $result;
		}

		/**
		 * Exporte un fichier comportant les chemins CMIS qui n'ont pas pu être
		 * supprimés.
		 *
		 * @param array $errors
		 * @return boolean
		 */
		protected function _exportCmisErrors(array $errors) {
			$success = true;

			if(false === empty($errors)) {
				$this->out( "<info>Fichier de log des erreurs CMIS: </info>".'APP/'.preg_replace( '/^'.str_replace( '/', '\/', APP ).'/', '', $this->_cmisErrorsLogFile ) );
				$result = file_put_contents( $this->_cmisErrorsLogFile, implode( "\n", $errors ) );
			}
			else {
				$this->out( "<info>Rien à écrire dans le fichier de log des erreurs CMIS</info>" );
			}

			return $success;
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			$success = true;
			$affected = 0;
			$results = array();

			if( true === $this->params['repair'] ) {
				$this->Fichiermodule->begin();
			}

			$modules = $this->_modules();

			foreach( $modules as $module ) {
				$this->out( sprintf( "Traitement du module <info>%s</info> (%s)", __m( $module ), $module ) );
				$queries = $this->_queries($module);
				$results[$module] = $queries;

				foreach( $queries as $name => $query ) {
					if( false === strpos( $name, 'error_' ) ) {
						$directive = '%d';
					}
					else if( 0 === $query['results'] ) {
						$directive = '<success>%d</success>';
					}
					else {
						$directive = '<error>%d</error>';
					}

					$this->out( sprintf( "\t<info>%s</info>", __m($name) ) );
					$this->out( sprintf( "\t\t<info>nombre</info>: {$directive}", $query['results'] ) );

					if( true === $this->params['sql'] ) {
						$this->out( sprintf( "\t\t<info>SQL de détection</info>: %s", $query['sql'] ) );
					}

					if( true === $this->params['repair'] ) {
						if( 0 === strpos( $name, 'error_' ) ) {
							if('error_has_0' === $name) {
								$tmp = $this->_repairHas( $module, $query, '1' );
								$success = $success && false !== $tmp;
								$affected += false === $tmp ? 0 : $tmp;
							}
							else if('error_has_1' === $name) {
								$tmp = $this->_repairHas( $module, $query, '0' );
								$success = $success && false !== $tmp;
								$affected += false === $tmp ? 0 : $tmp;
							}
							else if( 'error_orphans' === $name ) {
								$tmp = $this->_repairOrphans( $module, $query );
								$success = $success && false !== $tmp;
								$affected += false === $tmp ? 0 : $tmp;
							}
						}
					}
					else if( 0 === strpos( $name, 'error_' ) ) {
						$affected += $query['results'];
					}
				}

				$this->out();
			}

			if( true === $this->params['repair'] ) {
				if( true === $success ) {
					if( true === $this->params['test'] ) {
						$message = __n(
							"<success>Succès, %d enregistrement aurait été corrigé (mode test)</success>",
							"<success>Succès, %d enregistrements auraient été corrigés (mode test)</success>",
							$affected
						);
						$message = sprintf( $message, $affected );
						$this->Fichiermodule->rollback();
					}
					else {
						$message = __n(
							"<success>Succès, %d enregistrement corrigé</success>",
							"<success>Succès, %d enregistrements corrigés</success>",
							$affected
						);
						$message = sprintf( $message, $affected );
						$this->Fichiermodule->commit();
					}
				}
				else {
					$message = __n(
						"<error>Erreur(s), %d enregistrement n'a pu être corrigé</error>",
						"<error>Erreur(s), %d enregistrements n'ont pu être corrigés</error>",
						$affected
					);
					$message = sprintf( $message, $affected );
					$this->Fichiermodule->rollback();
				}

				$this->out( $message );
			}
			else {
				$message = __n(
					"<success>Détection terminée, %d enregistrement à corriger</success>",
					"<success>Détection terminée, %d enregistrements à corriger</success>",
					$affected
				);
				$message = sprintf( $message, $affected );
				$this->out( $message );
			}

			// Suppression sur le serveur CMIS le cas échéant
			$cmisErrors = array();
			if( true === $success && true === $this->params['repair'] && false === $this->params['test'] ) {
				$this->out();
				$count = count( $this->_cmspaths );
				$this->out( sprintf( "<info>%d</info> fichiers à supprimer sur le serveur CMIS", $count ) );

				if( 0 < $count ) {
					sort( $this->_cmspaths );

					foreach( $this->_cmspaths as $cmspath ) {
						$tmp = Cmis::delete( $cmspath, true );
						if( false === $tmp ) {
							$cmisErrors[] = $cmspath;
						}
						$success = $tmp && $success;
					}

					$count = count( $cmisErrors );
					if( 0 === $count ) {
						$this->out( sprintf( "\t<success>%d</success> fichiers supprimés sur le serveur CMIS", $count ) );
					}
					else {
						$this->out( sprintf( "\t<error>%d</error> fichiers impossibles à supprimer sur le serveur CMIS", $count ) );
					}
				}
			}

			// Rapports CSV et erreurs CMIS
			if( true === $this->params['log'] ) {
				$success = $this->_exportCsv( $results ) && $success;
				if( true === $this->params['repair'] && false === $this->params['test'] ) {
					$success = $this->_exportCmisErrors( $cmisErrors ) && $success;
				}
			}

			$this->out();
			if(true === $success) {
				$msg = "<success>Shell terminé avec succès.</success>";
			}
			else {
				$msg = "<error>Shell terminé avec erreur(s).</error>";
			}
			$this->out( $msg );

			$this->_stop( true === $success ? self::SUCCESS : self::ERROR );
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