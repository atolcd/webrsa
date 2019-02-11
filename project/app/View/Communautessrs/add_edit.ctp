<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	echo $this->Default3->subform(
		array(
			'Communautesr.id' => array( 'type' => 'hidden' ),
			'Communautesr.name',
			'Communautesr.actif' => array( 'empty' => true )
		),
		array(
			'options' => $options
		)
	);

	$checkboxes = $this->Default3->subform(
		array(
			'Structurereferente.Structurereferente' => array(
				'label' => false,
				'multiple' => 'checkbox',
				'class' => 'divideInto2Columns'
			)
		),
		array(
			'options' => $options
		)
	);

	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __m( 'Structurereferente.Structurereferente' ) )
			.( isset( $checkedError ) ? $this->Html->tag( 'div', $checkedError, array( 'class' => 'error-message' ) ) : null )
			.$checkboxes,
		array( 'class' => isset( $checkedError ) ? 'input select required error' : 'input select required' )
	);


	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "communautesr_{$this->request->params['action']}_form" ) );
?>