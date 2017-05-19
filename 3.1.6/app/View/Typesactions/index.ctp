<?php $this->pageTitle = 'Paramétrage des types d\'actions d\'insertion';?>
<?php echo $this->Xform->create( 'Typeaction' );?>
<div>
	<h1><?php echo 'Visualisation de la table  ';?></h1>

	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'typesactions', 'action' => 'add' ),
				$this->Permissions->check( 'typesactions', 'add' )
			).' </li>';
		?>
	</ul>
	<div>
		<h2>Table Type d'actions d'insertion</h2>
		<table>
		<thead>
			<tr>
				<th>Libellé de l'action</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $typesactions as $typeaction ):?>
				<?php echo $this->Xhtml->tableCells(
							array(
								h( $typeaction['Typeaction']['libelle'] ),
								$this->Xhtml->editLink(
									'Éditer le type d\'action',
									array( 'controller' => 'typesactions', 'action' => 'edit', $typeaction['Typeaction']['id'] ),
									$this->Permissions->check( 'typesactions', 'edit' )
								),
								$this->Xhtml->deleteLink(
									'Supprimer le type d\'action',
									array( 'controller' => 'typesactions', 'action' => 'delete', $typeaction['Typeaction']['id'] ),
									$this->Permissions->check( 'typesactions', 'delete' )
								)
							),
							array( 'class' => 'odd' ),
							array( 'class' => 'even' )
						);
				?>
			<?php endforeach;?>
			</tbody>
		</table>
</div>
</div>
	<div class="submit">
		<?php
			echo $this->Xform->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>

<div class="clearer"><hr /></div>
<?php echo $this->Xform->end();?>