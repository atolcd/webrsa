<?php
	/**
	 * Fichier source de la classe ChangematrixShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe ChangematrixShell permet de générer un fichier "changematrix.html"
	 * qui présente la date de dernière modification des fichiers controllers,
	 * models et views pour chacun des modules.
	 *
	 * @package app.Console.Command
	 */
	class ChangematrixShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $output = '';

		/**
		 *
		 * @var type
		 */
		public $outfile;

		/**
		 *
		 * @param type $date
		 * @return type
		 */
		public function datetime_short( $date ) {
			$date = strtolower( $date );
			$date = str_replace(
					array( 'jan', 'fév', 'mar', 'avr', 'mai', 'jun', 'jui', 'aoû', 'sep', 'oct', 'nov', 'déc' ), array( 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec' ), $date
			);
			return strftime( '%d/%m/%Y %H:%M', strtotime( $date ) );
		}

		/**
		 *
		 */
		public function initialize() {
			parent::initialize();
			$this->outfile = APP.'changematrix.html';
		}

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( 'Ce script génère un fichier "changematrix.html" qui présente la date de dernière modification des fichiers controllers, models et views pour chacun des modules.' );
			$parser->addOption( 'tag', array(
				'short' => 't',
				'help' => 'Tag svn à vérifier',
				'default' => 'trunk'
			) );
			return $parser;
		}

		/**
		 *
		 */
		public function main() {
			$out = array( );

			if( $this->params['tag'] == 'trunk' ) {
				$matrixType = 'SVN '.strftime( '%d/%m/%Y %H:%M' );
				$svnUrl = 'svn://svn.adullact.net/svnroot/webrsa/trunk/app';
			}
			else {
				$matrixType = 'version '.$this->params['tag'];
				$svnUrl = 'svn://svn.adullact.net/svnroot/webrsa/tags/'.$this->params['tag'].'/app';
			}
			$lines = array( );
			$controllers = array( );
			$models = array( );
			$views = array( );

			$behaviors = array( );
			$components = array( );
			$helpers = array( );

			$shells = array( );

			$this->_wait( 'Récupération des informations via svn' );
			$hasList = @exec( 'svn list -R --verbose '.$svnUrl, $lines );

			if( $hasList ) {
				$this->_wait( 'Génération du document' );
				$this->XProgressBar->start( count( $lines ) );
				foreach( $lines as $line ) {
					$this->XProgressBar->next();
					$extract = preg_match(
							'/^ *(?P<revision>[0-9]+) +(?P<user>[^ ]+) +(?P<size>[^ ]+) +(?P<date>.+ [0-9]{0,4}([0-9]+:[0-9]+){0,1}) +(?P<file>.+)$/i', $line, $matches
					);

					if( $extract ) {
						if( substr( $matches['file'], -1 ) != '/' ) {
							// Controller
							if( ( $matches['file'] != 'Controller/AppController.php' ) && preg_match( '/^Controller\/([^\/]+)Controller.php$/', $matches['file'], $matches_controllers ) ) {
								$controllers[$matches_controllers[1]] = array(
									'revision' => $matches['revision'],
									'date' => $matches['date']
								);
							}

							// Models
							if( ( $matches['file'] != 'Model/AppModel.php' ) && preg_match( '/^Model\/([^\/]+).php$/', $matches['file'], $matches_models ) ) {
								$models[Inflector::pluralize( $matches_models[1] )] = array(
									'revision' => $matches['revision'],
									'date' => $matches['date']
								);
							}

							// Views
							if( !preg_match( '/^View\/Helper\//', $matches['file'] ) && preg_match( '/^View\/([^\/]+)\/([^\/]+).ctp/', $matches['file'], $matches_views ) ) {
								// Si n'existe pas ou est plus récent
								if( !isset( $views[$matches_views[1]] ) ) {
									$views[$matches_views[1]] = array( );
								}
								$views[$matches_views[1]][$matches_views[2]] = array(
									'revision' => $matches['revision'],
									'date' => $matches['date']
								);
							}

							//**************************************************
							// Behaviors
							if( preg_match( '/^Model\/Behavior\/([^\/]+).php$/', $matches['file'], $matches_behaviors ) ) {
								$behaviors[$matches_behaviors[1]] = array(
									'revision' => $matches['revision'],
									'date' => $matches['date']
								);
							}

							// Components
							if( preg_match( '/^Controller\/Component\/([^\/]+).php$/', $matches['file'], $matches_components ) ) {
								$components[$matches_components[1]] = array(
									'revision' => $matches['revision'],
									'date' => $matches['date']
								);
							}

							// Helpers
							if( preg_match( '/^View\/Helper\/([^\/]+).php$/', $matches['file'], $matches_helpers ) ) {
								$helpers[$matches_helpers[1]] = array(
									'revision' => $matches['revision'],
									'date' => $matches['date']
								);
							}

							//**************************************************
							// Shells
							if( preg_match( '/^Console\/Command\/([^\/]+).php$/', $matches['file'], $matches_shells ) ) {
								$shells[$matches_shells[1]] = array(
									'revision' => $matches['revision'],
									'date' => $matches['date']
								);
							}
						}
					}
				}

				//--------------------------------------------------------------

				$indexes = array_unique( Set::merge( array_keys( $controllers ), array_keys( $models ), array_keys( $views ) ) );
				sort( $indexes );

				$this->output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
						<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
							<head>
								<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
								<title>Changematrix '.$matrixType.'</title>
								<style type="text/css" media="all">
									body { font-size: 12px; }
									table { border-collapse: collapse; }
									th, td { border: 1px solid black; vertical-align: top; padding: 0.125em 0.25em; }
									table, thead, tbody, colgroup { border: 3px solid black; }
									tbody th { text-align: left; }
									h1 { font-weight: normal; }
									td.number { text-align: right; }
								</style>
							</head>
							<body>
								<h1>Webrsa '.$matrixType.'</h1>

								<h2>MVC</h2>

								<table>
									<colgroup span="1" />
									<colgroup span="2" />
									<colgroup span="2" />
									<colgroup span="3" />
									<thead>
										<tr>
											<th>Composant</th>
											<th colspan="2">Models</th>
											<th colspan="2">Controllers</th>
											<th colspan="3">Views</th>
										</tr>
									</thead>';
				foreach( $indexes as $index ) {
					$viewsCells = array( );
					if( isset( $views[$index] ) && is_array( $views[$index] ) ) {
						ksort( $views[$index] );
						foreach( $views[$index] as $key => $value ) {
							$viewsCells[] = '<td>'.$key.'</td>
											<td class="number">'.$value['revision'].'</td>
											<td>'.$this->datetime_short( $value['date'] ).'</td>';
						}
					}
					$rowspan = max( 1, count( $viewsCells ) );

					if( isset( $models[$index] ) ) {
						$modelCell = '<td rowspan="'.$rowspan.'" class="number">'.$models[$index]['revision'].'</td>
									<td rowspan="'.$rowspan.'">'.$this->datetime_short( $models[$index]['date'] ).'</td>';
					}
					else {
						$modelCell = '<td rowspan="'.$rowspan.'" class="number" colspan="2">N/A</td>';
					}

					if( isset( $controllers[$index] ) ) {
						$controllerCell = '<td rowspan="'.$rowspan.'" class="number">'.$controllers[$index]['revision'].'</td>
									<td rowspan="'.$rowspan.'">'.$this->datetime_short( $controllers[$index]['date'] ).'</td>';
					}
					else {
						$controllerCell = '<td rowspan="'.$rowspan.'" class="number" colspan="2">N/A</td>';
					}

					$this->output .= '<tbody>';
					$this->output .= '<tr>
							<th rowspan="'.$rowspan.'">'.$index.'</th>
							'.$modelCell.'
							'.$controllerCell.'
							'.( count( $viewsCells ) > 0 ? $viewsCells[0] : '<td colspan="3" class="number">N/A</td>' ).'
						</tr>';
					if( count( $viewsCells ) > 1 ) {
						for( $i = 1; $i < count( $viewsCells ); $i++ ) {
							$this->output .= '<tr>'.$viewsCells[$i].'</tr>';
						}
					}
					$this->output .= '</tbody>';
				}
				$this->output .= '</table>';

				//--------------------------------------------------------------

				$this->output .= '<h2>Abstractions</h2>';

				foreach( array( 'behaviors', 'components', 'helpers' ) as $abstraction ) {
					if( !empty( ${$abstraction} ) ) {
						$this->output .= '<h3>'.ucfirst( $abstraction ).'</h3>';

						$this->output .= '<table><tbody>';
						foreach( ${$abstraction} as $name => $item ) {
							$this->output .= '<tr>
								<th>'.$name.'</th>
								<td>'.$item['revision'].'</td>
								<td>'.$this->datetime_short( $item['date'] ).'</td>
							</tr>';
						}
						$this->output .= '</tbody></table>';
					}
				}

				//--------------------------------------------------------------

				if( !empty( $shells ) ) {
					$this->output .= '<h2>Shells</h2>';

					$this->output .= '<table><tbody>';
					foreach( $shells as $name => $item ) {
						$this->output .= '<tr>
							<th>'.$name.'</th>
							<td>'.$item['revision'].'</td>
							<td>'.$this->datetime_short( $item['date'] ).'</td>
						</tr>';
					}
					$this->output .= '</tbody></table>';
				}

				//--------------------------------------------------------------

				$this->output .= '</body></html>';
				file_put_contents( $this->outfile, $this->output );
				$out[] = '';
				$out[] = '<success>fichier généré : '.$this->outfile.'</success>';
			}
			else {
				$out[] = '<error>Erreur: impossible d\'obtenir '.$svnUrl.'</error>';
			}

			$this->out();
			$this->out( $out );
		}

	}
?>