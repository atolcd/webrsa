<?php
    echo $this->Xhtml->tag(
        'h1',
        $this->pageTitle = __d( 'modeletypecourrierpcg66', "Modelestypescourrierspcgs66::{$this->action}" )
    );
?>
<?php
    echo $this->Default2->index(
        $modelestypescourrierspcgs66,
        array(
            'Modeletypecourrierpcg66.name',
            'Typecourrierpcg66.name',
            'Modeletypecourrierpcg66.modeleodt',
            'Modeletypecourrierpcg66.ismontant' => array( 'type' => 'boolean' ),
            'Modeletypecourrierpcg66.isdates' => array( 'type' => 'boolean' ),
            'Modeletypecourrierpcg66.isactif'  => array( 'type' => 'boolean' )
        ),
        array(
            'actions' => array(
                'Modelestypescourrierspcgs66::edit',
                'Modelestypescourrierspcgs66::delete' => array( 'disabled' => '\'#Modeletypecourrierpcg66.occurences#\' != "0"')
            ),
            'add' => 'Modelestypescourrierspcgs66::add'
        )
    );
    
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'courrierspcgs66',
            'action'     => 'index'
        ),
        array(
            'id' => 'Back'
        )
    );
?>