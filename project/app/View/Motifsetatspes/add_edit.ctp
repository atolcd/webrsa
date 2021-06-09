<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Motifetatpe.id',
				'Motifetatpe.lib_motif',
				'Motifetatpe.actif' => array( 'type' => 'checkbox', 'value' => 1 ),
			)
		)
	);