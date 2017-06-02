<?php
	echo $this->Default3->titleForLayout( array(), array( 'msgid' => "/Cataloguesromesv3/{$this->request->params['action']}/{$modelName}/:heading" ) );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->form(
		$fields,
		array(
			'options' => $options
		)
	);

	echo $this->Observer->dependantSelect( $dependantFields	);
	echo $this->Observer->disableFormOnSubmit();
?>