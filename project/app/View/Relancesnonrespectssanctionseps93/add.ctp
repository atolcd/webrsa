<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle = 'Ajout d\'une relance';?></h1>

<?php
	echo $this->Xform->create();

	// Nonrespectsanctionep93
	echo $this->Xform->input( 'Nonrespectsanctionep93.id', array( 'type' => 'hidden' ) );
	echo $this->Xform->input( 'Nonrespectsanctionep93.orientstruct_id', array( 'type' => 'hidden' ) );
	echo $this->Xform->input( 'Nonrespectsanctionep93.contratinsertion_id', array( 'type' => 'hidden' ) );
	echo $this->Xform->input( 'Nonrespectsanctionep93.origine', array( 'domain' => 'nonrespectsanctionep93', 'type' => 'radio', 'options' => array( 'orientstruct' => 'Orientation non contractualisée', 'contratinsertion' => 'Non renouvellement du CER' ) ) );
	echo $this->Xform->input( 'Nonrespectsanctionep93.dossierep_id', array( 'type' => 'hidden' ) );
	echo $this->Xform->input( 'Nonrespectsanctionep93.propopdo_id', array( 'type' => 'hidden' ) );
	echo $this->Xform->input( 'Nonrespectsanctionep93.historiqueetatpe_id', array( 'type' => 'hidden' ) );
	echo $this->Xform->input( 'Nonrespectsanctionep93.rgpassage', array( 'type' => 'hidden' ) );
	echo $this->Xform->input( 'Nonrespectsanctionep93.sortienvcontrat', array( 'type' => 'hidden', 'value' => '0' ) );
	echo $this->Xform->input( 'Nonrespectsanctionep93.active', array( 'type' => 'hidden' ) );

	// Relancenonrespectsanctionep93
	echo $this->Xform->input( 'Relancenonrespectsanctionep93.id', array( 'type' => 'hidden' ) );
	echo $this->Xform->input( 'Relancenonrespectsanctionep93.nonrespectsanctionep93_id', array( 'type' => 'hidden' ) );
	echo $this->Xform->input( 'Relancenonrespectsanctionep93.numrelance', array( 'domain' => 'relancenonrespectsanctionep93', 'type' => 'radio', 'options' => $options['Relancenonrespectsanctionep93']['numrelance'] ) );
	echo $this->Xform->input( 'Relancenonrespectsanctionep93.dateimpression', array( 'type' => 'hidden' ) );
	echo $this->Xform->input( 'Relancenonrespectsanctionep93.daterelance_min', array( 'type' => 'hidden' ) );
	echo '<div class="input select"><span class="label">Date de relance minimale</span><span class="input">'.date_short( Hash::get( $this->request->data, 'Relancenonrespectsanctionep93.daterelance_min' ) ).'</span></div>';
	echo $this->Xform->input( 'Relancenonrespectsanctionep93.daterelance', array( 'domain' => 'relancenonrespectsanctionep93', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y', strtotime( Hash::get( $this->request->data, 'Relancenonrespectsanctionep93.daterelance_min' ) ) ), 'empty' => true ) );
	echo $this->Xform->input( 'Relancenonrespectsanctionep93.user_id', array( 'type' => 'hidden' ) );

	echo $this->Html->tag(
		'div',
		$this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
		.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
		array( 'class' => 'submit noprint' )
	);

	echo $this->Xform->end();
?>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		<?php if( Hash::get( $this->request->data, 'Nonrespectsanctionep93.origine' ) == 'orientstruct' ):?>
			$( 'Nonrespectsanctionep93OrigineContratinsertion' ).disable();
		<?php elseif( Hash::get( $this->request->data, 'Nonrespectsanctionep93.origine' ) == 'contratinsertion' ):?>
			$( 'Nonrespectsanctionep93OrigineOrientstruct' ).disable();
		<?php endif;?>

		<?php for( $i = 1 ; $i <= 2 ; $i++ ):?>
			<?php if( $i != Hash::get( $this->request->data, 'Relancenonrespectsanctionep93.numrelance' ) ):?>
				$( 'Relancenonrespectsanctionep93Numrelance<?php echo $i;?>' ).disable();
			<?php endif;?>
		<?php endfor;?>
	} );
</script>