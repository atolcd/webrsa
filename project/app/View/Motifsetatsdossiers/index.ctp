<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Motifetatdossier.lib_motif',
				'Motifetatdossier.actif' => array( 'type' => 'boolean' ),
				'/Motifsetatsdossiers/edit/#Motifetatdossier.id#' => array(
					'title' => true
				),
				'/Motifsetatsdossiers/delete/#Motifetatdossier.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motifetatdossier.has_linkedrecords#"'
				)
			)
		)
	);