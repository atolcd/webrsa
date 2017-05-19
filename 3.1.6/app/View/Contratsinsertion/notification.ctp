<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'contratinsertion', "Contratsinsertion::{$this->action}" )
	);

	echo '<fieldset>';
	echo $this->Xform->create( 'Contratinsertion', array( 'id' => 'notificationcontratform' ) );
	if( Set::check( $this->request->data, 'Contratinsertion.id' ) ){
		echo $this->Xform->input( 'Contratinsertion.id', array( 'type' => 'hidden' ) );
	}
	echo $this->Xform->input( 'Contratinsertion.personne_id', array( 'type' => 'hidden', 'value' => $personne_id ) );

	echo $this->Default2->subform(
		array(
			'Contratinsertion.datenotification' => array( 'type' => 'date', 'maxYear'=>date('Y')+2, 'minYear'=>date('Y')-3 , )
		)
	);
	echo '</fieldset>';

	echo "<div class='submit'>";
		echo $this->Form->submit( 'Enregistrer', array( 'div'=>false ) );
		echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div'=>false ) );
	echo "</div>";

	echo $this->Form->end();
?>