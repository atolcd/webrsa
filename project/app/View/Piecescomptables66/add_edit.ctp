<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Piececomptable66.id',
				'Piececomptable66.name',
				'Piececomptable66.actif' => array( 'type' => 'checkbox' )
			)
		)
	);
?>