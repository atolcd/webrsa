<?php $this->pageTitle = 'Paramétrage des participants au Comité de l\'APRE';?>
<?php echo $this->Form->create( 'Paramsparticipants', array() );?>
<div>
	<h1><?php echo 'Visualisation de la table participant au comité APRE ';?></h1>

	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'participantscomites', 'action' => 'add' ),
				$this->Permissions->check( 'participantscomites', 'add' )
			).' </li>';
		?>
	</ul>
	<?php if( empty( $participants ) ):?>
		<p class="notice">Aucun participant présent pour le moment.</p>
	<?php else:?>
	<div>
		<h2>Table des Participants APRE</h2>
		<table>
		<thead>
			<tr>
				<th>Civilité</th>
				<th>Nom</th>
				<th>Prénom</th>
				<th>Fonction</th>
				<th>Organisme</th>
				<th>N° de téléphone</th>
				<th>Email</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $participants as $participant ):?>
				<?php echo $this->Xhtml->tableCells(
					array(
						h( Set::classicExtract( $qual, Set::classicExtract( $participant, 'Participantcomite.qual' ) ) ),
						h( Set::classicExtract( $participant, 'Participantcomite.nom' ) ),
						h( Set::classicExtract( $participant, 'Participantcomite.prenom' ) ),
						h( Set::classicExtract( $participant, 'Participantcomite.fonction' ) ),
						h( Set::classicExtract( $participant, 'Participantcomite.organisme' ) ),
						h( Set::classicExtract( $participant, 'Participantcomite.numtel' ) ),
						h( Set::classicExtract( $participant, 'Participantcomite.mail' ) ),
						$this->Xhtml->editLink(
							'Éditer le participant ',
							array( 'controller' => 'participantscomites', 'action' => 'edit', $participant['Participantcomite']['id'] ),
							$this->Permissions->check( 'participantscomites', 'edit' )
						),
						$this->Xhtml->deleteLink(
							'Supprimer le participant ',
							array( 'controller' => 'participantscomites', 'action' => 'delete', $participant['Participantcomite']['id'] ),
							$this->Permissions->check( 'participantscomites', 'delete' )
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