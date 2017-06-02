<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Service66.id',
				'Service66.name',
				'Service66.interne' => array( 'empty' => true ),
				'Service66.actif' => array( 'type' => 'checkbox' )
			)
		)
	);
?>