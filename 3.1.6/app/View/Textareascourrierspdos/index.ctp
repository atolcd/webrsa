<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'textareacourrierpdo', "Textareascourrierspdos::{$this->action}" )
	)
?>

<?php
	echo $this->Default2->index(
		$textareascourrierspdos,
		array(
			'Courrierpdo.name',
			'Textareacourrierpdo.nomchampodt',
			'Textareacourrierpdo.name',
			'Textareacourrierpdo.ordre'
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'textareascourrierspdos::edit',
				'textareascourrierspdos::delete',
			),
			'add' => 'textareascourrierspdos::add'
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