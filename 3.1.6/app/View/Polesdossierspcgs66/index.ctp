<?php
    echo $this->Xhtml->tag(
        'h1',
        $this->pageTitle = __d( 'poledossierpcg66', "Polesdossierspcgs66::{$this->action}" )
    );
?>
<?php

    echo $this->Default2->index(
        $polesdossierspcgs66,
        array(
            'Poledossierpcg66.name',
            'Originepdo.libelle' => array( 'label' => __d( 'poledossierpcg66', 'Poledossierpcg66.originepdo_id' ) ),
            'Typepdo.libelle' => array( 'label' => __d( 'poledossierpcg66', 'Poledossierpcg66.typepdo_id' ) ),
            'Poledossierpcg66.isactif' => array( 'type' => 'boolean' )
        ),
        array(
            'actions' => array(
                'Polesdossierspcgs66::edit',
                'Polesdossierspcgs66::delete' => array( 'disabled' => '\'#Poledossierpcg66.occurences#\' != "0"' )
            ),
            'add' => 'Polesdossierspcgs66::add'
        )
    );
    
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'pdos',
            'action'     => 'index'
        ),
        array(
            'id' => 'Back'
        )
    );
?>