<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'raisonsocialepartenairecui66', "Raisonssocialespartenairescuis66::{$this->action}" )
	)
?>

<?php
	$fields = array(
		'Raisonsocialepartenairecui66.name'
	);

	echo $this->Default2->index(
		$raisonssocialespartenairescuis66,
		$fields,
		array(
			'cohorte' => false,
			'actions' => array(
				'Raisonssocialespartenairescuis66::edit',
				'Raisonssocialespartenairescuis66::delete' => array( 'disabled' => '\'#Raisonsocialepartenairecui66.occurences#\'!= "0"' )
			),
			'add' => 'Raisonssocialespartenairescuis66::add'
		)
	);
	echo '<br />';
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'cuis',
			'action'     => 'indexparams'
		),
		array(
			'id' => 'Back'
		)
	);
?>