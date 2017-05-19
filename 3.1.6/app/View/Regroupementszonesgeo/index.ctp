<?php $this->pageTitle = 'Paramétrage des régions';?>

<div>
	<h1><?php echo 'Visualisation de la table  ';?></h1>
	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'regroupementszonesgeo', 'action' => 'add' )
			).' </li>';
		?>
	</ul>
	<div>
		<h2>Table Région</h2>
		<table>
		<thead>
			<tr>
				<th>Libellé de la région</th>
				<th colspan="12" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $rgpts as $rgpt ):?>
				<?php echo $this->Xhtml->tableCells(
					array(
						h( $rgpt['Regroupementzonegeo']['lib_rgpt'] ),
						$this->Xhtml->editLink(
							'Éditer la région',
							array( 'controller' => 'regroupementszonesgeo', 'action' => 'edit', $rgpt['Regroupementzonegeo']['id'] )
						),
						$this->Xhtml->deleteLink(
							'Supprimer la région',
							array( 'controller' => 'regroupementszonesgeo', 'action' => 'delete', $rgpt['Regroupementzonegeo']['id'] )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				); ?>
			<?php endforeach;?>
			</tbody>
		</table>
</div>
</div>
<div class="clearer"><hr /></div>