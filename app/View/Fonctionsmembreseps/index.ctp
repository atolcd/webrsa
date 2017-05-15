<h1><?php echo $this->pageTitle = 'Liste des fonctions des membres des E.P.';?></h1>

<?php
	echo $this->Default2->index(
		$fonctionmembreeps,
		array(
			'Fonctionmembreep.name'
		),
		array(
			'actions' => array(
				'Fonctionsmembreseps::edit',
				'Fonctionsmembreseps::delete'
			),
			'add' => array( 'Fonctionsmembreseps.add' ),
			'options' => $options
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'gestionseps',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>