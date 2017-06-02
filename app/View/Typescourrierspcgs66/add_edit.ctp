<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'typecourrierpcg66', "Typescourrierspcgs66::{$this->action}" )
	);

	echo $this->Xform->create();

	echo $this->Default2->subform(
		array(
			'Typecourrierpcg66.id' => array( 'type' => 'hidden' ),
			'Typecourrierpcg66.name' => array( 'required' => true ),
			'Typecourrierpcg66.isactif' => array( 'required' => true )
		),
                array(
                    'options' => $options
                )
	);

	echo $this->Xform->end( 'Save' );
	
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'typescourrierspcgs66',
            'action'     => 'index'
        ),
        array(
            'id' => 'Back'
        )
    );
?>