<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Descriptionpdo.name',
				'Descriptionpdo.modelenotification',
				'Descriptionpdo.sensibilite',
				'Descriptionpdo.decisionpcg',
				'Descriptionpdo.dateactive',
				'Descriptionpdo.nbmoisecheance',
				'/Descriptionspdos/edit/#Descriptionpdo.id#' => array(
					'title' => true
				),
				'/Descriptionspdos/delete/#Descriptionpdo.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Descriptionpdo.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>