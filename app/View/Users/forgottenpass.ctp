<?php
	$title_for_layout = 'Mot de passe oublié';
	$this->set( compact( 'title_for_layout' ) );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Html->tag( 'h1', $title_for_layout );

	echo $this->Form->create();
	echo $this->Form->inputs(
		array(
			'fieldset' => false,
			'legend' => false,
			'User.username' => array( 'label' => __d( 'users', 'User.username' ) ),
			'User.email' => array( 'label' => __d( 'users', 'User.email' ) ),
		)
	);
	echo $this->Form->submit( 'Envoyer' );
	echo $this->Form->end();
?>