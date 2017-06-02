<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Orgtransmisdossierpcg66.name',
				'Poledossierpcg66.name',
				'Orgtransmisdossierpcg66.generation_auto' => array( 'type' => 'boolean' ),
				'Orgtransmisdossierpcg66.isactif' => array( 'type' => 'boolean' ),
				'/Orgstransmisdossierspcgs66/edit/#Orgtransmisdossierpcg66.id#' => array(
					'title' => true
				),
				'/Orgstransmisdossierspcgs66/delete/#Orgtransmisdossierpcg66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Orgtransmisdossierpcg66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>