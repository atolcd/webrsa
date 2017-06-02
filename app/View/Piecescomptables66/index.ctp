<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'piececomptable66', "Piecescomptables66::{$this->action}" )
	)
?>
<?php
	echo $this->Default->index(
		$piecescomptables66,
		array(
			'Piececomptable66.name',
		),
		array(
			'actions' => array(
				'Piececomptable66.edit',
				'Piececomptable66.delete'
			),
			'add' => 'Piececomptable66.add'
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