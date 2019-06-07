<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Motifemissiontitrecreancier.id',
				'Motifemissiontitrecreancier.nom',
				'Motifemissiontitrecreancier.actif' => array( 'type' => 'checkbox' ),
			)
		)
	);
