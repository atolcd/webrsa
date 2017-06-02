<?php
    echo $this->Xhtml->tag(
        'h1',
        $this->pageTitle = __d( 'textmailcui66', "Textsmailscuis66::{$this->action}" )
    );
?>
<?php

    
    echo $this->Default2->index(
        $textsmailscuis66,
        array(
            'Textmailcui66.name',
            'Textmailcui66.sujet',
            'Textmailcui66.contenu',
            'Textmailcui66.actif' => array( 'type' => 'boolean')
        ),
        array(
            'actions' => array(
                'Textsmailscuis66::edit',
                'Textsmailscuis66::delete' => array( 'disabled' => '\'#Piecemailcui66.occurences#\'!= "0"' )
            ),
            'add' => 'Textsmailscuis66::add'
        )
    );
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'cuis',
            'action'     => 'indexparams'
        ),
        array(
            'id' => 'Back'
        )
    );
?>