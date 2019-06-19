<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Motifemissioncreance.id',
				'Motifemissioncreance.nom',
				'Motifemissioncreance.emissiontitre' => array( 'type' => 'boolean' ),
				'Motifemissioncreance.actif' => array( 'type' => 'boolean' ),
				'/Motifsemissionscreances/edit/#Motifemissioncreance.id#' => array(
					'title' => true
				),
				'/Motifsemissionscreances/delete/#Motifemissioncreance.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motifemissioncreance.has_linkedrecords#"'
				)
			)
		)
	);