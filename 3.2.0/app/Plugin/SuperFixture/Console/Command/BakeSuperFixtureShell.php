<?php
	/**
	 * Code source de la classe BakeSuperFixtureShell.
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses( 'XShell', 'Console/Command' );
	App::uses('BakeSuperFixture', 'SuperFixture.Utility');
	 
	abstract class TemplateSuperFixture
	{
		public static function getFileString($className, $data) {
			// Fait un "var_export" personnalisé
			$dataString = "array(\n";
			foreach ($data as $modelName => $allDatas) {
				$dataString .= "\t\t\t\t'$modelName' => array(\n";
				foreach ($allDatas as $id => $datas) {
					$dataString .= "\t\t\t\t\t$id => array(\n";
					foreach ($datas as $fieldName => $value) {
						if ($value === null) {
							$escaped = 'null';
						} else {
							$escaped = is_integer($value) ? $value : "'$value'";
						}
						$dataString .= "\t\t\t\t\t\t'$fieldName' => $escaped,\n";
					}
					$dataString .= "\t\t\t\t\t),\n";
				}
				$dataString .= "\t\t\t\t),\n";
			}
			$dataString .= "\t\t\t)";
			 
			return 
"<?php
	/**
	 * Code source de la classe $className.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	App::uses('SuperFixtureInterface', 'SuperFixture.Interface');

	/**
	 * $className
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class $className implements SuperFixtureInterface {
		/**
		 * Fixtures supplémentaire à charger à vide
		 * 
		 * @var array
		 */
		public static \$fixtures = array();
		
		/**
		 * Permet d'obtenir les informations nécéssaire pour charger la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData() {
			return $dataString;
		}
	}
";
		}
	}
	 
	/**
	 * La classe BakeSuperFixtureShell de générer des SuperFixtures
	 *
	 * @package app.Console.Command
	 */
	class BakeSuperFixtureShell extends XShell
	{
		/**
		 * Initialisation: lecture des paramètres, on s'assure d'avoir une connexion
		 * valide
		 */
		public function startup() {
			parent::startup();
			try {
				$this->connection = ConnectionManager::getDataSource($this->params['connection']);
			}
			catch( Exception $e ) {}
		}
		
		/**
		 * Méthode principale.
		 */
		public function main() {
			while (!isset($this->args[0]) || empty($this->args[0]) || !is_string($this->args[0]) || !file_exists($this->args[0])){
				if (!isset($this->args[0]) || empty($this->args[0]) || !is_string($this->args[0])) {
					$this->out("Exemple : ".APP.'Vendor'.DS.'BakeSuperFixture'.DS.'ExempleBaker.php');
					$this->args[0] = $this->in("Indiquez le chemin du fichier de creation :");
					$this->out();
				}

				if (!file_exists($this->args[0])) {
					$this->out();
					$this->out("<error>Le fichier indiqué n'existe pas !</error>");
					$this->out();
					unset($this->args[0]);
				}
			}
			
			while (!isset($this->args[1]) || empty($this->args[1]) || !is_string($this->args[1]) || file_exists($this->args[1])) {
				if (!isset($this->args[1]) || empty($this->args[1]) || !is_string($this->args[1])){
					$this->out("Exemple : ".APP.'Test'.DS.'SuperFixture'.DS.'ExempleSuperFixture.php');
					$this->args[1] = $this->in("Indiquez le chemin du fichier de sortie :");
					$this->out();
				}

				if (file_exists($this->args[1])) {
					$deleteFile = strtoupper($this->in("Un fichier existe déjà au chemin indiqué, voulez vous le remplacer ?", array('O', 'N'), 'O'));
					$this->out();
					if ($deleteFile === 'N') {
						unset($this->args[1]);
					} else {
						unlink($this->args[1]);
						break;
					}
				}
			}
			
			if (!preg_match('/([^\\'.DS.']+)\\.[\w]+$/', $this->args[0], $matches)) {
				trigger_error("Le nom de fichier doit être une chaine de caractères avec une extension!");
				exit;
			}
			$classNameIn = $matches[1];
			if (!preg_match('/([^\\'.DS.']+)\\.[\w]+$/', $this->args[1], $matches)) {
				trigger_error("Le nom de fichier doit être une chaine de caractères avec une extension!");
				exit;
			}
			$classNameOut = $matches[1];
			
			require_once $this->args[0];
			$outFile = fopen($this->args[1], "w");
			if (!$outFile) {
				trigger_error("Le fichier ".$this->args[1]." n'a pas été crée ! Vérifiez les droits d'accès au dossier.");
				exit;
			}
			
			$BakeSuperFixture = new BakeSuperFixture();
			$$classNameIn = new $classNameIn();
			
			$data = $$classNameIn->getData();
			$fileString = TemplateSuperFixture::getFileString($classNameOut, $BakeSuperFixture->create($data, false));
			
			$report = '';
			foreach ($BakeSuperFixture->report() as $modelName => $count) {
				$report .= $modelName.': '.$count.' row'.($count > 1 ? 's' : '')."\n";
			}
			
			fputs($outFile, $fileString);
			fclose($outFile);
			
			chmod($this->args[1], 0664);
			
			$this->out();
			$this->out("Le fichier ".$this->args[1]." a été crée avec success !");
			$this->out($report, 2);
		}
		
		/**
		 * Paramétrages et aides du shell.
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();
			return $Parser;
		}
	}
?>