<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if(isset($title) && !empty($title)) {
		echo "<h1>" . $title . "</h1>";
		echo "<p>" . $subtitle . "</p>";
	} else {
		echo $this->Default3->titleForLayout();
	}

	echo $this->Default3->form(
		$this->Translator->normalize(
			array(
				'User.username' => array( 'required' => false ),
				'User.email' => array( 'required' => false, 'type' => 'text' )
			)
		),
		array(
			'buttons' => array( 'Envoyer', 'Cancel' )
		)
	);

	echo $this->Observer->disableFormOnSubmit();
?>