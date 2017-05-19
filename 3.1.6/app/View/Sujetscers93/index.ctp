<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'sujetcer93', "Sujetscers93::{$this->action}" )
	)
?>

<?php
	$fields = array(
		'Sujetcer93.name',
		'Sujetcer93.isautre' => array( 'type' => 'boolean' ),
		'Sujetcer93.actif' => array( 'type' => 'boolean')
	);

	echo $this->Default2->index(
		$sujetscers93,
		$fields,
		array(
			'cohorte' => false,
			'actions' => array(
				'Sujetscers93::edit',
				'Sujetscers93::delete',
			),
			'add' => 'Sujetscers93::add'
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