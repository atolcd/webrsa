<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Sujetcer93.id',
				'Sujetcer93.name',
				'Sujetcer93.dreesactionscer_id' => array( 'type' => 'select', 'empty' => true ),
				'Sujetcer93.isautre' => array( 'type' => 'checkbox' ),
				'Sujetcer93.actif' => array( 'type' => 'checkbox', 'required' => false )
			)
		)
	);
?>