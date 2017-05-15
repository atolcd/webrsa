<?php $this->pageTitle = 'Paramétrage des types de notification';?>
<?php echo $this->Xform->create( 'Typenotifpdo' );?>
<div>
	<h1><?php echo 'Visualisation de la table  ';?></h1>

	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'typesnotifspdos', 'action' => 'add' ),
				$this->Permissions->check( 'typesnotifspdos', 'add' )
			).' </li>';
		?>
	</ul>
	<div>
		<h2>Table Type de Notification</h2>
		<table>
		<thead>
			<tr>
				<th>Libellé</th>
				<th>Modèle de notification de PDO</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
				<?php foreach( $typesnotifspdos as $typenotifpdo ):?>
				<?php echo $this->Xhtml->tableCells(
							array(
								h( $typenotifpdo['Typenotifpdo']['libelle'] ),
								h( $typenotifpdo['Typenotifpdo']['modelenotifpdo'] ),
								$this->Xhtml->editLink(
									'Éditer le type de PDO ',
									array( 'controller' => 'typesnotifspdos', 'action' => 'edit', $typenotifpdo['Typenotifpdo']['id'] ),
									$this->Permissions->check( 'typesnotifspdos', 'edit' )
								),
								$this->Xhtml->deleteLink(
									'Supprimer le type de PDO ',
									array( 'controller' => 'typesnotifspdos', 'action' => 'deleteparametrage', $typenotifpdo['Typenotifpdo']['id'] ),
									$this->Permissions->check( 'typesnotifspdos', 'deleteparametrage' )
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