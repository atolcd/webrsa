<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'pieceaide66', "Piecesaides66::{$this->action}" )
	)
?>
<?php
	echo $this->Default->index(
		$piecesaides66,
		array(
			'Pieceaide66.name',
		),
		array(
			'actions' => array(
				'Pieceaide66.edit',
				'Pieceaide66.delete'
			),
			'add' => 'Pieceaide66.add'
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