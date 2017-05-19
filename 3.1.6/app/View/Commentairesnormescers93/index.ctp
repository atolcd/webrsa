<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'commentairenormecer93', "Commentairesnormescers93::{$this->action}" )
	)
?>

<?php
	echo $this->Default2->index(
		$commentairesnormescers93,
		array(
			'Commentairenormecer93.name',
			'Commentairenormecer93.isautre' => array( 'type' => 'boolean' )
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Commentairesnormescers93::edit',
				'Commentairesnormescers93::delete',
			),
			'add' => 'Commentairesnormescers93::add'
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