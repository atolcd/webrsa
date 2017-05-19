<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>
<h1>Flux CNAF</h1>
<div id="tabbedWrapper" class="tabs">
<?php
	foreach($results as $flux => $result) {
		$id = str_replace( '_', '', Inflector::slug($flux) );
		echo "<div id=\"{$id}\">";

		echo $this->Html->tag( 'h2', "{$flux}", array( 'class' => 'title' ) );
		echo "<div id=\"tabbedWrapper{$id}\" class=\"tabs\">";

		echo "<div id=\"{$id}Correspondances \">";
		echo $this->Html->tag( 'h3', h( 'Correspondances XML <=> BDD' ), array( 'class' => 'title' ) );

		foreach($result as $path => $lines) {
			echo $this->Html->tag( 'h4', $path );

			echo '<table>';
			echo '<thead>';
			echo $this->Html->tableHeaders(
				array(
					'path',
					'table_name',
					'column_name',
					'data_type',
					'character_maximum_length',
					'is_nullable'
				)
			);
			echo '</thead>';
			echo '<tbody>';
			// Champs trouvés
			foreach($lines as $line) {
				echo $this->Html->tableCells(
					array(
						h($line[0]['path']),
						h($line[0]['table_name']),
						h($line[0]['column_name']),
						h($line[0]['data_type']),
						h($line[0]['character_maximum_length']),
						h($line[0]['is_nullable'])
					)
				);
			}
			// Champs manquants
			if( isset($missing[$flux][$path]) ) {
				foreach($missing[$flux][$path] as $tag) {
					echo $this->Html->tableCells(
						array(
							h("{$path}/{$tag}"),
							null,
							null,
							null,
							null,
							null
						),
						array( 'class' => 'error missing' ),
						array( 'class' => 'error missing' )

					);
				}
			}
			echo '</tbody>';
			echo '</table>';
		}
		echo "</div>";

		echo "<div id=\"{$id}Manquantes\">";
		echo $this->Html->tag( 'h3', h( 'Informations manquantes en BDD' ), array( 'class' => 'title' ) );
		echo '<table>';
		echo '<thead>';
		echo $this->Html->tableHeaders(
			array(
				'chemin',
				'balise'
			)
		);
		echo '</thead>';
		echo '<tbody>';
		foreach($missing[$flux] as $path => $columns) {
			foreach($columns as $column) {
				echo $this->Html->tableCells(
					array(
						h($path),
						h($column)
					)
				);
			}
		}
		echo '</tbody>';
		echo '</table>';
		echo "</div>";

		echo "<div id=\"{$id}Tables\">";
		echo $this->Html->tag( 'h3', h( 'Tables concernées' ), array( 'class' => 'title' ) );

		echo '<table>';
		echo '<thead>';
		echo $this->Html->tableHeaders(
			array(
				'table_name'
			)
		);
		echo '</thead>';
		echo '<tbody>';
		foreach($tables[$flux] as $table) {
			echo $this->Html->tableCells(
				array(
					h($table)
				)
			);
		}
		echo '</tbody>';
		echo '</table>';
		echo "</div>";

		// Commandes shell à lancer pour obtenir le MPD et le dictionnaire de données
		echo "<div id=\"{$id}Commands\">";
		echo $this->Html->tag( 'h3', h( 'Commandes shell' ), array( 'class' => 'title' ) );
		echo $this->Html->tag( 'h3', h( 'Dictionnaire de données' ) );
		echo "<pre>sudo -u www-data lib/Cake/Console/cake dictionnaire --output \"app/tmp/logs/{$flux}.html\" --regexp \"^(".implode( '|', $tables[$flux] ).")$\"</pre>";

		echo $this->Html->tag( 'h3', h( 'Modèle physique de données' ) );
		echo "<pre>sudo -u www-data lib/Cake/Console/cake Graphviz.graphviz_mpd2 --output \"app/tmp/logs/{$flux}.dot\" --tables \"/^(".implode( '|', $tables[$flux] ).")$/\" --fields true</pre>";
		echo "<pre>dot -K fdp -T png -o \"app/tmp/logs/{$flux}.png\" \"app/tmp/logs/{$flux}.dot\"</pre>";
		echo "</div>";

		echo "</div>";

		echo "</div>";
	}
?>
</div>
<script type="text/javascript">
	// On tronque la longueur des titres à 40 caractères avant de faire les onglets.
	$$( 'h2, h3' ).each( function( title ) { truncateWithEllipsis( title, 25 ); } );

	// Création des onglets à partir des titres.
	makeTabbed( 'tabbedWrapper', 2 );
	<?php
		foreach(array_keys($results) as $flux) {
			$id = str_replace( '_', '', Inflector::slug($flux) );
			echo "\tmakeTabbed( 'tabbedWrapper{$id}', 3 );\n";
		}
	?>
	// TODO: ajouter une classe error dans les onglets lorsqu'il existe des tr.error
</script>