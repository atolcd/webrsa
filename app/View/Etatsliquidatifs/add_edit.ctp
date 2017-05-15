<?php
	$this->pageTitle = 'État liquidatif';
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	echo $this->Xform->create( 'Etatliquidatif' );
	if( $this->action == 'edit' && isset( $this->request->data['Etatliquidatif']['id'] ) ) {
		echo $this->Xhtml->tag( 'div', $this->Xform->input( 'Etatliquidatif.id' ) );
	}

	echo $this->Xform->input( 'Etatliquidatif.budgetapre_id', array( 'required' => true, 'options' => $budgetsapres, 'empty' => true, 'domain' => 'apre' ) );
	echo $this->Xform->input( 'Etatliquidatif.typeapre', array( 'required' => true, 'options' => $typesapres /* FIXME */, 'empty' => true, 'domain' => 'apre' ) );
	echo $this->Xform->input( 'Etatliquidatif.commentaire', array( 'domain' => 'apre', 'required' => true ) );

	echo $this->Xform->submit( 'Enregistrer' );
	echo $this->Xform->end();
?>