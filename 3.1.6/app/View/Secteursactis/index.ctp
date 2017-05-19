<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'secteuracti', "Secteursactis::{$this->action}" )
	)
?>

<?php
	$fields = array(
		'Secteuracti.name'
	);
	echo $this->Default2->index(
		$secteursactis,
		$fields,
		array(
			'cohorte' => false,
			'actions' => array(
				'Secteursactis::edit',
				'Secteursactis::delete',
			),
			'add' => 'Secteursactis::add'
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