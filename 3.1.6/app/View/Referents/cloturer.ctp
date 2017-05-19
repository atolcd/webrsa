<?php
	$title_for_layout = 'Clôture en masse du référent';
	$this->set( 'title_for_layout', $title_for_layout );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<?php
	echo $this->Html->tag( 'h1', $title_for_layout );

	echo $this->Xform->create( null, array( 'inputDefaults' => array( 'domain' => 'referent' ) ) );

	echo $this->Xform->inputs(
		array(
			'fieldset' => false,
			'legend' => false,
			'Referent.referer' => array( 'type' => 'hidden' ),
			'Referent.id' => array( 'type' => 'hidden' ),
			'Referent.datecloture' => array( 'required' => true, 'type' => 'date', 'dateFormat' => 'DMY' )
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
	echo $this->Observer->disableFormOnSubmit();
?>
<div class="clearer"><hr /></div>