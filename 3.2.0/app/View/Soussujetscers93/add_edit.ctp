<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Soussujetcer93.id',
				'Soussujetcer93.name',
				'Soussujetcer93.sujetcer93_id' => array( 'empty' => true ),
				'Soussujetcer93.isautre' => array( 'type' => 'checkbox' ),
				'Soussujetcer93.actif' => array( 'type' => 'checkbox', 'required' => false )
			)
		)
	);
?>