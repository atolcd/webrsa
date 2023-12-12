<?php
	/**
	 * Paramètres utilisés:
	 *	- fields: obligatoire, les champs du formulaire
	 *	- options: facultatif, les options à utiliser dans le formulaire
	 */
	$options = isset( $options ) ? $options : array();

	// -------------------------------------------------------------------------

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	};

	echo $this->Default3->titleForLayout( $this->request->data );

	echo $this->FormValidator->generateJavascript();


	debug($fields);
	echo $this->Default3->form(
		$this->Translator->normalize( $fields ),
		array( 'options' => $options )
	);

	echo $this->Observer->disableFormOnSubmit();
?>