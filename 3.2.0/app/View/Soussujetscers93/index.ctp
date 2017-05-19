<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Sujetcer93.name',
				'Soussujetcer93.name',
				'Soussujetcer93.isautre' => array( 'type' => 'boolean' ),
				'Soussujetcer93.actif' => array( 'type' => 'boolean'),
				'/Soussujetscers93/edit/#Soussujetcer93.id#' => array(
					'title' => true
				),
				'/Soussujetscers93/delete/#Soussujetcer93.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Soussujetcer93.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#contratsinsertion'
		)
	);
?>