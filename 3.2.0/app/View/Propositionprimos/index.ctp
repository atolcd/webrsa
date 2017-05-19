<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Propositionprimo.name',
				'Propositionprimo.actif' => array( 'type' => 'boolean' ),
				'/Propositionprimos/edit/#Propositionprimo.id#' => array(
					'title' => true
				),
				'/Propositionprimos/delete/#Propositionprimo.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Propositionprimo.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#cuis'
		)
	);
?>