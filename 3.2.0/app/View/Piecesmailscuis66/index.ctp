<?php
    echo $this->Xhtml->tag(
        'h1',
        $this->pageTitle = __d( 'piecemailcui66', "Piecesmailscuis66::{$this->action}" )
    );
?>
<?php
	echo $this->Default3->actions(
		array(
			"/Piecesmailscuis66/add/0" => array(
				'disabled' => !$this->Permissions->check( 'Piecesmailscuis66', 'add' ),
			),
		)
	);

    echo $this->Default2->index(
        $piecesmailscuis66,
        array(
            'Piecemailcui66.name',
            'Piecemailcui66.haspiecejointe' => array( 'type' => 'boolean' ),
            'Piecemailcui66.actif' => array( 'type' => 'boolean' ),
            'Fichiermodule.nb_fichiers_lies' => array( 'type' => 'text', 'domain' => 'piecemailcui66', 'class' => 'center' ),
        ),
        array(
            'actions' => array(
                'Piecesmailscuis66::edit',
                'Piecesmailscuis66::delete' => array( 'disabled' => '\'#piecemailcui66.occurence#\'== true' )
            ),
        )
    );
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'parametrages',
            'action'     => 'index',
            '#'     => 'cuis66'
        ),
        array(
            'id' => 'Back'
        )
    );
?>