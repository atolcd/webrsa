<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'piecemail', "Piecesemails::{$this->action}" )
	);


    echo $this->Xform->create( 'Piecemail', array( 'id' => 'piecemailform' ) );

	echo $this->Default2->subform(
		array(
			'Piecemail.id' => array( 'type' => 'hidden' ),
			'Piecemail.name' => array( 'required' => true ),
			'Piecemail.actif' => array( 'type' => 'checkbox' )
		)
	);

	echo '<br />';

    // Ajout de pièces jointes
    echo "<fieldset><legend>".required( $this->Default2->label( 'Piecemail.haspiecejointe' ) )."</legend>";
    echo "<div style='display: none;'>";
    echo $this->Form->input( 'Piecemail.haspiecejointe', array( 'type' => 'radio', 'options' => $options['Piecemail']['haspiecejointe'], 'legend' => false, 'fieldset' => false, 'value' => 1 ) );
	echo "</div>";
    echo '<fieldset id="filecontainer-piece" class="noborder invisible">';
        echo $this->Fileuploader->create(
            $fichiers,
            array( 'action' => 'ajaxfileupload' )
        );

        echo $this->Fileuploader->results(
            $fichiersEnBase
        );
    echo '</fieldset>';
	echo $this->Fileuploader->validation( 'piecemailform', 'Piecemail', 'Pièce jointe' );
    echo '</fieldset>';

	echo $this->Html->tag(
		'div',
		 $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
		.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
		array( 'class' => 'submit noprint' )
	);

	echo $this->Xform->end();

?>