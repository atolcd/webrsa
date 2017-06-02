<?php $this->pageTitle = 'Paramétrages des Equipes Pluridisciplinaires';?>
<h1>Paramétrage des EPs</h1>

<?php echo $this->Form->create( 'NouvellesEPs', array() );?>
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
						h( 'Fonction des membres d\'une EP' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'fonctionsmembreseps', 'action' => 'index' ),
							$this->Permissions->check( 'fonctionsmembreseps', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				echo $this->Xhtml->tableCells(
					array(
						h( __d( 'regroupementep', 'Regroupementep::index' ) ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'regroupementseps', 'action' => 'index' ),
							$this->Permissions->check( 'regroupementseps', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);

				if( Configure::read( 'Cg.departement' ) ==  93 ) {
					echo $this->Xhtml->tableCells(
						array(
							h( 'Motifs de demandes de réorientation' ),
							$this->Xhtml->viewLink(
								'Voir la table',
								array( 'controller' => 'motifsreorientseps93', 'action' => 'index' ),
								$this->Permissions->check( 'motifsreorientseps93', 'index' )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				}

				if( Configure::read( 'Cg.departement' ) ==  66 ) {
					echo $this->Xhtml->tableCells(
						array(
							h( 'Composition des Équipes Pluridisciplinaires' ),
							$this->Xhtml->viewLink(
								'Voir la table',
								array( 'controller' => 'compositionsregroupementseps', 'action' => 'index' ),
								$this->Permissions->check( 'compositionsregroupementseps', 'index' )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				}
			?>
		</tbody>
	</table>
	<div class="submit">
		<?php
			echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
<?php echo $this->Form->end();?>