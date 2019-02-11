<?php
	$this->pageTitle = __d( 'actioncandidat_partenaire', "ActionscandidatsPartenaires::{$this->action}" );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	echo $this->Default->form(
		array(
			'ActioncandidatPartenaire.actioncandidat_id' => array( 'type' => 'select', 'empty' => true, 'required' => true ),
			'ActioncandidatPartenaire.partenaire_id' => array( 'type' => 'select', 'empty' => true, 'required' => true )
		),
		array(
			'actions' => array(
				'ActioncandidatPartenaire.save',
				'ActioncandidatPartenaire.cancel'
			),
			'options' => $options
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'actionscandidats_partenaires',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>