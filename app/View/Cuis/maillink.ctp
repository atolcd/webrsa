<?php
	$domain = 'cui';
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'cui', "Cuis::{$this->action}" )
	);

	echo $this->Xform->create( null, array('id' => 'Cuimail'));

    echo '<fieldset>';
    if( !empty($mailBodySend) ) {
        echo $this->Xform->fieldValue( 'Textmailcui66.contenuexemple', $mailBodySend, true, 'textarea', array('class' => 'aere') );
    }
    if( empty( $cui['Cui']['dateenvoimail']) ) {
        echo $this->Default2->subform(
            array(
                'Cui.id' => array( 'type' => 'hidden' ),
                'Cui.dateenvoimail' => array( 'required' => true, 'type' => 'date', 'dateFormat' => 'DMY', 'empty' => false )
            )
        );
   


        echo $this->Html->tag(
            'div',
             $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
            .$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
            array( 'class' => 'submit noprint' )
        );
    }
    else { // Cas de la relance employeur !!
        
        echo $this->Xform->fieldValue( 'Cui.dateenvoimail', $this->Locale->date( 'Date::short', $cui['Cui']['dateenvoimail'] ), true, 'text', array('class' => 'aere') );   

        echo '</hr>';
        echo '<fieldset><legend>Relance</legend>';
        if( $cui['Cui']['dossiercomplet'] != '1' ) {
            if( !empty($mailBodyRelance) ) {
                echo $this->Xform->fieldValue( 'Textmailcui66.contenuexemple', $mailBodyRelance, true, 'textarea', array('class' => 'aere') );
            }
            
            if( empty($cui['Cui']['dateenvoirelance'] ) ) {
                echo $this->Default2->subform(
                    array(
                        'Cui.id' => array( 'type' => 'hidden' ),
                        'Cui.dateenvoirelance' => array( 'required' => true, 'type' => 'date', 'dateFormat' => 'DMY', 'empty' => false )
                    )
                );
            }
            else {
                echo $this->Xform->fieldValue( 'Cui.dateenvoirelance', $this->Locale->date( 'Date::short', $cui['Cui']['dateenvoirelance'] ), true, 'text', array('class' => 'aere') );   
            }

            echo '</fieldset>';
        }
        
        if( !empty($cui['Cui']['dateenvoirelance'] ) ) {
            echo $this->Html->tag(
                'div',
                 $this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
                array( 'class' => 'submit noprint' )
            );
        }
        else {
            echo $this->Html->tag(
                'div',
                 $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
                .$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
                array( 'class' => 'submit noprint' )
            );
        }
    }
    echo '</fieldset>';
	echo $this->Xform->end();
	
?>