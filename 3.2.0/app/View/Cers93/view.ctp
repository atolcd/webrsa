<?php
	$title_for_layout = 'Visualisation du CER';
	$this->set( 'title_for_layout', $title_for_layout );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<?php
	echo $this->Html->tag( 'h1', $title_for_layout );

	require  dirname( __FILE__ ).'/_view.ctp' ;
?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'cers93',
			'action'     => 'index',
			$personne_id
		),
		array(
			'id' => 'Back'
		)
	);
?>