<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Modeletypecourrierpcg66.name',
				'Typecourrierpcg66.name',
				'Modeletypecourrierpcg66.modeleodt',
				'Modeletypecourrierpcg66.ismontant' => array( 'type' => 'boolean' ),
				'Modeletypecourrierpcg66.isdates' => array( 'type' => 'boolean' ),
				'Modeletypecourrierpcg66.isactif'  => array( 'type' => 'boolean' ),
				'/Modelestypescourrierspcgs66/edit/#Modeletypecourrierpcg66.id#' => array(
					'title' => true
				),
				'/Modelestypescourrierspcgs66/delete/#Modeletypecourrierpcg66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Modeletypecourrierpcg66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#courrierspcgs66'
		)
	);
?>