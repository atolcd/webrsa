<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'statutdecisionpdo', "Statutsdecisionspdos::{$this->action}" )
	)
?>

<?php
	echo $this->Default->index(
		$statutsdecisionspdos,
		array(
			'Statutdecisionpdo.libelle'
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Statutdecisionpdo.edit',
				'Statutdecisionpdo.delete',
			),
			'add' => 'Statutdecisionpdo.add',
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'parametrages',
			'action'     => 'index',
			'#'     => 'pdos',
		),
		array(
			'id' => 'Back'
		)
	);
?>