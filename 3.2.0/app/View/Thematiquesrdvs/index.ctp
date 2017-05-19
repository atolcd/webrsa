<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Thematiquerdv.name',
				'Typerdv.libelle',
				'Statutrdv.libelle',
				'Thematiquerdv.linkedmodel',
				'/Thematiquesrdvs/edit/#Thematiquerdv.id#' => array(
					'title' => true
				),
				'/Thematiquesrdvs/delete/#Thematiquerdv.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Thematiquerdv.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>