<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Sujetcer93.name',
				'Sujetcer93.isautre' => array( 'type' => 'boolean' ),
				'Sujetcer93.actif' => array( 'type' => 'boolean'),
				'/Sujetscers93/edit/#Sujetcer93.id#' => array(
					'title' => true
				),
				'/Sujetscers93/delete/#Sujetcer93.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Sujetcer93.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#contratsinsertion'
		)
	);
?>