<?php $this->pageTitle = 'Paramétrages du module de courriers PCGs';?>
<h1><?php echo $this->pageTitle;?></h1>

<?php echo $this->Form->create( 'Courrierspcgs66', array() );?>
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
						h( 'Type de courriers' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'typescourrierspcgs66', 'action' => 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);

				echo $this->Xhtml->tableCells(
					array(
						h( 'Modèles liés aux types de courriers' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'modelestypescourrierspcgs66', 'action' => 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);

				echo $this->Xhtml->tableCells(
					array(
						h( 'Pièces liées aux modèles de courriers' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'piecesmodelestypescourrierspcgs66', 'action' => 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);

			?>
		</tbody>
	</table>
	<div class="submit">
		<?php
			echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
<?php echo $this->Form->end();?>