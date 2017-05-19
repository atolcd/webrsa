<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'pieceaide66', "Piecesaides66::{$this->action}" )
	)
?>
<?php
	echo $this->Default->form(
		array(
			'Pieceaide66.name' => array('required' => true)
		)
	);
?>