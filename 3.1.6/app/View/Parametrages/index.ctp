<?php $this->pageTitle = 'Paramétrages';?>
<h1>Paramétrage des tables</h1>

<?php echo $this->Form->create( 'NouvellesDemandes', array() );?>
	<table >
		<thead>
			<tr>
				<th>Nom de Table</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach( $links as $label => $link ) {
					echo $this->Xhtml->tableCells(
						array(
							h( $label ),
							$this->Xhtml->viewLink(
								'Voir la table',
								$link,
								$this->Permissions->check( $link['controller'], $link['action'] )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				}
			?>
		</tbody>
	</table>
<?php echo $this->Form->end();?>
