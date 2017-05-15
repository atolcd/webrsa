<h1><?php echo $this->pageTitle = 'Paramétrages des APRE/ADREs';?></h1>

<table >
	<thead>
		<tr>
			<th>Nom de Table</th>
			<th colspan="2" class="action">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php

			echo $this->Xhtml->tableCells(
				array(
					h( 'Liste des aides de l\'APRE/ADRE' ),
					$this->Xhtml->viewLink(
						'Voir la table',
						array( 'controller' => 'typesaidesapres66', 'action' => 'index' ),
						( $this->Permissions->check( 'typesaidesapres66', 'index' ) && ( $compteurs['Pieceaide66'] > 0 ) && ( $compteurs['Themeapre66'] > 0 ) )
					)
				),
				array( 'class' => 'odd' ),
				array( 'class' => 'even' )
			);
			echo $this->Xhtml->tableCells(
				array(
					h( 'Liste des pièces administratives' ),
					$this->Xhtml->viewLink(
						'Voir la table',
						array( 'controller' => 'piecesaides66', 'action' => 'index' ),
						$this->Permissions->check( 'piecesaides66', 'index' )
					)
				),
				array( 'class' => 'odd' ),
				array( 'class' => 'even' )
			);
			echo $this->Xhtml->tableCells(
				array(
					h( 'Liste des pièces comptables' ),
					$this->Xhtml->viewLink(
						'Voir la table',
						array( 'controller' => 'piecescomptables66', 'action' => 'index' ),
						$this->Permissions->check( 'piecescomptables66', 'index' )
					)
				),
				array( 'class' => 'odd' ),
				array( 'class' => 'even' )
			);
			echo $this->Xhtml->tableCells(
				array(
					h( 'Thèmes de la demande d\'aide APRE/ADRE' ),
					$this->Xhtml->viewLink(
						'Voir la table',
						array( 'controller' => 'themesapres66', 'action' => 'index' ),
						$this->Permissions->check( 'themesapres66', 'index' )
					)
				),
				array( 'class' => 'odd' ),
				array( 'class' => 'even' )
			);
		?>
	</tbody>
</table>

<?php
	echo $this->Default->button(
		'back',
		array( 'controller' => 'parametrages', 'action' => 'index' ),
		array(
			'id' => 'Back'
		)
	);
?>