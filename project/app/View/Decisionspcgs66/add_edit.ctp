<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Decisionpcg66.id',
				'Decisionpcg66.name',
				'Decisionpcg66.nbmoisecheance',
				'Decisionpcg66.courriernotif',
				'Decisionpcg66.actif' => array( 'empty' => true )
			)
		)
	);
?>