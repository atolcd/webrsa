<?php
    echo $this->Xhtml->tag(
        'h1',
        $this->pageTitle = __d( 'piecemail', "Piecesemails::{$this->action}" )
    );
?>
<?php
	echo $this->Default3->actions(
		array(
			"/Piecesemails/add/0" => array(
				'disabled' => !$this->Permissions->check( 'Piecesemails', 'add' ),
			),
		)
	);

   echo $this->Default2->index(
        $piecesemails,
        array(
            'Piecemail.name',
            'Piecemail.actif' => array(
				'type' => 'boolean'
			),
            'Fichiermodule.nb_fichiers_lies' => array(
				'type' => 'text',
				'domain' => 'piecemail',
				'class' => 'center'
			),
        ),
        array(
            'actions' => array(
                'Piecesemails::edit',
                'Piecesemails::delete' => array( 'disabled' => '\'#piecemail.occurence#\'== true' )
            ),
        )
    );

?>