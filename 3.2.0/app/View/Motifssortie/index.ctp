<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Motifsortie.name',
				'/Motifssortie/edit/#Motifsortie.id#' => array(
					'title' => true
				),
				'/Motifssortie/delete/#Motifsortie.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motifsortie.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#actionscandidats'
		)
	);
?>