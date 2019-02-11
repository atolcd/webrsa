<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Courrierpdo.name',
				'Textareacourrierpdo.nomchampodt',
				'Textareacourrierpdo.name',
				'Textareacourrierpdo.ordre',
				'/Textareascourrierspdos/edit/#Textareacourrierpdo.id#' => array(
					'title' => true
				),
				'/Textareascourrierspdos/delete/#Textareacourrierpdo.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Textareacourrierpdo.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>