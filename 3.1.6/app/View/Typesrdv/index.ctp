<?php $this->pageTitle = 'Paramétrage des objets du rendez-vous';?>
<h1><?php echo $this->pageTitle;?></h1>

<ul class="actionMenu">
	<?php
		echo '<li>'.$this->Xhtml->addLink(
			'Ajouter',
			array( 'controller' => 'typesrdv', 'action' => 'add' )
		).' </li>';
	?>
</ul>

<table>
	<thead>
		<tr>
			<th>Objet du rendez-vous</th>
			<th>Modèle de notification de RDV</th>
			<?php
				if ( Configure::read( 'Cg.departement' ) == 66 ) {
					echo '<th>Nombre d\'absences avant possible passage en EPL Audition</th>';
				}
			?>
			<th colspan="2" class="action">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $typesrdv as $typerdv ):?>
			<?php
				$listefields = array(
					h( $typerdv['Typerdv']['libelle'] ),
					h( $typerdv['Typerdv']['modelenotifrdv'] )
				);
				if ( Configure::read( 'Cg.departement' ) == 66 ) {
					$listefields = array_merge(
						$listefields,
						array(
							h( $typerdv['Typerdv']['nbabsaveplaudition'] )
						)
					);
				}

				$listefields = array_merge(
					$listefields,
					array(
						$this->Xhtml->editLink(
							'Éditer le type d\'action',
							array( 'controller' => 'typesrdv', 'action' => 'edit', $typerdv['Typerdv']['id'] )
						),
						$this->Xhtml->deleteLink(
							'Supprimer le type d\'action',
							array( 'controller' => 'typesrdv', 'action' => 'delete', $typerdv['Typerdv']['id'] )
						)
					)
				);
				echo $this->Xhtml->tableCells(
					$listefields,
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			?>
		<?php endforeach;?>
	</tbody>
</table>
<?php
	echo $this->Default3->actions(
		array(
			"/Gestionsrdvs/index" => array(
				'text' => 'Retour',
				'class' => 'back',
				'disabled' => !$this->Permissions->check( 'Gestionsrdvs', 'index' )
			),
		)
	);
?>
<div class="clearer"><hr /></div>