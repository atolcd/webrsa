<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'coderomemetierdsp66', "Codesromemetiersdsps66::{$this->action}" )
	);

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xform->create( null, array() );

	if (isset($this->request->data['Coderomemetierdsp66']['id'])) {
		echo $this->Form->input('Coderomemetierdsp66.id', array('type'=>'hidden'));
	}

	echo $this->Default->subform(
		array(
			'Coderomemetierdsp66.code' => array( 'required' => true ),
			'Coderomemetierdsp66.name' => array( 'required' => true ),
			'Coderomemetierdsp66.coderomesecteurdsp66_id' => array( 'required' => true, 'options' => $options['Coderomesecteurdsp66'] )
		)
	);

	echo $this->Xform->end( __( 'Save' ) );
	echo $this->Default->button(
		'back',
		array('controller' => 'codesromemetiersdsps66', 'action' => 'index'),
		array('id' => 'Back')
	);
?>