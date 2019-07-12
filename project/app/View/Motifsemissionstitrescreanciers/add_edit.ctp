<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Motifemissiontitrecreancier.id',
				'Motifemissiontitrecreancier.nom',
				'Motifemissiontitrecreancier.emissiontitre' => array( 'type' => 'checkbox' ),
				'Motifemissiontitrecreancier.actif' => array( 'type' => 'checkbox', 'value' => 1 ),
			)
		)
	);
