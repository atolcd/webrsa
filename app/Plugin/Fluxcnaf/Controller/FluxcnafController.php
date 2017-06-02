<?php
	/**
	 * Code source de la classe FluxcnafController.
	 *
	 * @package Fluxcnaf
	 * @subpackage Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe FluxcnafController ...
	 *
	 * @package Fluxcnaf
	 * @subpackage Controller
	 */
	class FluxcnafController extends AppController
	{
		public $uses = array( 'Dossier', 'Fluxcnaf.Fluxcnaf' );

		public $aucunDroit = array( 'index', 'diffs' );

		protected function _column($column) {
			return strtolower( str_replace( '-', '', $column) );
		}

		protected function _sql($path, $columns) {
			$unions = array();

			foreach ($columns as $column) {
				$column_name = $this->_column($column);
				$unions[] = "SELECT
								'{$path}/{$column}' AS path,
								c.table_name,
								c.column_name,
								c.data_type,
								c.character_maximum_length,
								c.is_nullable
							FROM information_schema.columns AS c
							WHERE
								c.table_schema = 'public'
								AND c.column_name = '{$column_name}'
								/* Début: ne pas prendre en compte certaines tables */
								AND c.table_name NOT LIKE 'tmp_%'
								AND c.table_name NOT LIKE 'b_%'
								AND c.table_name NOT IN ('".implode("', '", $this->Fluxcnaf->tablesNonCaf)."')
								/* Fin: ne pas prendre en compte certaines tables */";
			}
			//$sql = "SELECT * FROM ( ".implode( " UNION ", $unions ).") AS r ORDER BY table_schema, table_name, ordinal_position;";
			$sql = implode( " UNION ", $unions );

			return $sql;
		}

		protected function _normalize( array $flux ) {
			foreach( $flux as $path => $values ) {
				sort( $values );
				$flux[$path] = $values;
			}

			ksort( $flux );
			return $flux;
		}

		protected function _diff( array $flux1, array $flux2 ) {
			$result = array();

			foreach( $flux1 as $path => $values ) {
				if( !isset( $flux2[$path] ) ) {
					$result[$path] = $values;
				}
				else {
					$diff = array_diff( $values, $flux2[$path] );
					if( !empty( $diff ) ) {
						$result[$path] = $diff;
					}
				}
			}

			return $result;
		}

		public function diffs() {
			$vrsd0301 = $this->_normalize( $this->Fluxcnaf->flux['Bénéficiaire VRSD0301'] );
			$vrsd0101 = $this->_normalize( $this->Fluxcnaf->flux['Bénéficiaire VRSD0101'] );

			$results = array(
				'vrsd0301_vrsd0101' => $this->_diff( $vrsd0301, $vrsd0101 ),
				'vrsd0101_vrsd0301' => $this->_diff( $vrsd0101, $vrsd0301 )
			);
			$this->set( compact( 'results' ) );
		}

		public function index() {
			$Conn = $this->Dossier->getDatasource();

			$results = array();
			$tables = array();
			$missing = array();
			foreach($this->Fluxcnaf->flux as $name => $flux) {
				$results[$name] = array();
				// Correspondances chemins XML <=> colonnes et tables de la BDD
				foreach($flux as $path => $columns) {
					$sql = $this->_sql($path, $columns);
					$results[$name][$path] = $Conn->query($sql);
				}

				// Liste des tables utilisées
				$tables[$name] = array_unique( Hash::extract($results[$name], '{s}.{n}.0.table_name') );
				sort($tables[$name]);

				// Listes des colonnes / balises manquantes
				$found = array_unique( Hash::extract($results[$name], '{s}.{n}.0.column_name') );
				$missing[$name] = array();
				foreach($flux as $path => $columns) {
					foreach( $columns as $tag ) {
						$column = $this->_column( $tag );
						if( !in_array( $column, $found ) ) {
							if( !isset( $missing[$name][$path] ) ) {
								$missing[$name][$path] = array();
							}
							$missing[$name][$path][] = $tag;
						}
					}
				}
			}

			$this->set(compact('results', 'tables', 'missing'));
		}
	}
?>