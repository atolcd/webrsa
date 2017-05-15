<?php
echo $this->Xhtml->tag(
    'h1',
    $this->pageTitle = __d( 'typecontratcui66', "Typescontratscuis66::{$this->action}" )
);

echo $this->Default2->index(
    $typescontratscuis66,
    array(
        'Typecontratcui66.name',
        'Typecontratcui66.actif' => array( 'type' => 'boolean' ),
    ),
    array(
        'cohorte' => false,
        'actions' => array(
            'Typescontratscuis66::edit',
            'Typescontratscuis66::delete' => array( 'disabled' => '\'#Typecontratcui66.occurences#\'!= "0"' )
        ),
        'add' => 'Typescontratscuis66::add'
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