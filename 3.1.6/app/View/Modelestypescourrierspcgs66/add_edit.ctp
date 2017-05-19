<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'modeletypecourrierpcg66', "Modelestypescourrierspcgs66::{$this->action}" )
	);

	echo $this->Xform->create();

	echo $this->Default2->subform(
		array(
			'Modeletypecourrierpcg66.id' => array( 'type' => 'hidden' ),
			'Modeletypecourrierpcg66.name' => array( 'required' => true ),
			'Modeletypecourrierpcg66.typecourrierpcg66_id' => array( 'required' => true, 'type' => 'select', 'options' => $options['Modeletypecourrierpcg66']['typecourrierpcg66_id'], 'empty' => true ),
			'Modeletypecourrierpcg66.modeleodt' => array( 'required' => true ),
			'Modeletypecourrierpcg66.ismontant' => array( 'required' => true, 'type' => 'select', 'options' => $options['Modeletypecourrierpcg66']['ismontant'], 'empty' => true ),
			'Modeletypecourrierpcg66.isdates' => array( 'required' => true, 'type' => 'select', 'options' => $options['Modeletypecourrierpcg66']['isdates'], 'empty' => true ),
			'Modeletypecourrierpcg66.isactif' => array( 'required' => true, 'type' => 'select', 'options' => $options['Modeletypecourrierpcg66']['isactif'], 'empty' => true )
		),
                array(
                    'options' => $options
                )
	);
	echo $this->Xform->end( 'Save' );
	
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'modelestypescourrierspcgs66',
            'action'     => 'index'
        ),
        array(
            'id' => 'Back'
        )
    );
?>