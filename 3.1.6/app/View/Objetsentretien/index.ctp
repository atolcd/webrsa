<h1><?php echo $this->pageTitle = 'Liste des objets d\'entretien';?></h1>

<?php
	echo $this->Default2->index(
		$objetsentretien,
		array(
			'Objetentretien.name'
		),
		array(
			'actions' => array(
				'Objetsentretien::edit',
				'Objetsentretien::delete'
			),
			'add' => array( 'Objetsentretien.add' )
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'parametrages',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>