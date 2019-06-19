<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Motifemissioncreance.id',
				'Motifemissioncreance.nom',
				'Motifemissioncreance.emissiontitre' => array( 'type' => 'checkbox' ),
				'Motifemissioncreance.actif' => array( 'type' => 'checkbox' ),
			)
		)
	);
