<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Motifproposrecoursgracieux.id',
				'Motifproposrecoursgracieux.nom',
				'Motifproposrecoursgracieux.actif' => array( 'type' => 'checkbox', 'value' => 1 ),
			)
		)
	);
?>