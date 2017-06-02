<?php
    echo $this->Xhtml->tag(
        'h1',
        $this->pageTitle = __d( 'orgtransmisdossierpcg66', "Orgstransmisdossierspcgs66::{$this->action}" )
    );
?>
<?php

    
    echo $this->Default2->index(
        $orgstransmisdossierspcgs66,
        array(
            'Orgtransmisdossierpcg66.name',
            'Poledossierpcg66.name',
            'Orgtransmisdossierpcg66.isactif'
        ),
        array(
            'actions' => array(
                'Orgstransmisdossierspcgs66::edit',
                'Orgstransmisdossierspcgs66::delete' => array( 'disabled' => '\'#Orgtransmisdossierpcg66.occurences#\'!= "0"' )
            ),
            'add' => 'Orgstransmisdossierspcgs66::add',
            'options' => $options
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