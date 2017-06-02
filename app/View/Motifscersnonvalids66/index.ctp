<?php
    echo $this->Xhtml->tag(
        'h1',
        $this->pageTitle = __d( 'motifcernonvalid66', "Motifscersnonvalids66::{$this->action}" )
    );
?>
<?php
	$motifscersnonvalids66 = !empty($motifscersnonvalids66) ? $motifscersnonvalids66 : array();
    echo $this->Default2->index(
        $motifscersnonvalids66,
        array(
            'Motifcernonvalid66.name'
        ),
        array(
            'actions' => array(
                'Motifscersnonvalids66::edit',
                'Motifscersnonvalids66::delete'
            ),
            'add' => 'Motifscersnonvalids66::add'
        )
    );

    echo $this->Default->button(
        'back',
        array(
            'controller' => 'parametrages',
            'action'     => 'index'
        ),
        array(
            'id' => 'Back'
        )
    );
?>