<?php $this->pageTitle = 'Paramétrage de zone géographique';?>
<?php echo $this->Xform->create( 'Zonegeographique' );?>

<div>
	<h1><?php echo 'Visualisation de la table  ';?></h1>

	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'zonesgeographiques', 'action' => 'add' ),
				$this->Permissions->check( 'zonesgeographiques', 'add' )
			).' </li>';
		?>
	</ul>
	<div>
		<h2>Table Zone géographique</h2>
		<table>
		<thead>
			<tr>
				<th>Libellé</th>
				<th>Code insee</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $zones as $zone ):?>
				<?php echo $this->Xhtml->tableCells(
							array(
								h( $zone['Zonegeographique']['libelle'] ),
								h( $zone['Zonegeographique']['codeinsee'] ),
								$this->Xhtml->editLink(
									'Éditer la zone géographique ',
									array( 'controller' => 'zonesgeographiques', 'action' => 'edit', $zone['Zonegeographique']['id'] ),
									$this->Permissions->check( 'zonesgeographiques', 'edit' )
								),
								$this->Xhtml->deleteLink(
									'Supprimer la zone géographique ',
									array( 'controller' => 'zonesgeographiques', 'action' => 'delete', $zone['Zonegeographique']['id'] ),
									$this->Permissions->check( 'zonesgeographiques', 'delete' )
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