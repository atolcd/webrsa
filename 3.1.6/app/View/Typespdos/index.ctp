<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'typepdo', "Typespdos::{$this->action}" )
	)
?>

<?php
	$fields = array(
		'Typepdo.libelle'
	);

	/*if ( Configure::read( 'Cg.departement' ) == 66 ) {
		$fields[] = 'Typepdo.originepcg';
	}*/

	echo $this->Default2->index(
		$typespdos,
		$fields,
		array(
			'options' => $options,
			'cohorte' => false,
			'actions' => array(
				'Typespdos::edit',
				'Typespdos::delete' => array( 'disabled' => '\'#Typepdo.occurences#\'!= "0"' )
			),
			'add' => 'Typespdos::add',
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