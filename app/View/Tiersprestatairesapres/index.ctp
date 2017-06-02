<?php $this->pageTitle = 'Paramétrage des Tiers prestataires APRE';?>
<?php echo $this->Xform->create( 'Tiersprestataireapre' );?>
<div>
	<h1><?php echo 'Visualisation de la table tiers prestataire APRE ';?></h1>

	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'tiersprestatairesapres', 'action' => 'add' ),
				$this->Permissions->check( 'tiersprestatairesapres', 'add' )
			).' </li>';
		?>
	</ul>
	<?php if( empty( $tiersprestatairesapres ) ):?>
		<p class="notice">Aucun tiers prestataire présent pour le moment.</p>
	<?php else:?>
	<div>
		<h2>Table des Tiers prestataires APRE</h2>
		<table>
		<thead>
			<tr>
				<th>Nom organisme</th>
				<th>N° Siret  </th>
				<th>Adresse</th>
				<th>N° de téléphone</th>
				<th>Email</th>
				<th>Formation liée</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $tiersprestatairesapres as $tiersprestataire ):?>
				<?php echo $this->Xhtml->tableCells(
					array(
						h( Set::classicExtract( $tiersprestataire, 'Tiersprestataireapre.nomtiers' ) ),
						h( Set::classicExtract( $tiersprestataire, 'Tiersprestataireapre.siret' ) ),
						h(
							Set::classicExtract( $tiersprestataire, 'Tiersprestataireapre.numvoie' ).' '.Set::enum( Set::classicExtract( $tiersprestataire, 'Tiersprestataireapre.typevoie' ), $typevoie ).' '.Set::classicExtract( $tiersprestataire, 'Tiersprestataireapre.nomvoie' ).' '.Set::classicExtract( $tiersprestataire, 'Tiersprestataireapre.codepos' ).' '.Set::classicExtract( $tiersprestataire, 'Tiersprestataireapre.ville' )
						),
						h( Set::classicExtract( $tiersprestataire, 'Tiersprestataireapre.numtel' ) ),
						h( Set::classicExtract( $tiersprestataire, 'Tiersprestataireapre.adrelec' ) ),
						h( Set::enum( Set::classicExtract( $tiersprestataire, 'Tiersprestataireapre.aidesliees' ), $natureAidesApres ) ),
						$this->Xhtml->editLink(
							'Éditer le tiers prestataire APRE ',
							array( 'controller' => 'tiersprestatairesapres', 'action' => 'edit', $tiersprestataire['Tiersprestataireapre']['id'] ),
							$this->Permissions->check( 'tiersprestatairesapres', 'edit' )
						),
						$this->Xhtml->deleteLink(
							'Supprimer le tiers prestataire APRE ',
							array( 'controller' => 'tiersprestatairesapres', 'action' => 'delete', $tiersprestataire['Tiersprestataireapre']['id'] ),
							$this->Permissions->check( 'tiersprestatairesapres', 'delete' ) && Set::classicExtract( $tiersprestataire, 'Tiersprestataireapre.deletable' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				); ?>
			<?php endforeach;?>
			</tbody>
		</table>
	</div>
	<?php endif;?>
	<div class="submit">
		<?php
			echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
</div>
<div class="clearer"><hr /></div>
<?php echo $this->Form->end();?>