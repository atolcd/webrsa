<?php $this->pageTitle = 'Foyer';?>

<?php echo $this->element( 'dossier_menu', array( 'foyer_id' => $foyer['Foyer']['id'] ) );?>

<div class="with_treemenu">
	<?php
		echo $this->Xhtml->link(
			$this->Xhtml->image(
				'icons/add.png',
				array( 'alt' => 'Ajouter', 'title' => 'Ajouter une personne au foyer' )
			),
			array( 'controller' => 'personnes', 'action' => 'add', $foyer['Foyer']['id'] ),
			array( 'escape' => false )
		);
	?>

	<table class="tooltips">
		<tbody>
			<?php
				foreach( $foyer['Personne'] as $personne ) {
					echo $this->Xhtml->tableCells(
						array(
							h( $personne['qual'] ),
							h( $personne['nom'] ),
							h( $personne['prenom'] ),
							h( $personne['nomnai'] ),
							h( $personne['prenom2'] ),
							h( $personne['prenom3'] ),
							h( $personne['nomcomnai'] ),
							h( $personne['dtnai'] ),
							h( $personne['rgnai'] ),
							h( $personne['typedtnai'] ),
							h( $personne['nir'] ),
							h( $personne['topvalec'] ),
							h( $personne['sexe'] ),
							$this->Xhtml->link(
								$this->Xhtml->image(
									'icons/pencil.png',
									array( 'alt' => 'Éditer' )
								),
								array( 'controller' => 'personnes', 'action' => 'edit', $personne['id'] ),
								array( 'escape' => false )
							),
							$this->Xhtml->link(
								$this->Xhtml->image(
									'icons/delete.png',
									array( 'alt' => 'Supprimer', 'title' => 'Supprimer' )
								),
								array( 'controller' => 'personnes', 'action' => 'delete', $personne['id'] ),
								array( 'escape' => false )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				}
			?>
		</tbody>
	</table>
</div>

<div class="clearer"><hr/></div>