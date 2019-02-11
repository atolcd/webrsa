<?php
	 $this->pageTitle = 'Présence des participants au comité d\'examen';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>

<?php echo $this->Xform->create( 'ComiteapreParticipantcomite', array( 'type' => 'post' ) ); ?>
	<div class="aere">
		<fieldset>
			<legend>Participants au comité</legend>
			<table>
				<thead>
					<tr>
						<th>Nom/Prénom</th>
						<th>Fonction</th>
						<th>Organisme</th>
						<th>N° Téléphone</th>
						<th>Email</th>
						<th>Présence</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach( $comiteparticipant as $index => $participant ) {
							$participantcomite_id = Set::classicExtract( $participant, 'ComiteapreParticipantcomite.participantcomite_id');
							$comiteapre_id = Set::classicExtract( $participant, 'ComiteapreParticipantcomite.comiteapre_id');
							$comiteapreparticipantcomite_id = Set::classicExtract( $participant, 'ComiteapreParticipantcomite.id');
							$valuePresence = Set::classicExtract( $this->request->data, "$index.ComiteapreParticipantcomite.presence" );

							echo $this->Xhtml->tableCells(
								array(
									h( Set::classicExtract( $participant, 'Participantcomite.nom' ) ),
									h( Set::classicExtract( $participant, 'Participantcomite.fonction' ) ),
									h( Set::classicExtract( $participant, 'Participantcomite.organisme' ) ),
									h( Set::classicExtract( $participant, 'Participantcomite.numtel' ) ),
									h( Set::classicExtract( $participant, 'Participantcomite.mail' ) ),

									$this->Xform->enum( 'ComiteapreParticipantcomite.'.$index.'.presence', array( 'legend' => false, 'type' => 'radio', 'separator' => '<br />', 'options' => $options['presence'], 'value' => ( !empty( $valuePresence ) ? $valuePresence : 'PRE' ) ) ).
									$this->Xform->input( 'ComiteapreParticipantcomite.'.$index.'.id', array( 'label' => false, 'div' => false, 'value' => $comiteapreparticipantcomite_id, 'type' => 'hidden' ) ).
									$this->Xform->input( 'ComiteapreParticipantcomite.'.$index.'.participantcomite_id', array( 'label' => false, 'div' => false, 'value' => $participantcomite_id, 'type' => 'hidden' ) ).
									$this->Xform->input( 'ComiteapreParticipantcomite.'.$index.'.comiteapre_id', array( 'label' => false, 'type' => 'hidden', 'value' => $comiteapre_id ) )
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

	<?php echo $this->Xform->submit( 'Enregistrer' );?>
<?php echo $this->Xform->end();?>

<div class="clearer"><hr /></div>
