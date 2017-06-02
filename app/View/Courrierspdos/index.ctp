<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'courrierpdo', "Courrierspdos::{$this->action}" )
	);

	echo $this->Default2->index(
		$courrierspdos,
		array(
			'Courrierpdo.name',
			'Courrierpdo.modeleodt'
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'courrierspdos::edit',
				'courrierspdos::delete',
			),
			'add' => 'courrierspdos::add'
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