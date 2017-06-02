<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'typersapcg66', "Typesrsapcgs66::{$this->action}" )
	);

	echo $this->Xform->create();

	echo $this->Default2->subform(
		array(
			'Typersapcg66.id' => array( 'type' => 'hidden' ),
			'Typersapcg66.name' => array( 'required' => true )
		)
	);

	echo $this->Xform->end( 'Save' );
	
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'typesrsapcgs66',
            'action'     => 'index'
        ),
        array(
            'id' => 'Back'
        )
    );
?>