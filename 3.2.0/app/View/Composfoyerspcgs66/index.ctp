<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Compofoyerpcg66.name',
				'Compofoyerpcg66.actif',
				'/Composfoyerspcgs66/edit/#Compofoyerpcg66.id#' => array(
					'title' => true
				),
				'/Composfoyerspcgs66/delete/#Compofoyerpcg66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Compofoyerpcg66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#decisionsdossierspcgs66'
		)
	);
?>