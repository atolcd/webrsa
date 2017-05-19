<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Propositionprimo.id',
				'Propositionprimo.name',
				'Propositionprimo.actif' => array( 'type' => 'checkbox' ),
			)
		)
	);
?>