<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Motifetatpe.lib_motif',
				'Motifetatpe.actif' => array( 'type' => 'boolean' ),
				'/Motifsetatspes/edit/#Motifetatpe.id#' => array(
					'title' => true
				),
				'/Motifsetatspes/delete/#Motifetatpe.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motifetatpe.has_linkedrecords#"'
				)
			)
		)
	);