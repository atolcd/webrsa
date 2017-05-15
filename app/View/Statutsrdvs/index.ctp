<?php $this->pageTitle = 'Paramétrage des statuts de rendez-vous';?>
<h1><?php echo $this->pageTitle;?></h1>

<ul class="actionMenu">
	<?php
		echo '<li>'.$this->Xhtml->addLink(
			'Ajouter',
			array( 'controller' => 'statutsrdvs', 'action' => 'add' )
		).' </li>';
	?>
</ul>
<?php if( empty( $statutsrdvs ) ):?>
	<p class="notice">Aucun statut de RDV présent pour le moment.</p>
<?php else:?>
	<table>
	<thead>
		<tr>
			<th>Statut de rendez-vous</th>
			<?php
				if( Configure::read( 'Cg.departement' ) == 58 ) {
					echo '<th>Provoque un passage en commission ?</th>';
				}
				elseif( Configure::read( 'Cg.departement' ) == 66 ) {
					echo '<th>Permet un passage en EPL Audition ?</th>';
				}
			?>
			<th colspan="2" class="action">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $statutsrdvs as $statutrdv ):?>
			<?php
				$listefields = array( h( $statutrdv['Statutrdv']['libelle'] ) );
				if( Configure::read( 'Cg.departement' ) == 58 ) {
					$listefields = array_merge(
						$listefields,
						array( $provoquepassagecommission[$statutrdv['Statutrdv']['provoquepassagecommission']] )
					);
				}
				elseif( Configure::read( 'Cg.departement' ) == 66 ) {
					$listefields = array_merge(
						$listefields,
						array( $provoquepassagecommission[$statutrdv['Statutrdv']['permetpassageepl']] )
					);
				}
				$listefields = array_merge(
					$listefields,
					array(
						$this->Xhtml->editLink(
							'Éditer le type d\'action',
							array( 'controller' => 'statutsrdvs', 'action' => 'edit', $statutrdv['Statutrdv']['id'] )
						),
						$this->Xhtml->deleteLink(
							'Supprimer le statut',
							array( 'controller' => 'statutsrdvs', 'action' => 'delete', $statutrdv['Statutrdv']['id'] )
						)
					)
				);
				echo $this->Xhtml->tableCells(
					array(
						$listefields
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			?>
		<?php endforeach;?>
		</tbody>
	</table>
<?php endif;?>
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