<?php
echo $this->Xhtml->tag(
    'h1',
    $this->pageTitle = __d( 'motifsuspensioncui66', "Motifssuspensioncuis66::{$this->action}" )
);

echo $this->Default2->index(
    $motifssuspensioncuis66,
    array(
        'Motifsuspensioncui66.name',
        'Motifsuspensioncui66.actif' => array( 'type' => 'boolean' ),
    ),
    array(
        'cohorte' => false,
        'actions' => array(
            'Motifssuspensioncuis66::edit',
            'Motifssuspensioncuis66::delete' => array( 'disabled' => '\'#Motifsuspensioncui66.occurences#\'!= "0"' )
        ),
        'add' => 'Motifssuspensioncuis66::add'
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