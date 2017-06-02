<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'naturecontrat', "Naturescontrats::{$this->action}" )
	)
?>
<?php
	$fields = array(
		'Naturecontrat.name'
	);
	echo $this->Default2->index(
		$naturescontrats,
		$fields,
		array(
			'cohorte' => false,
			'actions' => array(
				'Naturescontrats::edit',
				'Naturescontrats::delete',
			),
			'add' => 'Naturescontrats::add'
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