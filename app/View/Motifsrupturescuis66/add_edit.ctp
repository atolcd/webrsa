<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Motifrupturecui66.id',
				'Motifrupturecui66.name',
				'Motifrupturecui66.actif' => array( 'type' => 'checkbox' )
			)
		)
	);
?>