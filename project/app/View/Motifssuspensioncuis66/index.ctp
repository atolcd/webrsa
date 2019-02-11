<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Motifsuspensioncui66.name',
				'Motifsuspensioncui66.actif' => array( 'type' => 'boolean' ),
				'/Motifssuspensioncuis66/edit/#Motifsuspensioncui66.id#' => array(
					'title' => true
				),
				'/Motifssuspensioncuis66/delete/#Motifsuspensioncui66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motifsuspensioncui66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#cuis'
		)
	);
?>