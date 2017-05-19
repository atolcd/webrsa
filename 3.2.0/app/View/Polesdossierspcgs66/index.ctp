<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Poledossierpcg66.name',
				'Originepdo.libelle',
				'Typepdo.libelle',
				'Poledossierpcg66.isactif' => array( 'type' => 'boolean' ),
				'/Polesdossierspcgs66/edit/#Poledossierpcg66.id#' => array(
					'title' => true
				),
				'/Polesdossierspcgs66/delete/#Poledossierpcg66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Poledossierpcg66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>