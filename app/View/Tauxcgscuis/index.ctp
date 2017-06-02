<?php
    echo $this->Xhtml->tag(
        'h1',
        $this->pageTitle = __d( 'tauxcgcui', "Tauxcgscuis::{$this->action}" )
    );
?>
<?php
    echo $this->Default2->index(
        $tauxcgscuis,
        array(
			'Tauxcgcui.typecui',
            'Tauxcgcui.secteurcui_id',
            'Tauxcgcui.isaci',
            'Tauxcgcui.tauxmin',
            'Tauxcgcui.tauxmax',
            'Tauxcgcui.tauxnominal'
        ),
        array(
            'actions' => array(
                'Tauxcgscuis::edit',
                'Tauxcgscuis::delete'
            ),
            'options' => $options,
            'add' => 'Tauxcgscuis::add'
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