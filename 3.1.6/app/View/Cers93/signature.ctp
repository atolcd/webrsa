<?php
	$title_for_layout = 'Signature du CER';
	$this->set( 'title_for_layout', $title_for_layout );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<?php
	echo $this->Html->tag( 'h1', $title_for_layout );

	echo $this->Xform->create( null, array( 'inputDefaults' => array( 'domain' => 'cer93' ) ) );

	// Bloc 10 : Signature du bénéficiaire
	echo '<br /><fieldset class="loici58"><p><em>Après avoir pris connaissance des informations indiquées sur ce contrat, je m\'engage à tout mettre en oeuvre pour réaliser le contenu de ce présent contrat.</em></p></fieldset>';

	echo $this->Xform->inputs(
		array(
			'fieldset' => false,
			'legend' => false,
			'Cer93.id' => array( 'type' => 'hidden' ),
			'Cer93.contratinsertion_id' => array( 'type' => 'hidden' ),
			'Cer93.observbenef' => array( 'type' => 'textarea' ),
			'Cer93.datesignature' => array( 'type' => 'date', 'dateFormat' => 'DMY', 'empty' => false ),
			'Cer93.positioncer' => array( 'type' => 'hidden', 'value' => '01signe' ) // FIXME
		)
	);
?>

<?php
	echo $this->Html->tag(
		'div',
		 $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
		.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
		array( 'class' => 'submit noprint' )
	);

	echo $this->Xform->end();
?>