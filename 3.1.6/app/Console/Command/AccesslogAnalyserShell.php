<?php
	/**
	 * Code source de la classe AccesslogAnalyserShell.
	 *
	 * @package app.Console.Command
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Shell.php.
	 */

	/**
	 * La classe AccesslogAnalyserShell ...
	 *
	 * @package app.Console.Command
	 */
	class AccesslogAnalyserShell extends AppShell
	{
		/**
		 * Méthode principale.
		 */
		public function main() {
			if ( !isset($this->args[0] ) ){
				$this->args[0] = $this->in('Indiquez la position du log à analyser (vous devez posséder les droits) :', null, '/var/log/apache2/access.log');
			}
			
			$File = fopen( $this->args[0], "r");
			if (!$File) {
				$this->out("Le fichier n'a pas été trouvé!");
				exit;
			}
			
			$ip = '((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))'; // de 0 à 255 suivi d'un point 3 fois puis de 0 à 255
			$date = '\[([0123][0-9]\/[\w]+\/20[0-9]{2}.*) \+[\d]+\]'; // [D/M/Y .*]
			$method = '"((?:GET)|(?:POST))'; // GET ou POST
			$url = ' (\/.*) HTTP'; // tout ce qui suit le slash avant HTTP
			$code = '" ([\d]+) '; // les chiffres qui suivent entouré d'espaces

			$results = array();

			while (($line = fgets($File)) !== false) {
				if (preg_match("/$ip.*$date.*$method.*$url.*$code.*/", $line, $matches)) {
					$args = preg_split('/[\/?]/', trim($matches[4], '/')); // Explode de l'url

					// Page d'accueil
					if (count($args) === 1 && $args[0] === '') {
						$args = array( 'dossiers', 'index' );
					}

					if (count($args) < 2) {
						$args[] = '';
					}

					$controller = $args[0];
					$action = $args[1];
					unset($args[0], $args[1]);
					$vars = implode('/', $args);

					if (!isset($results[$controller.'/'.$action])) {
						$results[$controller.'/'.$action] = array();
					}

					$results[$controller.'/'.$action][] = array(
						'ip' => $matches[1],
						'date' => DateTime::createFromFormat('d/M/Y:H:i:s', $matches[2]),
						'method' => $matches[3],
						'code' => $matches[5],
						'args' => $vars
					);
					
					if (!isset($ipList[$matches[1]])) {
						$ipList[$matches[1]] = 0;
					}
					$ipList[$matches[1]]++;
				}
			}

			fclose($File);

			ksort($results);

			$report = '';
			$totalaccess = 0;
			foreach ($results as $path => $access) {
				$tab = "";

				for ($i=0; $i<5-floor(strlen($path)/8); $i++) {
					$tab .= "\t";
				}
				$report .= "\n".$path.$tab."\t\tcount: ".count($access);
				$totalaccess += count($access);
			}

			$ipmax = array('value' => '', 'count' => 0);
			foreach ($ipList as $ip => $count) {
				if ($count > $ipmax['count']) {
					$ipmax = array(
						'value' => $ip,
						'count' => $count
					);
				}
			}

			$this->out($report);
			$this->out();
			$this->out("Nombre de pages consultés : $totalaccess");
			$this->out("Nombre d'ips différentes : ".count($ipList));
			$this->out("Nombre moyen de pages consultés : ".floor($totalaccess / count($ipList)));
			$this->out("IP la plus fréquente (".$ipmax['count']." pages consultés) : ".$ipmax['value']);
			$this->out();

			while ($key = $this->in("Pour plus d'informations, entrez une des urls, tapez 'refresh' pour réfaire le rapport global :")) {
				if ($key === 'refresh') {
					$this->out($report);
					$this->out();
					$this->out("Nombre de pages consultés : $totalaccess");
					$this->out("Nombre d'ips différentes : ".count($ipList));
					$this->out("Ips la plus fréquente (".$ipmax['count']." pages consultés) : ".$ipmax['value']);
					$this->out();
					continue;
				}
				elseif (!isset($results[$key])) {
					$this->out("Url non trouvé, entrez une url sous la forme : controllers/action");
					continue;
				}

				$export = array(
					'ip' => array(),
					'code' => array(),
					'method' => array(),
					'args' => array()
				);
				$max = array(
					'ip' => array('value' => null, 'count' => 0),
					'code' => array('value' => null, 'count' => 0),
					'method' => array('value' => null, 'count' => 0),
					'args' => array('value' => null, 'count' => 0)
				);
				foreach ($results[$key] as $value) {
					foreach (array('ip', 'code', 'method', 'args') as $k) {
						if (!isset($export[$k][$value[$k]])) {
							$export[$k][$value[$k]] = 0;
						}
						$export[$k][$value[$k]]++;

						if ($export[$k][$value[$k]] > $max[$k]['count']) {
							$max[$k] = array(
								'value' => $value[$k],
								'count' => $export[$k][$value[$k]]
							);
						}
					}
				}
				
				$diffDates = $this->_moyenneEntreDates( $results[$key] );

				$this->out("IP s'étant le plus connecté (".$max['ip']['count']." fois): ".$max['ip']['value']);

				$this->out('-----------------');
				ksort($export['code']);
				foreach ($export['code'] as $code => $count) {
					$this->out("Il y a eu $count retours de code $code");
				}
				$this->out('-----------------');

				ksort($export['method']);
				foreach ($export['method'] as $method => $count) {
					$this->out("Il y a eu $count methodes $method");
				}
				$this->out('-----------------');

				$this->out("L'url la plus consulté (".$max['args']['count']." fois): ");
				$this->out($max['args']['value']);
				$this->out('-----------------');
				
				$this->out("Le temps d'accès minimum a été de ".$this->_formatSecondes($diffDates['min']));
				$this->out("L'eccart maximum a été de ".$this->_formatSecondes($diffDates['max']));
				$this->out("En moyenne, cette page est consulté toutes les ".$this->_formatSecondes($diffDates['moy']));
			}
		}
		
		/**
		 * Permet d'obtenir à partir d'un array contenant des clef 'date', une moyenne (en secondes) des connections entre
		 * Permet d'obtenir au passage le min et le max
		 * 
		 * @param array $datas array( array('date' => DateTime), ... )
		 * @return array array( 'min' => (int), 'max' => (int), 'moy' => (int) )
		 */
		protected function _moyenneEntreDates( $datas ) {
			$diffs = array();
			
			// On tri par date
			usort($datas, function($a, $b) {
				$ad = $a['date'];
				$bd = $b['date'];

				if ($ad == $bd) {
				  return 0;
				}

				return $ad > $bd ? 1 : -1;
			});
			
			foreach ($datas as $data) {
				if ($data['date'] === false) {
					$this->out("Erreur de date!");
					$this->out(var_export($data, true));
					continue;
				}
				
				if (!isset($memory)) {
					$memory = $data['date'];
				}
				else {
					$diff = $data['date']->diff($memory);
					$diffs[] = $diff->s + ($diff->i *60) + ($diff->h * 3600);
					$memory = $data['date'];
				}
			}
			
			return array(
				'min' => min($diffs),
				'max' => max($diffs),
				'moy' => round(array_sum($diffs) / count($diffs))
			);
		}
		
		/**
		 * Permet de transformer un nombre de secondes en phrase
		 * 
		 * @param integer $duree en secondes
		 * @return string
		 */
		protected function _formatSecondes( $duree ) {
			$heures = intval($duree / 3600);
			$minutes = intval(($duree % 3600) / 60);
			$secondes = intval((($duree % 3600) % 60));
			
			$heure_s = $heures > 1 ? 's' : '';
			$minute_s = $minutes > 1 ? 's' : '';
			$seconde_s = $secondes > 1 ? 's' : '';
			
			if ($heures > 0) {
				return "$heures heure$heure_s, $minutes minute$minute_s et $secondes seconde$seconde_s";
			}
			elseif ($minutes > 0) {
				return "$minutes minute$minute_s et $secondes seconde$seconde_s";
			}
			elseif ($secondes > 0) {
				return "$secondes seconde$seconde_s";
			}
			else {
				return "moins de 1 seconde";
			}
		}
	}
?>