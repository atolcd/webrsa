<?php
	/**
	 * Fichier source de la classe RevisionLineShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('XShell', 'Console/Command');

	/**
	 * La classe RevisionLineShell permet d'obtenir les informations sur une ligne en particulier
	 * Permet d'obtenir la révision, l'auteur et la date de dernière modification
	 *
	 * @package app.Console.Command
	 */
	class RevisionLineShell extends XShell
	{
		public function main() {
			/**
			 * Initialisation et vérification des paramêtres
			 */
			if (!isset($this->args[0])){
				$this->args[0] = $this->in('Indiquez le fichier (ex: app/Model/AppModel.php)');
			}
			
			if (!file_exists($this->args[0])) {
				$this->out("Le fichier n'a pas été trouvé!");
				exit;
			}
			
			if (strpos($this->args[0], ROOT) === 0) {
				$this->args[0] = trim(substr($this->args[0], strlen(ROOT)), '/');
			}
			
			if (!isset($this->args[1])){
				$this->args[1] = $this->in('Indiquez la ligne');
			}
			
			if (!is_numeric($this->args[1])) {
				$this->out("Vous devez indiquer un numero de ligne !");
				exit;
			}
			
			$revision = '';
			if (isset($this->args[2]) && is_numeric($this->args[1])) {
				$revision = '-r '.$this->args[2].' ';
			}
			
			/**
			 *  Extraction des informations subversion
			 */
			exec('svn blame -v '.$revision.'-x -b svn://scm.adullact.net/svn/webrsa/trunk/'.$this->args[0], $output);
			
			/**
			 * Vérification de l'existance de la ligne choisie et extraction de celle-ci
			 */
			if (isset($output[$this->args[1] -1])) {
				preg_match($regex = '/([\d]+)[\s]+([\w]+).+\(([\w.]+ [\d]+ [\S]+ [\d]+)\)(.*)/', $output[$this->args[1] -1], $matches);
			} else {
				$this->out(
					"<error>"
					.sprintf("La ligne %d n'a pas été trouvée dans la révision %s", $this->args[1], $revision ?: 'HEAD')
					.'</error>',
					2
				);
				exit;
			}
			
			/**
			 * Transfomation de l'array matches en variables
			 */
			list(, $lineRevision, $lineAuthor, $lineDate, $lineLine) = $matches;
			
			/**
			 * Extraction des informations sur le document (rev min et max + liste d'auteurs)
			 */
			$min = array();
			$max = array();
			$minRev = INF;
			$maxRev = 0;
			$authors = array();
			$authorsLine = array();
			
			foreach ($output as $value) {
				if (preg_match($regex, $value, $matches)) {
					list(, $revision, $author, $date, $line) = $matches;
					$data = compact('revision', 'author', 'date', 'line');
					
					// min et max des révisions
					if ((integer)$revision < $minRev) {
						$min = $data;
						$minRev = $revision;
					} elseif ((integer)$revision > $maxRev) {
						$max = $data;
						$maxRev = $revision;
					}
					
					// Liste les auteurs
					if (!isset($authors[$author])) {
						$authors[$author] = $data;
						$authorsLine[$author] = 0;
					} elseif ((integer)$authors[$author]['revision'] < $revision) {
						$authors[$author] = $data;
					}
					
					$authorsLine[$author]++;
				}
			}
			
			ksort($authors);
			$authorOutput = '';
			$nbLignes = count($output);
			
			/**
			 * Pour chaque auteurs, on indique la dernière révision, date et le nombre de ligne à son nom
			 */
			foreach ($authors as $author) {
				$authorOutput .= sprintf(
					"\t%s - révision %s (%s) %s/%s lignes\n",
					$author['author'],
					$author['revision'],
					$author['date'],
					$authorsLine[$author['author']],
					$nbLignes
				);
			}
			
			/**
			 * On imprime à l'écran le 1er rapport
			 */
			$this->out();
			$this->out(sprintf("RevisionLine %s %d %s", $this->args[0], $this->args[1], !empty($this->args[2]) ? $this->args[2] : ''));
			empty($this->args[2]) ? $this->out("Astuce: ajouter à la ligne ci-dessus la révision voulu") : '';
			$this->out();
			$this->out(sprintf("Document:\n\tLa plus ancienne révision: %s (%s par %s)", $minRev, $min['date'], $min['author']));
			$this->out(sprintf("\tLa plus réçente révision: %s (%s par %s)", $maxRev, $max['date'], $max['author']));
			$this->out();
			$this->out("Liste des auteurs du document:");
			$this->out($authorOutput, 0);
			$this->out();
			$this->out(sprintf("Ligne %d :", $this->args[1]));
			$this->out(sprintf("\tRévision: <success>%d</success>", $lineRevision));
			$this->out(sprintf("\tAuteur: <success>%s</success>", $lineAuthor));
			$this->out(sprintf("\tDate: <success>%s</success>", $lineDate));
			$this->out();
			$this->out(sprintf("\tContenu de la ligne:\n<success>%s</success>", $lineLine));
			$this->out();
			
			/**
			 *  Extraction du commentaire de révision
			 */
			$output = null;
			exec('svn log -r '.$lineRevision.' svn://scm.adullact.net/svn/webrsa/trunk/'.$this->args[0], $output);

			$text = array();
			foreach ($output as $value) {
				if ($value === '' || preg_match('/^\-+$|^r[\d]+ \| [\w]+ \| [\d]{4}/', $value)) {
					continue;
				}
				$text[] = $value;
			}
			
			$this->out("\tCommentaire :");
			$this->out('<success>'.implode("\n", $text).'</success>', 2);
			
			// URL vers la forge
			$this->out('<info>'.sprintf("https://adullact.net/scm/browser.php?group_id=613&commit=%s", $lineRevision).'</info>', 2);
			
			/**
			 *  Extraction du diff pour la zone de modification de la ligne choisie
			 */
			$output = null;
			exec('svn diff -r '.($lineRevision-1).':'.$lineRevision.' svn://scm.adullact.net/svn/webrsa/trunk/'.$this->args[0], $output);
			
			/**
			 *  On essaye de trouver le numéro de ligne par comparaison
			 */
			$active = false;
			$before = array();
			$after = array();
			$search = '+'.substr($lineLine, 1);
			$matches = array();
			$line = 0;
			foreach ($output as $value) {
				// Récupère le numéro de ligne
				if (preg_match('/@@ \-([\d]+),[\d]+ \+([\d]+),[\d]+ @@/', $value, $match)) {
					list(, $debutAncien, $debutNouveau) = $match;
					$line = $debutNouveau;
					continue;
				} elseif (isset($value[0])) {
					if ($value[0] === '+' || $value[0] === ' ') {
						$line++;
					}
				}
				
				if ($search === $value) {
					$matches[] = $line;
				}
			}
			
			/**
			 * Si un seul numéro de ligne est ressorti de la comparaison, on tient le numéro de ligne
			 * Si il y a plusieurs occurences du texte de la ligne choisi, on choisi la ligne la plus proche de celle passé en paramètre
			 */
			if (count($matches) === 1) {
				$lineNumber = end($matches);
			} else {
				
				$lineNumber = INF;
				$lastEccart = INF;
				
				foreach ($matches as $match) {
					$eccart = max($match, $this->args[1]) - min($match, $this->args[1]);
					if ($eccart < $lastEccart) {
						$lineNumber = $match;
						$lastEccart = $eccart;
					}
				}
			}
			
			/**
			 * On tri les - et les + du diff (Ancien et Nouveau code)
			 * On ajoute le numéro de ligne
			 * On n'affiche que la portion de texte qui conçerne la ligne choisie
			 */
			foreach ($output as $value) {
				if (preg_match('/@@ \-([\d]+),([\d]+) \+([\d]+),([\d]+) @@/', $value, $matches)) {
					list(, $debutAncien, $tailleAncien, $debutNouveau, $tailleNouveau) = $matches;
					
					// Si on est déja en train de relever les diffs, on s'arrete ici
					if ($active && $lineNumber < $debutNouveau) {
						break;
					}
				
					$ancienPos = $debutAncien;
					$nouveauPos = $debutNouveau;
					$tailleLigne = max(
						strlen((int)$debutAncien + (int)$tailleAncien),
						strlen((int)$debutNouveau + (int)$tailleNouveau)
					);
					
					// Si la portion de diff fait parti de la ligne choisi
					if ($lineNumber >= $debutNouveau) {
						$active = true;
						$before = array();
						$after = array();
						continue;
					}
				}
				
				if ($active) {
					// Ajout du numéro de ligne
					if (empty($value)) {
						$before[] = ' '.str_pad($ancienPos, $tailleLigne);
						$after[] = ' '.str_pad($nouveauPos, $tailleLigne);
						$ancienPos++;
						$nouveauPos++;
					} elseif ($value[0] === '-') {
						$before[] = '-'.str_pad($ancienPos, $tailleLigne).' '.substr($value, 1);
						$ancienPos++;
					} elseif ($value[0] === '+') {
						$after[] = '+'.str_pad($nouveauPos, $tailleLigne).' '.substr($value, 1);
						$nouveauPos++;
					} elseif ($value[0] === ' ') {
						$before[] = ' '.str_pad($ancienPos, $tailleLigne).' '.substr($value, 1);
						$after[] = ' '.str_pad($nouveauPos, $tailleLigne).' '.substr($value, 1);
						$ancienPos++;
						$nouveauPos++;
					}
				}
			}
			
			/**
			 * On imprime le 2e rapport (différences), on surligne la ligne choisie
			 */
			if (empty($before)) {
				$this->out('<warning>Création de la ligne avec la création du fichier à la révision '.$minRev.'</warning>');
			} else {
				$this->out('Ancien (révision '.($lineRevision-1).'):');
				
				// Surligne l'equivalent de l'ancien si possible
				$highlight = false;
				if (preg_match('/(?:function\s+[\w]+|if|foreach|for)(?=\s*\(.*\))|\$[\w]+\s*=/', $lineLine, $match)) {
					$highlight = $match[0];
				}

				$i = 0;
				foreach ($before as $b) {
					if ($highlight && $b[0] === '-' && strpos($b, $highlight)) {
						$b = '<warning>'.$b.'</warning>';
					} elseif ($b[0] === '-') {
						$b = '<comment>'.$b.'</comment>';
					}
					
					$this->out($b);
					$i++;
					
					if ($i >= 200) {
						$this->out('<warning>Dépasse les 200 lignes...</warning>');
						break;
					}
				}

				$this->out();
				$this->out('Nouveau (révision '.($lineRevision).'):');

				$i = 0;
				foreach ($after as $a) {
					if (substr($a, $tailleLigne+1) === $lineLine) {
						$a = '<warning>'.$a.'</warning>';
					} elseif ($a[0] === '+') {
						$a = '<comment>'.$a.'</comment>';
					}
					
					$this->out($a);
					$i++;
					
					if ($i >= 200) {
						$this->out('<warning>Dépasse les 200 lignes...</warning>');
						break;
					}
				}
			}
		}
	}