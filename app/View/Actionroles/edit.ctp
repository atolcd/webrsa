<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
	
	function multipleCheckbox( $View, $path, $options, $class = '' ) {
		$name = model_field($path);
		return $View->Xform->input($path, array(
			'label' => __m($path), 
			'type' => 'select', 
			'multiple' => 'checkbox', 
			'options' => $options[$name[0]][$name[1]],
			'class' => $class
		));
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'actionrole', "Actionroles::{$this->action}" )
	);

    echo $this->Xform->create();
    echo $this->Default2->subform(
        array(
            'Actionrole.id' => array( 'type' => 'hidden' ),
            'Actionrole.name' => array( 'type' => 'text', 'required' => true ),
            'Actionrole.role_id' => array( 'type' => 'select', 'required' => true ),
            'Actionrole.categorieactionrole_id' => array( 'type' => 'select', 'required' => true ),
            'Actionrole.description' => array( 'type' => 'textarea' ),
            'Actionrole.url' => array( 'type' => 'textarea', 'required' => true ),
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