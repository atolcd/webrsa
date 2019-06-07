<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Motifemissiontitrecreancier.id',
				'Motifemissiontitrecreancier.nom',
				'Motifemissiontitrecreancier.actif' => array( 'type' => 'boolean' ),
				'/Motifsemissionstitrescreanciers/edit/#Motifemissiontitrecreancier.id#' => array(
					'title' => true
				),
				'/Motifsemissionstitrescreanciers/delete/#Motifemissiontitrecreancier.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motifemissiontitrecreancier.has_linkedrecords#"'
				)
			)
		)
	);