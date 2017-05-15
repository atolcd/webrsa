<?php
	/**
	 * Code source de la classe CheckmodelsShell.
	 *
	 * PHP 5.3
	 *
	 * @package Pgsqlcake
	 * @subpackage Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses( 'ConnectionManager', 'Model' );
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe CheckmodelsShell permet de vérifier si tout est cohérent entre
	 * les relation entre les tables matérialisées par des clés étrangères, les
	 * liaisons des modèles et les champs se terminant par _id.
	 *
	 * @package Pgsqlcake
	 * @subpackage Console.Command
	 */
	class CheckmodelsShell extends XShell
	{

		/**
		 *
		 */
		public $uses = array( );

		/**
		 *
		 */
		public $showSuccess = false;

		/**
		 *
		 */
		protected $_connections = array( );

		/**
		 *
		 */
		protected $_dbos = array( );

		/**
		 *
		 */
		protected $_tables = array( );

		/**
		 *
		 * @var type
		 */
		public $output;

		/**
		 * @see DboSource::fullTableName
		 */
		protected function _modelTable( $modelName ) {
			$file = APP.'Model'.DS.$modelName.'.php';
			if( !file_exists( $file ) ) {
				return false;
			}

			App::uses( $modelName, 'Model' );
			$reflection = new ReflectionClass( $modelName );
			$properties = $reflection->getDefaultProperties();

			if( $properties['useTable'] === false ) {
				return false;
			}
			else if( $properties['useTable'] === null ) {
				$properties['useTable'] = Inflector::tableize( $modelName );
			}

			if( !in_array( $properties['useDbConfig'], $this->_connections ) ) {
				$this->output[] = "La connection {$properties['useDbConfig']} n'est pas définie.";
				$this->output[] = '';
				return false;
			}

			if( !isset( $this->_dbos[$properties['useDbConfig']] ) ) {
				$this->_dbos[$properties['useDbConfig']] = ConnectionManager::getDataSource( $properties['useDbConfig'] );
				$this->_tables[$properties['useDbConfig']] = $this->_dbos[$properties['useDbConfig']]->listSources();
			}

			$tableName = $this->_dbos[$properties['useDbConfig']]->config['prefix'].$properties['useTable'];

			if( !in_array( $tableName, $this->_tables[$properties['useDbConfig']] ) ) {
				$this->output[] = "La table {$tableName} n'est pas présente pour la connection {$properties['useDbConfig']}.";
				$this->output[] = "";
				return false;
			}

			return $tableName;
		}

		/**
		 *
		 */
		public function startup() {
			parent::startup();
			$this->_connections = array_keys( ConnectionManager::enumConnectionObjects() );
		}

		/**
		 * FIXME:
		 * 	1°) faire des fonctions
		 * 	2°) quels sont les champs _id qui n'ont pas de fk ou pas de relation
		 * 	3°) quels sont les modèles qui devraient avoir une table et qui n'en ont pas
		 * 	4°) quelles sont les tables qui n'ont pas de modèles
		 * 	5°) intégrer les autres classes typiquement postgres dans le plugin (Pgsqlcake.PgsqlSchema)
		 */
		protected function _check( $modelName ) {

			$error = false;

			$this->output[] = "Analyse du modèle ".$modelName;

			$model = ClassRegistry::init( $modelName );
			$model->Behaviors->attach( 'Pgsqlcake.PgsqlSchema' );
			$foreignKeysTo = $model->foreignKeysTo();
			$foreignKeysFrom = $model->foreignKeysFrom();

			// 1°) TODO: foreignKeysFrom
			$innerError = false;
			$this->output[] = "\tMatérialisation des clés étrangères";
			foreach( $foreignKeysTo as $fk ) {
				$fkModel = Inflector::classify( $fk['From']['table'] );
				if( !isset( $model->{$fkModel} ) ) {
					$this->output[] = "\t\tLa clé étrangère entre le modèle ".$model->alias." et le modèle ".$fkModel." (".$fk['From']['table'].".".$fk['From']['column'].") n'est pas matérialisée par une relation dans le modèle ".$model->alias.".";
					$error = $innerError = true;
				}
			}
			if( !$innerError ) {
				$this->output[] = "<success>\t\tOK</success>";
			}

			$fkColumnsFrom = Set::extract( '/From/column', $foreignKeysFrom );
			$fkTablesTo = Set::extract( '/From/table', $foreignKeysTo );

			// 2°)
			$innerError = false;
			$this->output[] = "\tMatérialisation des relations du modèle";
			$associations = $model->getAssociated();

			foreach( $associations as $assocModel => $assocType ) {
				switch( $assocType ) {
					case 'belongsTo':
						$assoc = $model->belongsTo[$assocModel];
						if( !empty( $assoc['foreignKey'] ) && !in_array( $assoc['foreignKey'], $fkColumnsFrom ) ) {
							$this->output[] = "<error>\t\tLa relation entre le modèle ".$model->alias." et le modèle ".$assocModel." (".$model->useTable.".".$assoc['foreignKey'].") n'est pas matérialisée par une clé étrangère au niveau de la base de données.</error>";
							$error = $innerError = true;
						}
						break;
					case 'hasOne':
						$assoc = $model->hasOne[$assocModel];
						if( !empty( $assoc['foreignKey'] ) && !in_array( $model->{$assocModel}->useTable, $fkTablesTo ) ) {
							$this->output[] = "<error>\t\tLa relation entre le modèle ".$model->alias." et le modèle ".$assocModel." (".$model->{$assocModel}->useTable.".".$assoc['foreignKey'].") n'est pas matérialisée par une clé étrangère au niveau de la base de données.<.error>";
							$error = $innerError = true;
						}
						break;
					case 'hasMany':
						$assoc = $model->hasMany[$assocModel];
						if( !empty( $assoc['foreignKey'] ) && !in_array( $model->{$assocModel}->useTable, $fkTablesTo ) ) {
							$this->output[] = "<error>\t\tLa relation entre le modèle ".$model->alias." et le modèle ".$assocModel." ( ".$model->{$assocModel}->useTable.".".$assoc['foreignKey'].") n'est pas matérialisée par une clé étrangère au niveau de la base de données.</error>";
							$error = $innerError = true;
						}
						break;
					case 'hasAndBelongsToMany':
						$assoc = $model->hasAndBelongsToMany[$assocModel];
						if( !empty( $assoc['associationForeignKey'] ) && !in_array( $model->{$assoc['with']}->useTable, $fkTablesTo ) ) {
							$this->output[] = "<error>\t\tLa relation entre le modèle ".$model->alias." et le modèle ".$assoc['with']." (".$model->{$assocModel}->useTable.".".$assoc['associationForeignKey'].") n'est pas matérialisée par une clé étrangère au niveau de la base de données.</error>";
							$error = $innerError = true;
						}
						break;
					default:
						die( $assocModel );
				}
			}
			if( !$innerError ) {
				$this->output[] = "<success>\t\tOK</success>";
			}
		}

		/**
		 *
		 */
		protected function _modelsList( $accepted = array( ) ) {
			$models = array( );
			$dirName = sprintf( '%sModel'.DS, APP );
			$dir = opendir( $dirName );
			while( ( $file = readdir( $dir ) ) !== false ) {
				$explose = explode( '~', $file );
				if( ( count( $explose ) == 1 ) && (!is_dir( $dirName.$file ) ) && (!in_array( $file, array( 'empty', '.svn' ) ) ) ) {
					$model = Inflector::classify( preg_replace( '/\.php$/', '', $file ) );
					if( empty( $accepted ) || in_array( $model, $accepted ) ) {
						$models[] = $model;
					}
				}
			}
			closedir( $dir );
			sort( $models );

			return $models;
		}

		/**
		 *
		 */
		public function main() {
			$models = $this->_modelsList( $this->args );
			if( !empty( $models ) ) {
				$this->XProgressBar->start( count( $models ) );
				foreach( $models as $modelName ) {
					$this->XProgressBar->next( 1, '<info>Modèle en cours d\'analyse : </info><important>'.$modelName.'</important>' );
					$table = $this->_modelTable( $modelName );

					if( !empty( $table ) ) {
						$this->_check( $modelName );
					}
				}
			}
			$this->out( $this->output );
			$this->hr();
		}

	}
?>