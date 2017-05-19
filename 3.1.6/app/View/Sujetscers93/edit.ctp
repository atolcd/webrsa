<?php
	$title_for_layout = ( ( $this->action == 'add' ) ? 'Ajout d\'un sujet' : 'Modification d\'un sujet' );
	$this->set( 'title_for_layout', $title_for_layout );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}


	echo $this->Html->tag( 'h1', $title_for_layout );
	echo $this->Xform->create( null, array( 'inputDefaults' => array( 'domain' => 'sujetcer93' ) ) );


	echo $this->Xform->inputs(
		array(
			'fieldset' => false,
			'legend' => false,
			'Sujetcer93.id' => array( 'type' => 'hidden' ),
			'Sujetcer93.name' => array( 'type' => 'text' ),
			'Sujetcer93.isautre' => array( 'type' => 'checkbox' ),
			'Sujetcer93.actif' => array( 'type' => 'checkbox' )
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
