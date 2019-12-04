<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Motifproposrecoursgracieux.nom',
				'Motifproposrecoursgracieux.actif' => array( 'type' => 'boolean' ),
				'/Motifsproposrecoursgracieux/edit/#Motifproposrecoursgracieux.id#' => array(
					'title' => true
				),
				'/Motifsproposrecoursgracieux/delete/#Motifproposrecoursgracieux.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motifproposrecoursgracieux.has_linkedrecords#"'
				)
			)
		)
	);