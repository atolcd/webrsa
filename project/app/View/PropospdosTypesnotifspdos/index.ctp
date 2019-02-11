<?php
	$this->pageTitle = 'Situation PDO';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->element( 'dossier_menu', array( 'id' => $dossierId ) );
?>

<div class="with_treemenu">

<?php echo $this->Form->create( 'TraitementsPDOs', array('novalidate' => true) );?>
	<h1>Liste des traitements</h1>

	<?php if( empty( $notifs ) ):?>
		<p class="notice">Aucun traitement pour les PDOs.</p>
	<?php endif;?>

		<ul class="actionMenu">
			<?php
				echo '<li>'.$this->Xhtml->addLink(
					'Ajouter Traitement',
					array( 'controller' => 'propospdos_typesnotifspdos', 'action' => 'add', $pdo_id )
				).' </li>';
			?>
		</ul>

	<?php if( !empty( $notifs ) ):?>
		<table class="aere">
				<tbody>
					<tr class="even">
						<th>Type de notification</th>
						<th>Date de notification</th>
						<th class="action">Action</th>
					</tr>
					<?php foreach( $notifs as $index => $notif ):?>
						<tr>
							<td>
								<?php
									echo Set::enum( Set::classicExtract( $notif, 'PropopdoTypenotifpdo.typenotifpdo_id' ), $typenotifpdo );
								?>
							</td>
							<td>
								<?php
									echo date_short( Set::classicExtract( $notif, 'PropopdoTypenotifpdo.datenotifpdo' ) );
								?>
							</td>
							<td><?php
									echo $this->Xhtml->editLink(
										'Modifier la notification',
										array( 'controller' => 'propospdos_typesnotifspdos', 'action' => 'edit', Set::classicExtract( $notif, 'PropopdoTypenotifpdo.id' ) )
									);
								?>
							</td>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>
	<?php endif;?>
	<div class="submit">
		<?php
			echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
	<?php echo $this->Form->end();?>
	</div>
<div class="clearer"><hr /></div>