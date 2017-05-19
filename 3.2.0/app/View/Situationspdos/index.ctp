<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Situationpdo.libelle',
				'Situationpdo.isactif',
				'/Situationspdos/edit/#Situationpdo.id#' => array(
					'title' => true
				),
				'/Situationspdos/delete/#Situationpdo.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Situationpdo.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>