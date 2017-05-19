<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'traitementtypepdo', "Traitementstypespdos::{$this->action}" )
	)
?>

<?php
	echo $this->Default2->index(
		$traitementstypespdos,
		array(
			'Traitementtypepdo.name'
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Traitementstypespdos::edit',
				'Traitementstypespdos::delete',
			),
			'add' => 'Traitementstypespdos::add',
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'pdos',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>
