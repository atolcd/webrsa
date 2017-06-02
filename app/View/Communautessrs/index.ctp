<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Communautesr.name',
				'Communautesr.actif',
				'/Communautessrs/edit/#Communautesr.id#' => array(
					'title' => true
				),
				'/Communautessrs/delete/#Communautesr.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Communautesr.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index'
		)
	);
?>