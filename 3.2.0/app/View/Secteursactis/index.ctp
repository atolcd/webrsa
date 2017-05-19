<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Secteuracti.name',
				'/Secteursactis/edit/#Secteuracti.id#' => array(
					'title' => true
				),
				'/Secteursactis/delete/#Secteuracti.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Secteuracti.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#contratsinsertion'
		)
	);
?>