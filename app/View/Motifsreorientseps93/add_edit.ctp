<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	};

	echo $this->Default3->titleForLayout( $this->request->data );

	echo $this->Default3->form(
		$this->Translator->normalize(
			array(
				'Motifreorientep93.id',
				'Motifreorientep93.name'
			)
		)
	);

	echo $this->Observer->disableFormOnSubmit();
?>