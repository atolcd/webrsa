<?php
	$this->pageTitle = 'Budget APRE';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	echo $this->Xform->create( 'Budgetapre', array() );

	if( isset( $this->request->data['Budgetapre']['id'] ) ) {
		echo $this->Xhtml->tag( 'div', $this->Xform->input( 'Budgetapre.id' ) );
	}
	echo $this->Xform->input( 'Budgetapre.exercicebudgetai', array( 'domain' => 'apre', 'options' => array_range( date( 'Y' ) + 1, date( 'Y' ) - 10 ), 'empty' => true ) );
	echo $this->Xform->input( 'Budgetapre.montantattretat', array( 'domain' => 'apre', 'maxlength' => 10  ) );
	echo $this->Xform->input( 'Budgetapre.ddexecutionbudge', array( 'domain' => 'apre', 'dateFormat' => 'DMY' ) );
	echo $this->Xform->input( 'Budgetapre.dfexecutionbudge', array( 'domain' => 'apre', 'dateFormat' => 'DMY' ) );

	echo $this->Xform->submit( 'Enregistrer' );
	echo $this->Xform->end();
?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'budgetsapres',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>