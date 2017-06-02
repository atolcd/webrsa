<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'poledossierpcg66', "Polesdossierspcgs66::{$this->action}" )
	);

	echo $this->Xform->create();

	echo $this->Default2->subform(
		array(
			'Poledossierpcg66.id' => array( 'type' => 'hidden' ),
			'Poledossierpcg66.name' => array( 'required' => true ),
			'Poledossierpcg66.originepdo_id' => array( 'options' => $originespdos ),
			'Poledossierpcg66.typepdo_id' => array( 'options' => $typespdos ),
            'Poledossierpcg66.isactif' => array( 'required' => true, 'type' => 'radio', 'options' => $options['Poledossierpcg66']['isactif'], 'empty' => false )
		)
	);

	echo $this->Xform->end( 'Save' );
	
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'polesdossierspcgs66',
            'action'     => 'index'
        ),
        array(
            'id' => 'Back'
        )
    );
?>