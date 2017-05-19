<?php
    echo $this->Xhtml->tag(
        'h1',
        $this->pageTitle = __d( 'secteurcui', "Secteurscuis::{$this->action}" )
    );
?>
<?php


    echo $this->Default2->index(
        $secteurscuis,
        array(
            'Secteurcui.name',
            'Secteurcui.isnonmarchand' => array( 'type' => 'boolean' )
        ),
        array(
            'actions' => array(
                'Secteurscuis::edit',
                'Secteurscuis::delete' => array( 'disabled' => '\'#Secteurcui.occurences#\'!= "0"' )
            ),
            'add' => 'Secteurscuis::add'
        )
    );
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'parametrages',
            'action'     => 'index',
            '#'     => 'cuis'
        ),
        array(
            'id' => 'Back'
        )
    );
?>