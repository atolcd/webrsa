<?php $this->pageTitle = 'Paramétrage des actions d\'insertion';?>
<?php echo $this->Xform->create( 'Action' );?>
<div>
	<h1><?php echo 'Visualisation de la table  ';?></h1>

	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'actions', 'action' => 'add' )
			).' </li>';
		?>
	</ul>
	<div>
		<h2>Table Actions d'insertion</h2>
		<table>
		<thead>
			<tr>
				<th>Code de l'action</th>
				<th>Libellé de l'action</th>
				<th>Type d'action</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $actions as $action ):?>
				<?php echo $this->Xhtml->tableCells(
							array(
								h( $action['Action']['code'] ),
								h( $action['Action']['libelle'] ),
								h( $action['Typeaction']['libelle'] ),
								$this->Xhtml->editLink(
									'Éditer l\'action',
									array( 'controller' => 'actions', 'action' => 'edit', $action['Action']['id'] ),
									$this->Permissions->check( 'actions', 'delete' )
								),
								$this->Xhtml->deleteLink(
									'Supprimer l\'action',
									array( 'controller' => 'actions', 'action' => 'delete', $action['Action']['id'] ),
									$this->Permissions->check( 'actions', 'delete' ) && !$action['Action']['occurences']
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
			echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>

<div class="clearer"><hr /></div>
<?php echo $this->Form->end();?>