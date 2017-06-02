<?php
	foreach( $erreurs as $erreur => $status ) {
		if( true === $status ) {
			echo $this->Html->tag( 'p', $erreur, array( 'class' => 'error' ) );
		}
	}

	if( false === array_search( true, $erreurs, true ) ) {
		echo $this->element(
			'WebrsaParametrages/index',
			array(
				'cells' => array(
					'Questionpcg66.defautinsertion',
					'Compofoyerpcg66.name',
					'Questionpcg66.recidive',
					'Questionpcg66.phase',
					'Decisionpcg66.name',
					'/Questionspcgs66/edit/#Questionpcg66.id#' => array(
						'title' => true
					),
					'/Questionspcgs66/delete/#Questionpcg66.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Questionpcg66.has_linkedrecords#"'
					)
				),
				'backUrl' => '/Parametrages/index/#decisionsdossierspcgs66'
			)
		);
	}
?>