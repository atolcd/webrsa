<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typecourrierpcg66.name',
				'Typecourrierpcg66.isactif' => array( 'type' => 'boolean' ),
				'/Typescourrierspcgs66/edit/#Typecourrierpcg66.id#' => array(
					'title' => true
				),
				'/Typescourrierspcgs66/delete/#Typecourrierpcg66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typecourrierpcg66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#courrierspcgs66'
		)
	);
?>