<?php $this->pageTitle = 'Paramétrages des décisions de dossiers PCGs';?>
<h1><?php echo $this->pageTitle; ?></h1>

<?php echo $this->Form->create( 'DecisionsdossiersPCGs', array() );?>
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
						h( 'Compositions de foyer' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'composfoyerspcgs66', 'action' => 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);

				echo $this->Xhtml->tableCells(
					array(
						h( 'Décision PCGs' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'decisionspcgs66', 'action' => 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				
				echo $this->Xhtml->tableCells(
					array(
						h( 'Tableau des questions' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'questionspcgs66', 'action' => 'index' )
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