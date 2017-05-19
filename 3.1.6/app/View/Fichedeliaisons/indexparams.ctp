<?php $this->pageTitle = 'Paramétrages des Fiches de liaisons';?>
<h1>Paramétrages des Fiches de liaisons</h1>
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
						'Motifs de fiche de liaison',
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'motiffichedeliaisons', 'action' => 'index' ),
							$this->Permissions->check( 'motiffichedeliaisons', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);

				echo $this->Xhtml->tableCells(
					array(
						'Logiciels ou sites consultés',
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'logicielprimos', 'action' => 'index' ),
							$this->Permissions->check( 'logicielprimos', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);

				echo $this->Xhtml->tableCells(
					array(
						'Proposition de primoanalyse',
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'propositionprimos', 'action' => 'index' ),
							$this->Permissions->check( 'propositionprimos', 'index' )
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