<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'coderomesecteurdsp66', "Codesromesecteursdsps66::{$this->action}" )
	);

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xform->create( null, array() );

	if (isset($this->request->data['Coderomesecteurdsp66']['id'])) {
		echo $this->Form->input('Coderomesecteurdsp66.id', array('type'=>'hidden'));
	}

	echo $this->Default->subform(
		array(
			'Coderomesecteurdsp66.code' => array( 'required' => true ),
			'Coderomesecteurdsp66.name' => array( 'required' => true )
		)
	);

	echo $this->Xform->end( __( 'Save' ) );
	echo $this->Default->button(
		'back',
		array('controller' => 'codesromesecteursdsps66', 'action' => 'index'),
		array('id' => 'Back')
	);
?>