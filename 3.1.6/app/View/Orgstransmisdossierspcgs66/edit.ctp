<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'orgtransmisdossierpcg66', "Orgstransmisdossierspcgs66::{$this->action}" )
	);

	echo $this->Xform->create();

	echo $this->Default2->subform(
		array(
			'Orgtransmisdossierpcg66.id' => array( 'type' => 'hidden' ),
			'Orgtransmisdossierpcg66.name' => array( 'required' => true ),
			'Orgtransmisdossierpcg66.poledossierpcg66_id' => array( 'options' => $polesdossierspcgs66 ),
			'Orgtransmisdossierpcg66.isactif'
		),
		array(
		    'options' => $options
		)
	);

	echo $this->Html->tag(
		'div',
		 $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
		.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
		array( 'class' => 'submit noprint' )
	);

	echo $this->Xform->end();
	
?>