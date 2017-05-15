<?php
    echo $this->Xhtml->tag(
        'h1',
        $this->pageTitle = __d( 'typeaideapre66', "Typesaidesapres66::{$this->action}" )
    )
?>
<?php
    echo $this->Default2->index(
        $typesaidesapres66,
        array(
            'Themeapre66.name',
            'Typeaideapre66.name',
            'Typeaideapre66.isincohorte'
        ),
        array(
            'actions' => array(
                'Typesaidesapres66::edit',
                'Typesaidesapres66::delete' => array( 'disabled' => '"#Typeaideapre66.occurences#" != "0"' )
            ),
            'add' => 'Typesaidesapres66::add',
            'options' => $options
        )
    );

    echo $this->Default->button(
        'back',
        array(
            'controller' => 'apres66',
            'action'     => 'indexparams'
        ),
        array(
            'id' => 'Back'
        )
    );
?>