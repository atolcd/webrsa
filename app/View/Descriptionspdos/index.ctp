<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'descriptionpdo', "Descriptionspdos::{$this->action}" )
	);

	echo $this->Default2->index(
		$descriptionspdos,
		array(
			'Descriptionpdo.name',
			'Descriptionpdo.modelenotification',
			'Descriptionpdo.sensibilite',
			'Descriptionpdo.decisionpcg',
			'Descriptionpdo.dateactive',
			'Descriptionpdo.nbmoisecheance'
// 			'Descriptionpdo.declencheep'
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'descriptionspdos::edit',
				'descriptionspdos::delete' => array( 'disabled' => '\'#Descriptionpdo.occurences#\'!= "0"' )
			),
			'add' => 'descriptionspdos::add',
			'options' => $options
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