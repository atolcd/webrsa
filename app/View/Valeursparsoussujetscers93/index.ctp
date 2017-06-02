<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'valeurparsoussujetcer93', "Valeursparsoussujetscers93::{$this->action}" )
	)
?>

<?php
	$fields = array(
		'Soussujetcer93.name',
		'Valeurparsoussujetcer93.name',
		'Valeurparsoussujetcer93.isautre' => array( 'type' => 'boolean'),
		'Valeurparsoussujetcer93.actif' => array( 'type' => 'boolean')
	);
	echo $this->Default2->index(
		$valeursparsoussujetscers93,
		$fields,
		array(
			'cohorte' => false,
			'actions' => array(
				'Valeursparsoussujetscers93::edit',
				'Valeursparsoussujetscers93::delete',
			),
			'add' => 'Valeursparsoussujetscers93::add'
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