<?php $this->pageTitle = 'Paramétrages des CUIs';?>
<h1>Paramétrage de l'Editeur de requêtes</h1>
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
							array( 'controller' => 'requestgroups', 'action' => 'index' ),
							$this->Permissions->check( 'requestgroups', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				
				echo $this->Xhtml->tableCells(
					array(
						'Requêtes',
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'requestsmanager', 'action' => 'savedindex' ),
							$this->Permissions->check( 'requestsmanager', 'savedindex' )
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