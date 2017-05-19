<?php
	 $this->pageTitle = 'Ajout de participant au comité d\'examen';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<script type="text/javascript">
//<![CDATA[
	function allCheckboxes( checked ) {
		$$('input.checkbox').each( function ( checkbox ) {
			$( checkbox ).checked = checked;
		} );
		return false;
	}
//]]>
</script>
<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout participant';
	}
	else {
		$this->pageTitle = 'Édition participant';
	}
?>

	<h1><?php echo $this->pageTitle;?></h1>
	<?php
		///
		echo $this->Xhtml->tag(
			'ul',
			implode(
				'',
				array(
					$this->Xhtml->tag( 'li', $this->Xhtml->link( 'Tout sélectionner', '#', array( 'onclick' => 'allCheckboxes( true ); return false;' ) ) ),
					$this->Xhtml->tag( 'li', $this->Xhtml->link( 'Tout désélectionner', '#', array( 'onclick' => 'allCheckboxes( false ); return false;' ) ) ),
				)
			)
		);
	?>
	<?php echo $this->Xform->create( 'ComiteapreParticipantcomite', array( 'type' => 'post' ) ); ?>
		<div class="aere">
			<fieldset>
				<legend>Participants au comité</legend>
				<?php echo $this->Xform->input( 'Comiteapre.id', array( 'label' => false, 'type' => 'hidden' ) ) ;?>
				<table>
					<thead>
						<tr>
							<th>Nom/Prénom</th>
							<th>Fonction</th>
							<th>Organisme</th>
							<th>N° Téléphone</th>
							<th>Email</th>
							<th>Sélectionner</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach( $participants as $i => $participant ) {
								$pcPc = Set::extract( $this->request->data, 'Participantcomite.Participantcomite' );
								if( empty( $pcPc ) ) {
									$pcPc = array();
								}
								echo $this->Xhtml->tableCells(
									array(
										h( Set::classicExtract( $participant, 'Participantcomite.qual' ).' '.Set::classicExtract( $participant, 'Participantcomite.nom' ).' '.Set::classicExtract( $participant, 'Participantcomite.prenom' ) ),
										h( Set::classicExtract( $participant, 'Participantcomite.fonction' ) ),
										h( Set::classicExtract( $participant, 'Participantcomite.organisme' ) ),
										h( Set::classicExtract( $participant, 'Participantcomite.numtel' ) ),
										h( Set::classicExtract( $participant, 'Participantcomite.mail' ) ),

										$this->Xform->checkbox( 'Participantcomite.Participantcomite.'.$i, array( 'value' => $participant['Participantcomite']['id'], 'id' => 'ParticipantcomiteParticipantcomite'.$participant['Participantcomite']['id'] , 'checked' => in_array( $participant['Participantcomite']['id'], $pcPc ), 'class' => 'checkbox'  ) ),
									),
									array( 'class' => 'odd' ),
									array( 'class' => 'even' )
								);
							}
						?>
					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="submit">
			<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
			<?php echo $this->Form->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
		</div>
	<?php echo $this->Xform->end();?>
<div class="clearer"><hr /></div>