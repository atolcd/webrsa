<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Motifetatdossier.id',
				'Motifetatdossier.lib_motif',
				'Motifetatdossier.actif' => array( 'type' => 'checkbox', 'value' => 1 ),
			)
		)
	);