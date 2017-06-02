<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'requestmanager', "Requestsmanager::{$this->action}" )
	);

    echo $this->Xform->create();
	
	// Evite de transformer le champ name en select
	unset($options['Requestmanager']['name']);
	
    echo $this->Default2->subform(
        array(
            'Requestmanager.id' => array( 'type' => 'hidden' ),
            'Requestmanager.requestgroup_id' => array( 'empty' => true, 'type' => 'select' ),
            'Requestmanager.name',
			'Requestmanager.json' => array( 'view' => true ),
			'Requestmanager.actif' => array( 'type' => 'checkbox' ),
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