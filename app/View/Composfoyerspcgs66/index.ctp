<h1><?php echo $this->pageTitle = 'Compositions des foyers';?></h1>

<?php
	echo $this->Default2->index(
		$composfoyerspcgs66,
		array(
			'Compofoyerpcg66.name'
		),
		array(
			'actions' => array(
				'Composfoyerspcgs66::actif' => array( 'label' => 'Désactiver', 'condition' => '(\'#Compofoyerpcg66.actif#\' == "O")' ),
				'Composfoyerspcgs66::inactif' => array( 'label' => 'Activer', 'condition' => '(\'#Compofoyerpcg66.actif#\' != "O")'  ),
				'Composfoyerspcgs66::edit' => array( 'disabled' => '(\'#Compofoyerpcg66.actif#\' != "O" || \'#Compofoyerpcg66.occurences#\' != "0")'  ),
				'Composfoyerspcgs66::delete' => array( 'disabled' => '(\'#Compofoyerpcg66.actif#\' != "O" || \'#Compofoyerpcg66.occurences#\' != "0")'  )
			),
			'add' => array( 'Compofoyerpcg66.add' )
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'decisionsdossierspcgs66',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>