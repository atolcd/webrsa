<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'motifrefuscui66', "Motifsrefuscuis66::{$this->action}" )
	);

    echo $this->Xform->create();
    echo $this->Default2->subform(
        array(
            'Motifrefuscui66.id' => array( 'type' => 'hidden' ),
            'Motifrefuscui66.name' => array( 'type' => 'text' ),
			'Motifrefuscui66.actif' => array( 'type' => 'checkbox' ),
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