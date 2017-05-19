<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'piececomptable66', "Piecescomptables66::{$this->action}" )
	)
?>
<?php
	echo $this->Default->form(
		array(
			'Piececomptable66.name' => array('required' => true)
		)
	);
?>