<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'piecemailcui66', "Piecesmailscuis66::{$this->action}" )
	);


    echo $this->Xform->create( 'Piecemailcui66', array( 'id' => 'piecemailcui66form' ) );

	echo $this->Default2->subform(
		array(
			'Piecemailcui66.id' => array( 'type' => 'hidden' ),
			'Piecemailcui66.name' => array( 'required' => true ),
			'Piecemailcui66.actif' => array( 'type' => 'checkbox' )
		)
	);

	echo '<br />';

    // Ajout de pièces jointes
    echo "<fieldset><legend>".required( $this->Default2->label( 'Piecemailcui66.haspiecejointe' ) )."</legend>";
    echo $this->Form->input( 'Piecemailcui66.haspiecejointe', array( 'type' => 'radio', 'options' => $options['Piecemailcui66']['haspiecejointe'], 'legend' => false, 'fieldset' => false ) );

    echo '<fieldset id="filecontainer-piece" class="noborder invisible">';
        echo $this->Fileuploader->create(
            $fichiers,
            array( 'action' => 'ajaxfileupload' )
        );

        if( !empty( $fichiersEnBase ) ) {
            echo $this->Fileuploader->results(
                $fichiersEnBase
            );
        }
    echo '</fieldset>';
	echo $this->Fileuploader->validation( 'piecemailcui66form', 'Piecemailcui66', 'Pièce jointe' );
    echo '</fieldset>';

	echo $this->Html->tag(
		'div',
		 $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
		.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
		array( 'class' => 'submit noprint' )
	);

	echo $this->Xform->end();

?>
<script type="text/javascript">
document.observe( "dom:loaded", function() {
	observeDisableFieldsetOnRadioValue(
		'piecemailcui66form',
		'data[Piecemailcui66][haspiecejointe]',
		$( 'filecontainer-piece' ),
		'1',
		false,
		true
	);
} );
</script>