<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'originepdo', "Originespdos::{$this->action}" )
	)
?>

<?php
	$fields = array(
		'Originepdo.libelle'
	);

	/*if ( Configure::read( 'Cg.departement' ) == 66 ) {
		$fields[] = 'Originepdo.originepcg';
	}*/

	echo $this->Default2->index(
		$originespdos,
		$fields,
		array(
			'options' => $options,
			'cohorte' => false,
			'actions' => array(
				'Originespdos::edit',
				'Originespdos::delete' => array( 'disabled' => '\'#Originepdo.occurences#\'!= "0"' )
			),
			'add' => 'Originespdos::add',
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
