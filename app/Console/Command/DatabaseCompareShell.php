<?php
	/**
	 * Fichier source de la classe DatabaseCompareShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	
	App::uses('XShell', 'Console/Command');

	/**
	 * La classe DatabaseCompareShell
	 *
	 * @package app.Console.Command
	 */
	class DatabaseCompareShell extends XShell
	{
		/**
		 * Liste des options et de leurs valeurs par défaut
		 * 
		 * @var array
		 */
		public $options = array(
			'schema' => array(
				'short' => 's',
				'help' => 'Effectue la comparaison sur un shema uniquement. Mettre la valeur à [all] afin de ne pas limiter la comparaison',
				'default' => 'public'
			),
		);
		
		/**
		 * Génère un fichier .dot
		 */
		public function main() {
			$this->_createTmpDir();
			$scans = $this->_readTmpDir();
			
			if (!empty($scans) && count($scans) >= 2) {
				if (count($scans) === 2) {
					$continue = $this->in(
						sprintf(
							"Il existe exactement deux fichiers scans ('%s' et '%s'), voulez-vous les comparer ?",
							$scans[0],
							$scans[1]
						),
						array('O', 'n'),
						'O'
					);
					
					if (strtoupper($continue) !== 'O') {
						$this->_scanDatabase();
					} else {
						$this->_compare($scans[0], $scans[1]);
					}
				} else {
					$this->out("Veuillez choisir deux fichiers scans sur lesquels effectuer la comparaison ou bien tapez '<warning>scan</warning>'");
					$choixPossible = array();
					
					foreach ($scans as $scan) {
						$choixPossible[] = substr($scan, 0, strlen($scan) -5);
					}
					
					$choix1 = $this->in("", array_merge($choixPossible, array('scan')), $choixPossible[0]);
					
					if ($choix1 === 'scan') {
						return $this->_scanDatabase();
					}
					
					// On retire le choix de la liste
					foreach ($choixPossible as $key => $scan) {
						if ($scan === $choix1) {
							unset($choixPossible[$key]);
							break;
						}
					}
					sort($choixPossible);
					
					$choix2 = $this->in("", $choixPossible, $choixPossible[0]);
					$this->_compare($choix1.'.scan', $choix2.'.scan');
				}
			} else {
				$this->_scanDatabase();
			}
		}
		
		protected function _scanDatabase() {
			$continue = $this->in(
				sprintf(
					"La base de donnée '%s' est sur le point d'être scannée, ce qui peut prendre plusieurs minutes. Voulez vous continuer ?",
					$this->connection->config['database']
				),
				array('O', 'n'),
				'O'
			);
			
			if (strtoupper($continue) !== 'O') {
				exit;
			}
			
			$name = $this->in("Veuillez donner un nom au fichier scan qui va être généré :", null, $this->connection->config['database']);
			
			$schema = $this->_getSchema();
			
			$json = json_encode($schema);
			$schema['md5'] = md5($json);
			$json = json_encode($schema);
			
			if ($this->createFile(TMP.'DatabaseCompare'.DS.$name.'.scan', $json)) {
				$this->out(sprintf("Le fichier '%s' à été crée ete porte la signature '%s'", $name, $schema['md5']));
				$this->out("Afin d'effectuer une comparaison, il faut obtenir deux fichiers du même genre.");
				$this->out(sprintf("Il vous faut alors changer la base de donnée dans %s", APP.'Config'.DS.'database.php'));
			}
		}
		
		protected function _getSchema() {
			$results = $this->connection->query("SELECT * FROM information_schema.columns");
			$tables = array();
			
			foreach ($results as $infos) {
				// Si table temporaire, on ignore
				if (preg_match('/^tmp_.*_[\w]{6}$/', $infos[0]['table_name'])) {
					continue;
				}
				
				unset($infos[0]['table_catalog'], $infos[0]['ordinal_position'],
					$infos[0]['dtd_identifier'], $infos[0]['udt_catalog'],
					$infos[0]['domain_catalog']
				);
				$tables[$infos[0]['table_schema']][$infos[0]['table_name']]['fields'][$infos[0]['column_name']] = $infos[0];
				
				if (!isset($tables[$infos[0]['table_schema']][$infos[0]['table_name']]['constraints'])) {
					$tables[$infos[0]['table_schema']][$infos[0]['table_name']]['constraints'] = $this->_getContraints($infos[0]);
				}
			}
			
			return $tables;
		}
		
		protected function _getContraints($infos) {
			$results = $this->connection->query("SELECT 
				a.table_schema, a.table_name, a.constraint_type, a.constraint_name, b.consrc
				FROM information_schema.table_constraints a
				LEFT JOIN pg_catalog.pg_constraint b ON b.conname = a.constraint_name
				WHERE a.table_schema = '{$infos['table_schema']}'
				  AND a.table_name   = '{$infos['table_name']}'");
			
			$constraints = array();
			foreach ($results as $res) {
				$name = $res[0]['constraint_name'];
				
				// remplace un nom de contrainte "not null" généré par un nom fixe
				if (preg_match('/^[\d]+_[\d]+_[\d]+_not_null$/', $res[0]['constraint_name'])) {
					$res[0]['constraint_name'] = $res[0]['table_schema'].'_'.$res[0]['table_name'].'_not_null';
				}
				
				$constraints[$res[0]['constraint_name']] = $res[0];
			}
			
			return $constraints;
		}
		
		protected function _createTmpDir() {
			if (!is_dir(TMP.'DatabaseCompare')) {
				mkdir(TMP.'DatabaseCompare');
				chmod(TMP.'DatabaseCompare', 0777);
			}
		}
		
		protected function _readTmpDir() {
			$results = array();
			
			foreach (scandir(TMP.'DatabaseCompare') as $file) {
				if (!is_dir(TMP.'DatabaseCompare'.DS.$file) && strpos($file, '.scan')) {
					$results[] = $file;
				}
			}
			
			return $results;
		}
		
		protected function _compare($scan1, $scan2) {
			$this->name1 = substr($scan1, 0, strlen($scan1) -5);
			$this->name2 = substr($scan2, 0, strlen($scan2) -5);
			$scanData1 = json_decode(file_get_contents(TMP.'DatabaseCompare'.DS.$scan1), true);
			$scanData2 = json_decode(file_get_contents(TMP.'DatabaseCompare'.DS.$scan2), true);
			
			if ($scanData1['md5'] === $scanData2['md5']) {
				return array();
			}
			unset($scanData1['md5'], $scanData2['md5']);
			
			$missings1 = $this->_missings($scanData1, $scanData2);
			$missings2 = $this->_missings($scanData2, $scanData1);
			
			$this->_renderReport($missings1, $missings2);
		}
		
		protected function _missings($scanData1, $scanData2) {
			$diffs = array(
				'schemas' => array(),
				'tables' => array(),
				'columns' => array(),
				'constraints' => array(),
				'diffs' => array(
					'constraints' => array(),
					'fields' => array(),
				),
			);
			
			// Moteur de comparaisons :
			// Regroupe les différences par catégories (voir variable $diffs)
			foreach ($scanData1 as $schema => $schemaDatas) {
				if ($this->params['schema'] !== 'all' && $schema !== $this->params['schema']) {
					continue;
				}
				
				if (!in_array($schema, array_keys($scanData2))) {
					$diffs['schemas'][$schema] = true;
					continue;
				}
				
				foreach ($schemaDatas as $table => $tableDatas) {
					if (!in_array($table, array_keys($scanData2[$schema]))) {
						$diffs['tables'][$schema.'.'.$table] = true;
						continue;
					}
					
					foreach ($tableDatas['constraints'] as $constraint => $constraintDatas) {
						if (!in_array($constraint, array_keys($scanData2[$schema][$table]['constraints']))) {
							$diffs['contraints'][$schema.'.'.$table.'.'.$constraint] = true;
							continue;
						}
						
						$differences = Hash::diff($constraintDatas, $scanData2[$schema][$table]['constraints'][$constraint]);
						
						foreach (array_keys($differences) as $key) {
							$diffs['diffs']['constraints'][$schema.'.'.$table.'.'.$constraint][$key] = array(
								$this->name1 => $constraintDatas[$key],
								$this->name2 => $scanData2[$schema][$table]['constraints'][$constraint][$key],
							);
						}
					}
					
					foreach ($tableDatas['fields'] as $field => $fieldDatas) {
						if (!in_array($field, array_keys($scanData2[$schema][$table]['fields']))) {
							$diffs['columns'][$schema.'.'.$table.'.'.$field] = true;
							continue;
						}
						
						$differences = Hash::diff($fieldDatas, $scanData2[$schema][$table]['fields'][$field]);
						
						foreach (array_keys($differences) as $key) {
							$diffs['diffs']['fields'][$schema.'.'.$table.'.'.$field][$key] = array(
								$this->name1 => $fieldDatas[$key],
								$this->name2 => $scanData2[$schema][$table]['fields'][$field][$key],
							);
						}
					}
				}
			}
			
			return $diffs;
		}
		
		protected function _renderReport($missings1, $missings2) {
			$report = array();
			$sentence = "%s dans '%s' / ou en trop dans '%s' : '%s'";
			
			foreach (array_keys($missings1['schemas']) as $schema) {
				$report[] = sprintf($sentence, "Schema manquant", $this->name2, $this->name1, $schema);
			}
			foreach (array_keys($missings2['schemas']) as $schema) {
				$report[] = sprintf($sentence, "Schema manquant", $this->name1, $this->name2, $schema);
			}
			
			foreach (array_keys($missings1['tables']) as $table) {
				$report[] = sprintf($sentence, "Table manquante", $this->name2, $this->name1, $table);
			}
			foreach (array_keys($missings2['tables']) as $table) {
				$report[] = sprintf($sentence, "Table manquante", $this->name1, $this->name2, $table);
			}
			
			foreach (array_keys($missings1['columns']) as $column) {
				$report[] = sprintf($sentence, "Colonne manquante", $this->name2, $this->name1, $column);
			}
			foreach (array_keys($missings2['columns']) as $column) {
				$report[] = sprintf($sentence, "Colonne manquante", $this->name1, $this->name2, $column);
			}
			
			foreach (array_keys($missings1['constraints']) as $constraint) {
				$report[] = sprintf($sentence, "Contrainte manquante", $this->name2, $this->name1, $constraint);
			}
			foreach (array_keys($missings2['constraints']) as $constraint) {
				$report[] = sprintf($sentence, "Contrainte manquante", $this->name1, $this->name2, $constraint);
			}
			
			$this->out(implode("\n", $report), 2);
			
			$report = array();
			$sentence = "Difference detectée sur la %s %s sur l'attribut '%s' :\n\t%s => '%s'\n\t%s => '%s'";
			
			foreach ($missings1['diffs']['constraints'] as $constraint => $values) {
				foreach ($values as $attribut => $cgValues) {
					$report[] = sprintf($sentence, 'contrainte', $constraint, $attribut, $this->name1, $cgValues[$this->name1], $this->name2, $cgValues[$this->name2]);
				}
			}
			
			foreach ($missings1['diffs']['fields'] as $field => $values) {
				foreach ($values as $attribut => $cgValues) {
					$report[] = sprintf($sentence, 'colonne', $field, $attribut, $this->name1, $cgValues[$this->name1], $this->name2, $cgValues[$this->name2]);
				}
			}
			
			$this->out($r = implode("\n\n", $report), 2);
			$this->createFile(TMP.'DatabaseCompare'.DS.'last_report.txt', $r);
		}
		
		/**
		 * Ajoute les options présentes dans $this->options
		 * 
		 * @return ConsoleOptionParser
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			
			$parser->addOptions($this->options);
			
			return $parser;
		}
	}