<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'textmailcui66', "Textsmailscuis66::{$this->action}" )
	);

	echo $this->Xform->create();

	echo $this->Default2->subform(
		array(
			'Textmailcui66.id' => array( 'type' => 'hidden' ),
			'Textmailcui66.name' => array( 'required' => true ),
			'Textmailcui66.sujet' => array( 'required' => true ),
			'Textmailcui66.contenu' => array( 'required' => true, 'type' => 'textarea' ),
			'Textmailcui66.actif' => array( 'required' => true, 'type' => 'checkbox' ),
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