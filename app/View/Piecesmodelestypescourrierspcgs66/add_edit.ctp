<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'piecemodeletypecourrierpcg66', "Piecesmodelestypescourrierspcgs66::{$this->action}" )
	);

	echo $this->Xform->create();

	echo $this->Default2->subform(
		array(
			'Piecemodeletypecourrierpcg66.id' => array( 'type' => 'hidden' ),
			'Piecemodeletypecourrierpcg66.name' => array( 'required' => true ),
			'Piecemodeletypecourrierpcg66.modeletypecourrierpcg66_id' => array( 'required' => true, 'type' => 'select', 'options' => $options['Piecemodeletypecourrierpcg66']['modeletypecourrierpcg66_id'], 'empty' => true ),
			'Piecemodeletypecourrierpcg66.isautrepiece' => array( 'required' => true, 'type' => 'select', 'options' => $options['Piecemodeletypecourrierpcg66']['isautrepiece'], 'empty' => true ),
			'Piecemodeletypecourrierpcg66.isactif' => array( 'required' => true, 'type' => 'select', 'options' => $options['Piecemodeletypecourrierpcg66']['isactif'], 'empty' => true )
		),
		array(
			'options' => $options
		)
	);
	echo $this->Xform->end( 'Save' );
	
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'piecesmodelestypescourrierspcgs66',
            'action'     => 'index'
        ),
        array(
            'id' => 'Back'
        )
    );
?>