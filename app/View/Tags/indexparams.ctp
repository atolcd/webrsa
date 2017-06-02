<?php $this->pageTitle = 'Paramétrages des Tags';?>
<h1>Paramétrage des Tags</h1>
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
						'Catégories',
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'categorietags', 'action' => 'index' ),
							$this->Permissions->check( 'categorietags', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				
				echo $this->Xhtml->tableCells(
					array(
						'Valeurs',
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'valeurstags', 'action' => 'index' ),
							$this->Permissions->check( 'valeurstags', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			?>
		</tbody>
	</table>
<br />
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'parametrages',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>