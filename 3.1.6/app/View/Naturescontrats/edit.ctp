<?php
	$title_for_layout = ( ( $this->action == 'add' ) ? 'Ajout d\'une nature de contrat' : 'Modification d\'une nature de contrat' );
	$this->set( 'title_for_layout', $title_for_layout );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
	

	echo $this->Html->tag( 'h1', $title_for_layout );
	echo $this->Xform->create( null, array( 'inputDefaults' => array( 'domain' => 'naturecontrat' ) ) );

	
	echo $this->Xform->inputs(
		array(
			'fieldset' => false,
			'legend' => false,
			'Naturecontrat.id' => array( 'type' => 'hidden' ),
			'Naturecontrat.name' => array( 'type' => 'text' ),
			'Naturecontrat.isduree' => array( 'type' => 'checkbox', 'options' => $options['Naturecontrat']['isduree'] )
		)
	);

	echo $this->Html->tag(
		'div',
		 $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
		.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
		array( 'class' => 'submit noprint' )
	);

	echo $this->Xform->end();
?>
