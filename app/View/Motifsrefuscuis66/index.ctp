<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Motifrefuscui66.name',
				'Motifrefuscui66.actif' => array( 'type' => 'boolean' ),
				'/Motifsrefuscuis66/edit/#Motifrefuscui66.id#' => array(
					'title' => true
				),
				'/Motifsrefuscuis66/delete/#Motifrefuscui66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motifrefuscui66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#cuis'
		)
	);
?>