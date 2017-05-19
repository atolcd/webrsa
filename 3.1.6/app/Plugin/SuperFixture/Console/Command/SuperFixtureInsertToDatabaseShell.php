<?php
	/**
	 * Code source de la classe SuperFixtureInsertToDatabaseShell.
	 *
	 * @package app.Console.Command
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Shell.php.
	 */

	App::uses('XShell', 'Console/Command');
	
	/**
	 * La classe SuperFixtureInsertToDatabaseShell permet de peupler une base de donnée avec une SuperFixtures
	 * 
	 * Exemple de procédure (Note: aros et aros_acos peuplé par super fixtures):
	 * CREATE DATABASE "demo" WITH OWNER "webrsa" ENCODING 'UTF8';
	 * Export de la structure de la base 'trunk' et import de cette derniere dans 'demo'
	 * Modification du fichier app/Config/database.php -> 'database' => 'demo'
	 * sudo -u www-data lib/Cake/Console/cake SuperFixture.BakeSuperFixture ~/workspace/webrsa/app/Vendor/BakeSuperFixture/DossierBaker.php ~/workspace/webrsa/app/Test/SuperFixture/BaseTest.php
	 * sudo -u www-data lib/Cake/Console/cake SuperFixture.SuperFixtureInsertToDatabase ~/workspace/webrsa/app/Test/SuperFixture/BaseTest.php
	 * sudo -u www-data lib/Cake/Console/cake PermissionsDeveloppement
	 * 
	 * Pour reconstruire la base plus rapidement :
	 * sudo -u www-data lib/Cake/Console/cake SuperFixture.SuperFixtureInsertToDatabase app/Test/SuperFixture/BaseTest.php --force -a
	 * 
	 * @package app.Console.Command
	 */
	class SuperFixtureInsertToDatabaseShell extends XShell
	{
		/**
		 * Tâches utilisées par ce shell.
		 *
		 * @var array
		 */
		public $tasks = array('ProgressBar');
		
		/**
		 * @var SuperFixture
		 */
		public $SuperFixture = null;
		
		/**
		 * Méthode principale.
		 */
		public function main() {
			if (!$this->params['force']) {
				$this->out();
				$this->out("\t\t<error>Attention</error>, cette action va écraser les données dans la base "
					. "'<warning>{$this->connection->config['database']}</warning>'.", 2);
				$in = $this->in("Voulez-vous continuer ?", array('o', 'n'));

				if ($in === 'n') {
					exit;
				}
			}
			
			if (empty($this->args[0])) {
				$this->out();
				$this->out("Indiquez le chemin vers la super fixture à utiliser\n(note: vous pouvez utiliser le premier paramètre de la fonction pour indiquer le chemin)\nExemple:");
				$in = $this->in(TESTS.'SuperFixture'.DS);
			} else {
				$in = $this->args[0];
			}
			
			$this->_isSuperFixtureOrDie($in);
			$superFixtureDatas = $this->SuperFixture->getData();
			
			// Une super fixture peut être très lourde, les informations sont dans $superFixtureDatas donc on libère la mémoire
			unset($this->SuperFixture);
			
			// Nombre de lignes à insérer
			$count = 0;
			foreach ($superFixtureDatas as $datas) {
				$count += count($datas);
			}
			
			$this->connection->begin();
			
			if ($this->params['truncate-all'] || $this->params['autoincrement'] || $this->params['delete-all']) {
				$notEmptyTables = $this->_resetEmptyTables();
			}
			
			if ($this->params['truncate-all'] || $this->params['delete-all']) {
				$this->_truncate($notEmptyTables, $this->params['delete-all'] ? 'delete' : 'truncate');
			}
			
			$this->ProgressBar->start($count);
			
			foreach ($superFixtureDatas as $modelName => $datas) {
				$Model = ClassRegistry::init($modelName);
				$this->ProgressBar->next(
					$c = count($datas),
					"\t<success>".$modelName."</success> : inserting <info>".$c."</info> row".($c > 1 ? 's' : '')
				);
				
				if (!$this->params['truncate-all'] && !$this->params['delete-all']) {
					// Recontruit la table
					$Model->deleteAll(array('1 = 1'));
					$Model->query("ALTER SEQUENCE {$Model->useTable}_{$Model->primaryKey}_seq RESTART WITH 1;");
				}
				
				foreach ($datas as $data) {
					$Model->create($data);
					$Model->save(null, array('validate' => false, 'callbacks' => false)) or die;
				}
			}
			
			$this->out();
			$this->out('<success>'.$count." lignes ajouté avec succès</success>");
			$this->connection->commit();
		}
		
		/**
		 * Vérifi l'existance du fichier indiqué et sa conformité
		 * Place l'instance de la super fixture dans $this->SuperFixture
		 * 
		 * @param string $in
		 * @throws Exception
		 */
		protected function _isSuperFixtureOrDie($in) {
			if (!is_file($in) || is_dir($in) || !preg_match('/\\'.DS.'([^\\'.DS.']+)\.php/', $in, $match))  {
				throw new Exception("Aucun fichier n'a été trouvé sur '$in'");
			}
			
			require $in;

			if (class_exists($match[1])) {
				// Note : On doit instancier la SuperFixture pour vérifier ses interfaces, 
				// on aura besoin de cette instance plus tard
				$this->SuperFixture = new $match[1]();
				
				$interfaces = class_implements($this->SuperFixture);
				
				if (!in_array('SuperFixtureInterface', $interfaces)) {
					throw new Exception("La class {$match[1]} doit implémenter "
					. "'SuperFixtureInterface' pour être utilisable par ce shell");
				}
			} else {
				throw new Exception("Le nom de class doit correspondre au nom du fichier dans '$in'");
			}
		}
		
		/**
		 * Ajoute des options :
		 * 
		 * --force : Supprime les avertissements, ne pose pas de questions
		 * --truncate-all : Supprime toutes les données de la base.
		 * 
		 * @return ConsoleOptionParser
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			
			$parser->addOption('force', array(
				'help' => 'Supprime les avertissements, ne pose pas de questions',
				'boolean' => true,
			));
			
			$parser->addOption('autoincrement', array(
				'short' => 'a',
				'help' => 'Remet les bases vide à autoincrement 1.',
				'boolean' => true,
			));
			
			$parser->addOption('truncate-all', array(
				'help' => 'Supprime toutes les données de la base et remet la table à neuf (bien plus long que delete-all)',
				'boolean' => true,
			));
			
			$parser->addOption('delete-all', array(
				'help' => 'Supprime toutes les données de la base.',
				'boolean' => true,
			));
			
			return $parser;
		}
		
		/**
		 * Scan la base de donnée. Si une table est vide, l'autoincrement est remis à 1
		 * 
		 * @return array tables non vide
		 */
		protected function _resetEmptyTables() {
			$tables = Hash::extract(
				$this->connection->query("SELECT tablename AS \"Table__name\" "
					. "FROM pg_tables WHERE schemaname = 'public' "
					. "AND tablename NOT LIKE 'tmp_%' "
					. "AND tablename NOT IN ('acos', 'aros', 'aros_acos')"),
				'{n}.Table.name'
			);
			
			$this->ProgressBar->start($c1 = count($tables));
			
			$notEmptyTable = array();
			foreach ($tables as $tablename) {
				$data = $this->connection->query("SELECT * FROM $tablename LIMIT 1");
				
				if (!empty($data)) {
					$this->ProgressBar->next(1, "\tscanned : <success>".$tablename."</success>");
					$notEmptyTable[] = $tablename;
				} else {
					$this->ProgressBar->next(1, "\tscanned : <success>".$tablename."</success> resetting Autoincrement...");
					$this->_resetAutoincrement($tablename);
				}
			}
			
			$c2 = count($notEmptyTable);
			$this->out("\n$c1 tables scannées, $c2 tables non vide", 2);
			
			return $notEmptyTable;
		}
		
		/**
		 * Supprime les données d'une base de donnée qui sont situé dans le schema 'public'
		 * 
		 * @params array $notEmptyTable
		 * @params string $method 'truncate' ou 'delete'
		 */
		protected function _truncate(array $notEmptyTable, $method = 'truncate') {
			$this->out("\n".'<warning>Suppression de tout les enregistrements en cours sauf '
				. '</warning><success>acos aros et aros_acos</success><warning>... (peut prendre plusieurs minutes)'
				. '</warning> Base de donnée: <warning>'.$this->connection->config['database'].'</warning>', 2);
			
			$this->ProgressBar->start($c = count($notEmptyTable));
			
			foreach ($notEmptyTable as $tablename) {
				if ($method === 'truncate') {
					$this->ProgressBar->next(1, "\ttruncating : <success>".$tablename."</success>");
					$this->connection->query('TRUNCATE TABLE public.'.$tablename.' RESTART IDENTITY CASCADE');
				} else {
					$modelName = Inflector::camelize(Inflector::singularize($tablename));
					$Model = ClassRegistry::init($modelName);
					
					$this->ProgressBar->next(1, "\tdeleting data : <success>".$tablename."</success>");
					$Model->deleteAll(array('1 = 1'));
					$Model->query("ALTER SEQUENCE {$Model->useTable}_{$Model->primaryKey}_seq RESTART WITH 1;");
				}
			}
			
			$this->out("\nSuppression des données de $c tables effectué avec succès", 2);
		}
		
		/**
		 * Remet à zéro la valeur de l'autoincrement en se basant sur les standards de nommage
		 * 
		 * NOTE : Incompatible avec les sequences customisés car n'invoque par le Modèle par soucis de performances
		 * 
		 * @param string $tablename
		 */
		protected function _resetAutoincrement($tablename, $restartWith = 1) {
			$primarykey = Hash::get(
				$this->connection->query("SELECT b.attname AS \"Table__primarykey\" "
					. "FROM pg_index a "
					. "JOIN pg_attribute b ON b.attrelid = a.indrelid AND b.attnum = ANY(a.indkey) "
					. "WHERE a.indrelid = '$tablename'::regclass " 
					. "AND a.indisprimary "
					. "LIMIT 1;"),
				'0.Table.primarykey'
			);
			
			if ($primarykey) {
				$this->connection->query("ALTER SEQUENCE {$tablename}_{$primarykey}_seq RESTART WITH $restartWith;");
			}
		}
	}