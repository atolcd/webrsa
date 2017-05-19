<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typerdv.libelle',
				'Statutrdv.libelle',
				'StatutrdvTyperdv.nbabsenceavantpassagecommission',
				'StatutrdvTyperdv.typecommission',
				'StatutrdvTyperdv.motifpassageep',
				'/StatutsrdvsTypesrdv/edit/#StatutrdvTyperdv.id#' => array(
					'title' => true
				),
				'/StatutsrdvsTypesrdv/delete/#StatutrdvTyperdv.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#StatutrdvTyperdv.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#rendezvous'
		)
	);
?>