<?php
/**
 * Code source de la classe ConfigurationInsertionShell.
 *
 * PHP 7.2
 *
 * @package app.Console.Command
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses( 'XShell', 'Console/Command' );
App::uses( 'ConfigurationCategorie', 'Model' );

class ConfigurationInsertionShell extends XShell {

	private $departement ;
	private $files = array();

	/*
	* Model utilisé
	*/
	public $uses = array('ConfigurationCategorie', 'Configuration');

	/**
	 * Main entry point to this shell
	 *
	 * sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake ConfigurationInsertion -app app
	 *
	 * @return void
	*/
	public function main() {
		$this->out('Début de l\'insertion des fichiers');
		$this->departement  = Configure::read('Cg.departement');

		$this->configInsert($this->param('fichier'));
		$this->out('Fin de l\'insertion des fichiers');
	}

	/*
	 *  Insère la liste des configurations en BDD
	 *  @param string $directory
	 *
	 *  @return void
	*/
	private function configInsert($fichier = '') {
		$directory = APP.'Config'.DS;

		if($fichier === '') {
			// Récupération de tous les fichiers à insérer
			$this->lireDossier($directory);
		} else{
			if(strpos($fichier, '/') !== false){
				$this->out('Uniquement le nom de fichier est nécessaire, pas le chemin');
				exit();
			}
			if(strpos($fichier, '.php') === false && strpos($fichier, '.inc') === false){
				$this->out('L\'extension du fichier est nécessaire et seuls les fichiers .php et .inc sont lus');
				exit();
			}
			if(strpos($fichier, 'webrsa') !== false) {
				$fichier = $directory . $fichier;
			}else{
				$fichier = $directory . 'Cg' . $this->departement . DS . $fichier;
			}
			$this->files[0] = $fichier;
		}

		foreach ($this->files as $file) {
			$nameFile = substr(basename($file),0, strlen(basename($file))-4);

			if($nameFile === 'webrsa' || $nameFile === 'webrsa.cg'.$this->departement) {
				$folderConfig = true;
			} else {
				$folderConfig = false;
				require_once($file);
			}
			$confs = $this->listConfig($file, $folderConfig);

			$idCat = $this->ConfigurationCategorie->setCategorie($nameFile);

			// Insertion en BDD des valeurs de configurations
			$nbVariable = $this->insertSQL($confs, $idCat);
			$this->out('Fichier [' . $file . '] inséré en base avec ' . $nbVariable .' variables.', 2);
		}
	}

	/*
	 *  Récupère la liste fichiers à mettre en BDD
	 *  @param string $directory
	 *
	 *  @return void
	*/
	private function lireDossier($directory) {
		$filesDir = array_diff(scandir($directory, 1), array('..', '.'));
		foreach ($filesDir as $file) {
			if(is_dir($directory.$file) && $file == 'Cg' . $this->departement){
				$this->lireDossier($directory.$file.'/');
			}elseif ($directory === APP.'Config'.DS ) {
				if($file === 'webrsa.inc' || $file === 'webrsa.cg'.$this->departement.'.inc')
					$this->files[] = $directory . $file;
			}else{
				$this->files[] = $directory . $file;
			}
		}
	}

	/*
	 *  Insère en BDD les informations se trouvant dans $confToInsert
	 *  @param string $confToInsert
	 *
	 *  @return int
	*/
	private function insertSQL($confToInsert, $categorie) {
		$variables = $confToInsert['nom_variable'];
		$valeurs = $confToInsert['value'];
		$comments = $confToInsert['comments'];

		$nbInsertions = 0;

		// Récupération de l'id de la variable, ou création si besoin
		foreach ($variables as $key => $var) {
			$idVar = $this->Configuration->field('id', array(
				'Configuration.lib_variable LIKE' => $var,
				)
			);
			$this->Configuration->clear();
			$query = array();
			if($idVar !== false) {
				$query['id'] = $idVar;
			}
			$query['lib_variable'] = $var;
			$query['value_variable'] = $valeurs[$key];
			$query['configurationscategorie_id'] = $categorie;
			if(isset($comments[$key]))
				$query['comments_variable'] = $comments[$key];

			$this->Configuration->set($query);
			if($this->Configuration->validates()) {
				$this->Configuration->save();
				$nbInsertions++;
			} else{
				debug($this->Configuration->validationErrors);
			}
		}
		return $nbInsertions;
	}

	/*
	 *  Récupère la liste des configurations (nom de la variable, valeur et commentaires) par fichiers
	 *  @param string $filename
	 *  @param boolean $folder
	 *
	 *  @return array
	 *
	*/
	private function listConfig ($filename, $folder) {
		$configAll = array();
		$content = file_get_contents ($filename);

		$configAll['nom_variable'] = $this->listVar($content);
		$configAll['value'] = $this->listContent($configAll['nom_variable']);
		$configAll['comments'] = $this->listComments($content, $folder);
		foreach ($configAll['nom_variable'] as $key => $varName) {
			// Suppression des valeurs pour les variables d'environnements
			if($varName == 'Cg.departement' || $varName == 'Cmis' || $varName == 'Gedooo') {
				unset($configAll['nom_variable'][$key]);
				unset($configAll['value'][$key]);
				unset($configAll['comments'][$key]);
			}
		}

		return $configAll;
	}

	/*
	 *  Récupère la liste des noms de variable d'un fichier
	 *  @param string $fileContent
	 *
	 *  @return array
	 *
	*/
	private function listVar($fileContent) {
		$matches = array ();

		$varPattern = '#[^\/|\*]Configure::write[ ]{0,1}\([a-zA-Z0-9 ".\(\)\'=<>:/\-_\r\n\t@\*$€\+]{0,}\,#';
		$subject = $fileContent;

		preg_match_all ($varPattern, $subject, $matches);

		// Vérification
		// On ne garde que le nom de la variable
		$variables = $matches[0];
		foreach ($variables as $key => $value) {
			$pattern = array ('#Configure::write[ ]{0,1}\(#', '# #', '#,#', '#\r\n#', '#\'#', '#,#', '#\n#', '#\r#', '#\t#');
			$variables[$key] = preg_replace ($pattern, '', substr ($value, 0, -2));
		}

		return $variables;
	}

	/*
	 *  Récupère la liste des contenus de variable d'une liste
	 *  @param string $varList
	 *
	 *  @return array
	 *
	*/
	private function listContent($varList) {
		$val = array();
		$varContents = array();
		foreach ($varList as $key => $value) {
			// Ajout des valeurs des variables
			$varContents[$key] = Configure::read($value);
			$val[$key] = json_encode($varContents[$key], JSON_UNESCAPED_UNICODE);
		}

		return $val;
	}

	/*
	 *  Récupère la liste des commentaires de variable d'une liste
	 *  @param string $fileContent
	 *  @param string $folder
	 *
	 *  @return array
	 *
	*/
	private function listComments($fileContent, $folder) {
		$varPattern = '#[^\/|\*]Configure::write\([a-zA-Z0-9 ".\(\)\'=<>:/\-_\r\n\t@\*$€\+]{0,}\,#';
		// Récupération de tout ce qui n'est pas une variable
		$contents = preg_split($varPattern, $fileContent);
		$varComments = array();
		$finalComments = array();
		$comment = '';
		$index = 0;
		foreach ($contents as $key => $reste) {
			if($folder == true) {
				// Cas des fichiers webrsa*.inc
				preg_match_all('#/\*[\s\S]*?\*/#', $reste, $varComments[$key]);
				foreach ($varComments[$key][0] as $comments) {
					$comments = preg_replace('#[\*\/]++#','', $comments);
					if(isset($finalComments[$key]))
						$finalComments[$key] .= trim($comments);
					else {
						$finalComments[$key] = trim($comments);
					}
				}
			} else {
				// Cas des fichiers de config par CG
				$pos = strpos($reste, ');');
				if($pos === false) {
					$comment = ltrim(str_replace('<?php', '', $reste));
					$index = 0;
				} else {
					$comment .= substr($reste, 0, $pos);
					$comment = preg_replace('#[\*\/]++#','', $comment);
					if(isset($finalComments[$index]))
						$finalComments[$index] .= trim($comment);
					else {
						$finalComments[$index] = trim($comment);
					}
					$comment = substr($reste, $pos+2);
					$index++;
				}
			}
		}
		return $finalComments;
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(array(
			'/!\ /!\ /!\ Avant d\'utiliser cette fonction, il faut modifier les fichiers webrsa.inc & webrsa.cg[DEPARTEMENT].inc',
			'',
			'1/ il NE DOIT PAS il y avoir de commentaire dans les tableau (rappel: UNIQUEMENT webrsa.inc & webrsa.cg[DEPARTEMENT].inc)',
			'Exemple:',
			'/**',
			'* Options modifiable des cohortes liés aux tags (TAG et DossierPCG)',
			'*/',
			'Configure::write(\'Tag.Options.enums\',',
			'		array(',
			'		\'Personne\' => array(',
			'		// Tranche d\'age des personnes (25_30 sera entre 25 et 30 ans)',
			'		    \'trancheage\' => array(',
			'				\'0_24\' => \'< 25\',',
			'				\'25_30\' => \'25 - 30\',',
			'				\'31_55\' => \'31 - 55\',',
			'				\'56_65\' => \'56 - 65\',',
			'				\'66_999\' => \'> 65\',',
			'',
			'etc ... doit devenir : ',
			'',
			'/*',
			'* Options modifiable des cohortes liés aux tags (TAG et DossierPCG)',
			'* Tranche d\'age des personnes (25_30 sera entre 25 et 30 ans)',
			'/*',
			'Configure::write(\'Tag.Options.enums\',',
			'		array(',
			'		\'Personne\' => array(',
			'			\'trancheage\' => array(',
			'				\'0_24\' => \'< 25\',',
			'				\'25_30\' => \'25 - 30\',',
			'				\'31_55\' => \'31 - 55\',',
			'				\'56_65\' => \'56 - 65\',',
			'				\'66_999\' => \'> 65\',',
			'',
			'Variables à modifier :',
			'   - Statistiqueministerielle',
			'   - Statistiquedrees',
			' 	- Tag.Options.enums',
			'	- Search.Options.enums',
			'',
			'2/ Toutes les variables doivent avoir un commentaire au-dessus d\'elles. Si il n\'y a pas d\'explications, ajouter:',
			'(rappel: UNIQUEMENT webrsa.inc & webrsa.cg[DEPARTEMENT].inc)',
			'/*',
			'*	Comment NULL',
			'*/',
			'',
			'3/ Enlever les variables en commentaire (ex: //Configure::write...), (rappel: UNIQUEMENT webrsa.inc & webrsa.cg[DEPARTEMENT].inc)',
			'',
			'4/ Aller dans chaque fichiers se trouvant dans Config/Cg[DEPARTEMENT] puis faire cette correspondance:',
			'date_sql_to_cakephp( date( \'Y-m-d\', strtotime( \'-1 week\' ) ) ) ==> \'TAB::-1WEEK\'',
			'date_sql_to_cakephp( date( \'Y-m-d\', strtotime( \'now\' ) ) ) ==> \'TAB::NOW\'',
			'date_sql_to_cakephp( date( \'Y-m-d\', strtotime( \'first day of this month\' ) ) ) ==> \'TAB::FDOTM\'',
			'date_sql_to_cakephp( date( \'Y-m-d\', strtotime( \'-1 month\' ) ) ) ==> \'TAB::-1MONTH\'',
			'date_sql_to_cakephp( date( \'Y-m-d\', strtotime( \'+1 day\' ) ) ) ==> \'TAB::+1DAY\'',
			'date_format(date_add(new DateTime(), date_interval_create_from_date_string(\'+3 months\')), \'Y-m-d\') ==> \'TEXT::+3MONTHS\'',
			'date( \'Y-m-d\', strtotime( \'-1 month\' ) ) ==> \'TEXT::-1MONTH\'',
			'date( \'Y-m-d\', strtotime( \'+1 day\' ) ) ==> \'TEXT::+1DAY\'',
			'date(\'Y-m-d\') ==> \'TEXT::NOW\'',
			'date( \'Y\' ) ==> \'TEXT::ONLYYEAR\'',
			'',
		)
	);
		$parser->addOption( 'fichier', array(
			'short' => 'f',
			'help' => 'fichier à insérer en base avec extension et sans chemin. Si le nom est ommis, le script mettera tous les fichiers se trouvant dans app/Config/Cg[DEPARTEMENT]/* en BDD',
			'default' => '',
		) );
		return $parser;
	}
}