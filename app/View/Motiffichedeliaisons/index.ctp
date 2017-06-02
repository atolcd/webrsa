<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Motiffichedeliaison.name',
				'Motiffichedeliaison.actif' => array( 'type' => 'boolean' ),
				'/Motiffichedeliaisons/edit/#Motiffichedeliaison.id#' => array(
					'title' => true
				),
				'/Motiffichedeliaisons/delete/#Motiffichedeliaison.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motiffichedeliaison.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#fichedeliaisons'
		)
	);
?>