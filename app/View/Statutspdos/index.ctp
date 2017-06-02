<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'statutpdo', "Statutspdos::{$this->action}" )
	)
?>

<?php
	echo $this->Default2->index(
		$statutspdos,
		array(
			'Statutpdo.libelle',
			'Statutpdo.isactif'
		),
		array(
			'cohorte' => false,
            'options' => $options,
			'actions' => array(
				'Statutspdos::edit',
				'Statutspdos::delete' => array( 'disabled' => '\'#Statutpdo.occurences#\'!= "0"' )
			),
			'add' => 'Statutspdos::add',
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
