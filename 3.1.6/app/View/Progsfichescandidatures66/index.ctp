<?php
    echo $this->Xhtml->tag(
        'h1',
        $this->pageTitle = __d( 'progfichecandidature66', "Progsfichescandidatures66::{$this->action}" )
    );
?>
<?php
    echo $this->Default2->index(
        $progsfichescandidatures66,
        array(
            'Progfichecandidature66.name',
            'Progfichecandidature66.isactif' => array( 'type' => 'boolean' )
        ),
        array(
            'actions' => array(
                'Progsfichescandidatures66::edit',
                'Progsfichescandidatures66::delete' => array( 'disabled' => '\'#Progfichecandidature66.occurences#\' != "0"' )
            ),
            'add' => 'Progsfichescandidatures66::add'
        )
    );
?>