<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'manifestationbilanparcours66', "Manifestationsbilansparcours66::{$this->action}" )
	);
?>
<?php
	echo $this->Xform->create( null, array( 'inputDefaults' => array( 'domain' => 'manifestationbilanparcours66' ), 'id' => 'manifestation' ) );

	echo $this->Xform->inputs(
		array(
			'fieldset' => false,
			'legend' => false,
			'Manifestationbilanparcours66.id' => array( 'type' => 'hidden' ),
			'Manifestationbilanparcours66.bilanparcours66_id' => array( 'type' => 'hidden', 'value' => $bilanparcours66_id )
		)
	);

	echo $this->Default2->subform(
		array(
			'Manifestationbilanparcours66.commentaire',
			'Manifestationbilanparcours66.datemanifestation'
		)
	);
?>
<?php
	echo $this->Html->tag(
		'div',
		 $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
		.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
		array( 'class' => 'submit noprint' )
	);

	echo $this->Xform->end();
?>