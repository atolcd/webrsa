<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Thematiquerdv.name',
				'Typerdv.libelle',
				'Statutrdv.libelle',
				'Thematiquerdv.linkedmodel',
				'Thematiquerdv.actif' => array(
					'type' => 'boolean'
				),
				'Thematiquerdv.acomptabiliser' => array(
					'type' => 'boolean'
				),
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