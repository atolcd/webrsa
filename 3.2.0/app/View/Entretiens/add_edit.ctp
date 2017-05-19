<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
        <?php
            if( Configure::read( 'Cg.departement') == 66 ) {
                echo $this->Ajax->remoteFunction(
                    array(
                        'update' => 'EntretienPartenaire',
                        'url' => array( 'action' => 'ajaxaction', Set::extract( $this->request->data, 'Entretien.actioncandidat_id' ) )
                    )
                );
            }
		?>;

		dependantSelect( 'EntretienReferentId', 'EntretienStructurereferenteId' );
		observeDisableFieldsetOnCheckbox( 'EntretienRendezvousprevu', $( 'EntretienRendezvousId' ).up( 'fieldset' ), false );
		dependantSelect( 'RendezvousReferentId', 'RendezvousStructurereferenteId' );



	});
</script>
<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'entretien', "Entretiens::{$this->action}" )
	);
?>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Entretien', array( 'type' => 'post',  'id' => 'Bilan', 'novalidate' => true ) );
	}
	else {
		echo $this->Form->create( 'Entretien', array( 'type' => 'post', 'id' => 'Bilan', 'novalidate' => true ) );
		echo '<div>';
		echo $this->Form->input( 'Entretien.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
	echo '<div>';
	echo $this->Form->input( 'Entretien.personne_id', array( 'type' => 'hidden', 'value' => $personne_id ) );
	echo '</div>';
?>

<div class="aere">
	<fieldset class="aere">
			<?php
				echo $this->Default->subform(
					array(
						'Entretien.structurereferente_id',
						'Entretien.referent_id' => array( 'options' => $referents, 'empty' => true ),
						'Entretien.dateentretien' => array( 'dateFormat' => 'DMY', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+2, 'empty' => false ),
						'Entretien.typeentretien' => array( 'required' => true, 'options' => $options['Entretien']['typeentretien'], 'empty' => true ),
						'Entretien.objetentretien_id' => array(  'empty' => true )
					),
					array(
						'options' => $options
					)
				);
			?>
		<?php if( Configure::read( 'Cg.departement') == 66 ):?>
			<fieldset class="invisible">
				<legend><strong>Positionnement éventuel sur une action d'insertion</strong></legend>
				<table class="wide noborder">
					<tr>
						<td class="noborder">
							<?php
								echo $this->Form->input( 'Entretien.actioncandidat_id', array( 'label' => 'Intitulé de l\'action', 'type' => 'select', 'options' => $actionsSansFiche, 'empty' => true ) );
								echo $this->Ajax->observeField( 'EntretienActioncandidatId', array( 'update' => 'EntretienPartenaire', 'url' => array( 'action' => 'ajaxaction' ) ) );
								echo $this->Xhtml->tag(
									'div',
									' ',
									array(
										'id' => 'EntretienPartenaire'
									)
								);
							?>
						</td>
					</tr>
				</table>
			</fieldset>
		<?php endif;?>


			<?php
				echo $this->Default->subform(
					array(
						'Entretien.commentaireentretien'
					),
					array(
						'options' => $options
					)
				);
				echo $this->Xform->input( 'Entretien.arevoirle', array( 'label' => 'A revoir le ', 'type' => 'date', 'dateFormat' => 'MY', 'maxYear' => date('Y')+2, 'minYear' => date('Y')-2, 'empty' => true ) );?>
			<?php if( Configure::read( 'Cg.departement' ) != 66 && Configure::read( 'Cg.departement' ) != 93 ):?>
			<?php
				echo $this->Xform->input( 'Entretien.rendezvousprevu', array( 'label' => 'Rendez-vous prévu', 'type' => 'checkbox' ) );
			?>
		<fieldset class="invisible" id="rendezvousprevu">
			<?php
				echo $this->Default->subform(
					array(
						'Entretien.rendezvous_id' => array( 'type' => 'hidden' ),
						'Rendezvous.id' => array( 'type' => 'hidden' ),
						'Rendezvous.personne_id' => array( 'value' => $personne_id, 'type' => 'hidden' ),
						'Rendezvous.daterdv' => array( 'label' =>  'Rendez-vous fixé le ', 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 2, 'maxYear' => date( 'Y' ) + 2, 'empty' => true ),
						'Rendezvous.heurerdv' => array( 'label' => 'A ', 'type' => 'time', 'timeFormat' => '24', 'minuteInterval' => 5,  'empty' => true, 'hourRange' => array( 8, 19 ) ),
						'Rendezvous.typerdv_id' => array( 'label' => 'Type de rdv', 'type' => 'select', 'options' => $typerdv, 'empty' => true ),
					),
					array(
						'options' => $options
					)
				);

				echo $this->Xform->input( 'Rendezvous.structurereferente_id', array( 'label' =>  required( __( 'Nom de l\'organisme' ) ), 'type' => 'select', 'options' => $structs, 'empty' => true ) );

				echo $this->Xform->input( 'Rendezvous.referent_id', array( 'label' =>  ( 'Nom de l\'agent / du référent' ), 'type' => 'select', 'options' => $referents, 'empty' => true ) );
			?>
		</fieldset>
		<?php endif;?>
	</fieldset>

</div>
<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>