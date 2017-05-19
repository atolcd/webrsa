<h1><?php echo $this->pageTitle = 'Composition de l\'Équipe pluridisciplinaire';?></h1>

<?php
	if ( $compteurs['Regroupementep'] == 0 ) {
		echo "<p class='error'>Merci d'ajouter au moins un regroupement avant d'en indiquer la composition.</p>";
	}
	elseif ( $compteurs['Fonctionmembreep'] == 0 ) {
		echo "<p class='error'>Merci d'ajouter au moins un membre avant d'en indiquer la composition.</p>";
	}
	else {
		echo '<table><thead>';
			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'th',
					__d( 'regroupementep', 'Regroupementep.name' )
				).
				$this->Xhtml->tag(
					'th',
					'Actions',
					array(
						'class' => 'action'
					)
				)
			);
		echo '</thead><tbody>';
			foreach( $regroupementseps as $regroupementep ) {
				echo $this->Xhtml->tag(
					'tr',
					$this->Xhtml->tag(
						'td',
						$regroupementep['Regroupementep']['name']
					).
					$this->Xhtml->tag(
						'td',
						$this->Xhtml->editLink( 'Modifier', array( 'controller' => 'compositionsregroupementseps', 'action' => 'edit', $regroupementep['Regroupementep']['id'] ) )
					)
				);
			}
		echo '</table>';

		echo $this->Default->button(
			'back',
			array(
				'controller' => 'gestionseps',
				'action'     => 'index'
			),
			array(
				'id' => 'Back'
			)
		);
	}
?>