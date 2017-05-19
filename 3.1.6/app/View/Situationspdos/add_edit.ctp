<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'situationpdo', "Situationspdos::{$this->action}" )
	)
?>

<?php
	echo $this->Xform->create();

	echo $this->Default2->subform(
		array(
			'Situationpdo.id' => array( 'type' => 'hidden' ),
			'Situationpdo.libelle' => array( 'required' => true ),
            'Situationpdo.isactif' => array( 'type' => 'radio', 'empty' => false )
		),
        array(
            'options' => $options
        )
	);
?>

<?php
	echo $this->Xform->end( 'Save' );

    echo $this->Default->button(
        'back',
        array(
            'controller' => 'situationspdos',
            'action'     => 'index'
        ),
        array(
            'id' => 'Back'
        )
    );
?>
