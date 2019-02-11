<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Motifcernonvalid66.name',
				'/Motifscersnonvalids66/edit/#Motifcernonvalid66.id#' => array(
					'title' => true
				),
				'/Motifscersnonvalids66/delete/#Motifcernonvalid66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motifcernonvalid66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#decisionsdossierspcgs66'
		)
	);
?>