<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'metierexerce', "Metiersexerces::{$this->action}" )
	)
?>

<?php
	$fields = array(
		'Metierexerce.name'
	);
	echo $this->Default2->index(
		$metiersexerces,
		$fields,
		array(
			'cohorte' => false,
			'actions' => array(
				'Metiersexerces::edit',
				'Metiersexerces::delete',
			),
			'add' => 'Metiersexerces::add'
		)
	);
	echo '<br />';
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'cers93',
			'action'     => 'indexparams'
		),
		array(
			'id' => 'Back'
		)
	);
?>