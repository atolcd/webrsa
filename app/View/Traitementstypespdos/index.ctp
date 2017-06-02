<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Traitementtypepdo.name',
				'/Traitementstypespdos/edit/#Traitementtypepdo.id#' => array(
					'title' => true
				),
				'/Traitementstypespdos/delete/#Traitementtypepdo.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Traitementtypepdo.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>