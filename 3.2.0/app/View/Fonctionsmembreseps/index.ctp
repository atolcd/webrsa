<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Fonctionmembreep.name',
				'/Fonctionsmembreseps/edit/#Fonctionmembreep.id#' => array(
					'title' => true
				),
				'/Fonctionsmembreseps/delete/#Fonctionmembreep.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Fonctionmembreep.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#eps'
		)
	);
?>