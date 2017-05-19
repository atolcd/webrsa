<?php $this->pageTitle = 'Paramétrages du Tableau de bord';?>
<h1>Paramétrages du Tableau de bord</h1>
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
						'Définir les rôles',
						$this->Xhtml->viewLink(
							'Voir la table',
							array('controller' => 'roles', 'action' => 'index'),
							$this->Permissions->check( 'roles', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);

                echo $this->Xhtml->tableCells(
                    array(
                        'Catégories de rôles (onglets)',
                        $this->Xhtml->viewLink(
                            'Voir la table',
                            array('controller' => 'categoriesactionroles', 'action' => 'index'),
                            $this->Permissions->check('categoriesactionroles', 'index')
                        )
                    ),
                    array( 'class' => 'odd' ),
                    array( 'class' => 'even' )
                );

                echo $this->Xhtml->tableCells(
                    array(
                        'Action des rôles',
                        $this->Xhtml->viewLink(
                            'Voir la table',
                            array('controller' => 'actionroles', 'action' => 'index'),
                            $this->Permissions->check('actionroles', 'index')
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