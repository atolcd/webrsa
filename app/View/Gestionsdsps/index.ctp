<?php $this->pageTitle = 'Paramétrages des DSPs';?>
<h1>Paramétrage des DSPs</h1>

<?php echo $this->Form->create( 'NouvellesDsps', array() );?>
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
						h( 'Codes ROME pour les secteurs' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'codesromesecteursdsps66', 'action' => 'index' ),
							$this->Permissions->check( 'codesromesecteursdsps66', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				echo $this->Xhtml->tableCells(
					array(
						h( 'Codes ROME pour les métiers' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'codesromemetiersdsps66', 'action' => 'index' ),
							$this->Permissions->check( 'codesromemetiersdsps66', 'index' )
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