<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Soussujetcer93.name',
				'Valeurparsoussujetcer93.name',
				'Valeurparsoussujetcer93.isautre' => array( 'type' => 'boolean'),
				'Valeurparsoussujetcer93.actif' => array( 'type' => 'boolean'),
				'/Valeursparsoussujetscers93/edit/#Valeurparsoussujetcer93.id#' => array(
					'title' => true
				),
				'/Valeursparsoussujetscers93/delete/#Valeurparsoussujetcer93.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Valeurparsoussujetcer93.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#contratsinsertion'
		)
	);
?>