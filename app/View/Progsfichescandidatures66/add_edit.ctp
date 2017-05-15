<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'progfichecandidature66', "Progsfichescandidatures66::{$this->action}" )
	);

	echo $this->Xform->create();

	echo $this->Default2->subform(
		array(
			'Progfichecandidature66.id' => array( 'type' => 'hidden' ),
			'Progfichecandidature66.name' => array( 'required' => true ),
            'Progfichecandidature66.isactif' => array( 'required' => true )
		),
        array(
            'options' => $options
        )
	);

	echo $this->Xform->end( 'Save' );
	
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'progsfichescandidatures66',
            'action'     => 'index'
        ),
        array(
            'id' => 'Back'
        )
    );
?>