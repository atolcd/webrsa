<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Motifrupturecui66.name',
				'Motifrupturecui66.actif' => array( 'type' => 'boolean' ),
				'/Motifsrupturescuis66/edit/#Motifrupturecui66.id#' => array(
					'title' => true
				),
				'/Motifsrupturescuis66/delete/#Motifrupturecui66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motifrupturecui66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#cuis'
		)
	);
?>