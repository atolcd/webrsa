<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'categorieactionrole', "Categoriesactionroles::{$this->action}" )
	);

    echo $this->Xform->create();
    echo $this->Default2->subform(
        array(
            'Categorieactionrole.id' => array( 'type' => 'hidden' ),
            'Categorieactionrole.name' => array( 'type' => 'text', 'required' => true ),
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