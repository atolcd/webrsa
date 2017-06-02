<h1><?php echo $this->pageTitle = __d( 'gestionano', 'Gestionsanos::index' );?></h1>

<h2>Mise en place de contraintes</h2>
<table>
	<tbody>
	<?php
		foreach( $contraintes as $modelName => $contrainte ) {
			foreach( $contrainte as $name => $value ) {
				$label = __d( 'gestionano', "{$modelName}.{$name}" );
				echo "<tr><th>{$label}</th>";
				echo $this->Type2->format( $contraintes, "{$modelName}.{$name}", array( 'type' => 'boolean', 'tag' => 'td' ) ).'</tr>';
			}
		}
	?>
	</tbody>
</table>