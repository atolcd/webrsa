<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typenotifpdo.libelle',
				'Typenotifpdo.modelenotifpdo',
				'/Typesnotifspdos/edit/#Typenotifpdo.id#' => array(
					'title' => true
				),
				'/Typesnotifspdos/delete/#Typenotifpdo.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typenotifpdo.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>