<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Statutpdo.libelle',
				'Statutpdo.isactif',
				'/Statutspdos/edit/#Statutpdo.id#' => array(
					'title' => true
				),
				'/Statutspdos/delete/#Statutpdo.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Statutpdo.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>