<h1><?php echo $this->pageTitle = 'Décisions';?></h1>

<?php
	echo $this->Default2->index(
		$decisionspcgs66,
		array(
			'Decisionpcg66.name',
			'Decisionpcg66.nbmoisecheance',
			'Decisionpcg66.courriernotif'
		),
		array(
			'actions' => array(
				'Decisionspcgs66::actif' => array( 'label' => 'Désactiver', 'condition' => '(\'#Decisionpcg66.actif#\' == "O")' ),
				'Decisionspcgs66::inactif' => array( 'label' => 'Activer', 'condition' => '(\'#Decisionpcg66.actif#\' != "O")'  ),
				'Decisionspcgs66::edit' => array( 'disabled' => '(\'#Decisionpcg66.actif#\' != "O" || \'#Decisionpcg66.occurences#\' != "0")'  ),
				'Decisionspcgs66::delete'  => array( 'disabled' => '(\'#Decisionpcg66.actif#\' != "O" || \'#Decisionpcg66.occurences#\' != "0")'  )
			),
			'add' => array( 'Decisionpcg66.add' )
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