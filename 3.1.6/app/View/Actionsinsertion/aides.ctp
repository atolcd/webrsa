<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'une action d\'insertion';
	}
	else {
		$this->pageTitle = 'Aides d\'insertion ';
		$foyer_id = $this->request->data['Personne']['foyer_id'];
	}
?>

<?php echo $this->element( 'dossier_menu', array( 'personne_id' => $personne_id ) );?>

<div class="with_treemenu">
	<h1><?php echo 'Aides d\'insertion  ';?></h1>
		<ul class="actionMenu">
			<?php
				echo '<li>'.$this->Xhtml->addLink(
					'Ajouter une aide d\'insertion',
					array( 'controller' => 'actionsinsertion', 'action' => 'add', $personne_id )
				).' </li>';
			?>
		</ul>
	<?php if( empty( $actionsinsertion ) ):?>
		<p class="notice">Ce contrat ne possède pas encore d'actions d'insertion.</p>

	<?php else:?>

	<table class="tooltips">
		<thead>
			<tr>
				<th>Libelle aide</th>
				<th colspan="3" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $actionsinsertion['Actioninsertion'] as $actioninsertion ):?>
				<?php
					$title = $actioninsertion['lib_action'];

					echo $this->Xhtml->tableCells(
						array(
							h( $actioninsertion['lib_action'] ),
							$this->Xhtml->viewLink(
								'Ajouter une action d\'insertion',
								array( 'controller' => 'actionsinsertion', 'action' => 'index', $actioninsertion['id'])
							),
							$this->Xhtml->editLink(
								'Éditer l\'action d\'insertion ',
								array( 'controller' => 'actionsinsertion', 'action' => 'edit', $actioninsertion['id'] )
							)
						)
					);
				?>
			<?php endforeach;?>
		</tbody>
	</table>
	<?php endif;?>

</div>
<div class="clearer"><hr /></div>