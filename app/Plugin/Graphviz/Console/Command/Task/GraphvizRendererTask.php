<?php
	/**
	 * Code source de la classe GraphvizRendererTask.
	 *
	 * PHP 5.3
	 *
	 * @package Graphviz
	 * @subpackage Console.Command.Task
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe GraphvizRendererTask s'occupe du rendu au format MPD
	 *
	 * @package Graphviz
	 * @subpackage Console.Command.Task
	 */
	class GraphvizRendererTask extends AppShell
	{
		public $tables = array();

		public function renderTable( $summary ) {
			$label = $summary['Table']['name'];

			$primaryKey = Hash::get( $summary, 'Table.indexes.PRIMARY.column' );
			$foreignKeys = Hash::extract( $summary, 'Table.relations.{n}.From.column' );

			if( !empty( $this->params['fields'] ) ) {
				$fields = array();
				foreach( $summary['Table']['fields'] as $fieldName => $details ) {
					if( $fieldName === $primaryKey ) {
						$fieldName = "{$fieldName} &lt;PK&gt;";
					}
					else if( in_array( $fieldName, $foreignKeys ) ) {
						$fieldName = "{$fieldName} &lt;FK&gt;";
					}

					$fieldType = $details['type'];
					if( $details['length'] !== null ) {
						$fieldType = "{$fieldType}({$details['length']})";
					}

					$fields[] = "{$fieldName}: {$fieldType}";
				}
				$label = '{'.$label.'|'.implode( '\l', $fields ).'\l}';
			}

			return "\t\"{$summary['Table']['name']}\" [label=\"{$label}\", shape=record];\n";
		}

		public function renderAssociation( $summary ) {
			$return = '';

			foreach( $summary['Table']['relations'] as $relation ) {
				if( in_array( $relation['To']['table'], $this->tables ) ) {
					$taillabel = $headlabel = null;

					// ex.: personnes, foyers
					if($relation['From']['nullable'] === false && $relation['From']['unique'] === false) {
						$headlabel = '0,n';
						$taillabel = '1,1';
					}
					// TODO: nonorientationsproscovs58.dossiercov58_id -> NOT NULL && UNIQUE nonorientationsproscovs58_dossiercov58_id_idx
					else if($relation['From']['nullable'] === false && $relation['From']['unique'] === true) {
						$headlabel = '0,1';
						$taillabel = '1,1';
					}
					// ex.: accompagnementscuis66, immersionscuis66
					else if($relation['From']['nullable'] === true && $relation['From']['unique'] === false) {
						$headlabel = '0,n';
						$taillabel = '0,1';
					}
					else if($relation['From']['nullable'] === true && $relation['From']['unique'] === true) {
						$headlabel = '0,1';
						$taillabel = '0,1';
					}

					$return .= "\t\"{$relation['Foreignkey']['name']}\" [label=\"{$relation['Foreignkey']['name']}\", shape=ellipse];\n";
					$return .= "\t\"{$summary['Table']['name']}\" -> \"{$relation['Foreignkey']['name']}\" [dir=\"forward\", taillabel=\"{$taillabel}\", headlabel=\"\", arrowhead=\"none\"];\n";
					$return .= "\t\"{$relation['Foreignkey']['name']}\" -> \"{$relation['To']['table']}\" [dir=\"forward\", taillabel=\"\", headlabel=\"{$headlabel}\", arrowhead=\"none\"];\n";

				}
			}

			return $return;
		}

		public function render( array $summaries ) {
			$this->tables = Hash::extract( $summaries, '{n}.Table.name' );
			$content = '';

			foreach( $summaries as $summary ) {
				$content .= $this->renderTable( $summary );
			}

			foreach( $summaries as $summary ) {
				$content .= $this->renderAssociation( $summary );
			}

			$content = "digraph G {\n{$content}}";

			return $this->createFile( $this->params['output'], $content );
		}
	}
?>