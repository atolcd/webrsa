<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'coderomemetierdsp66', "Codesromemetiersdsps66::{$this->action}" )
	);

	echo $this->Default->index(
		$codesromemetiersdsps66,
		array(
            'Coderomemetierdsp66.code',
			'Coderomemetierdsp66.name',
			'Coderomesecteurdsp66.intitule' => array( 'type' => 'text', 'domain' => 'coderomemetierdsp66' )
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Coderomemetierdsp66.edit',
				'Coderomemetierdsp66.delete',
			),
			'add' => 'Coderomemetierdsp66.add',
			'options' => $options
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'gestionsdsps',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>
