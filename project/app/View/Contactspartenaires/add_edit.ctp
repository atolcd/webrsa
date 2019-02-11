<?php
	$this->pageTitle = __d( 'contactpartenaire', "Contactspartenaires::{$this->action}" );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
?>

<?php
	echo $this->Default->form(
		array(
			'Contactpartenaire.qual' => array( 'options' => $qual, 'empty' => true, 'required' => true ),
			'Contactpartenaire.nom' => array( 'required' => true ),
			'Contactpartenaire.prenom' => array( 'required' => true ),
			'Contactpartenaire.numtel',
			'Contactpartenaire.numfax',
			'Contactpartenaire.email',
			'Contactpartenaire.partenaire_id' => array( 'type' => 'select', 'empty' => true, 'required' => true )
		),
		array(
			'actions' => array(
				'Contactpartenaire.save',
				'Contactpartenaire.cancel',
			),
			'options' => $options
		)
	);
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'contactspartenaires',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>