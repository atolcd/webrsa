<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'soussujetcer93', "Soussujetscers93::{$this->action}" )
	)
?>

<?php
	$fields = array(
		'Sujetcer93.name',
		'Soussujetcer93.name',
		'Soussujetcer93.isautre' => array( 'type' => 'boolean' ),
		'Soussujetcer93.actif' => array( 'type' => 'boolean')
	);
	echo $this->Default2->index(
		$soussujetscers93,
		$fields,
		array(
			'cohorte' => false,
			'actions' => array(
				'Soussujetscers93::edit',
				'Soussujetscers93::delete',
			),
			'add' => 'Soussujetscers93::add'
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