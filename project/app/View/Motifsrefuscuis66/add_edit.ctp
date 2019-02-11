<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Motifrefuscui66.id',
				'Motifrefuscui66.name',
				'Motifrefuscui66.actif' => array( 'type' => 'checkbox' )
			)
		)
	);
?>