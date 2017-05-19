<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'valprogfichecandidature66', "Valsprogsfichescandidatures66::{$this->action}" )
	);

    echo $this->Xform->create();
    echo $this->Default2->subform(
        array(
            'Valprogfichecandidature66.id' => array( 'type' => 'hidden' ),
            'Valprogfichecandidature66.progfichecandidature66_id' => array( 'empty' => true, 'type' => 'select', 'required' => true ),
            'Valprogfichecandidature66.name' => array( 'type' => 'text', 'required' => true ),
			'Valprogfichecandidature66.actif' => array( 'type' => 'checkbox' ),
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