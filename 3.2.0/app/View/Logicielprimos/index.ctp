<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Logicielprimo.name',
				'Logicielprimo.actif' => array( 'type' => 'boolean' ),
				'/Logicielprimos/edit/#Logicielprimo.id#' => array(
					'title' => true
				),
				'/Logicielprimos/delete/#Logicielprimo.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Logicielprimo.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#cuis'
		)
	);
?>