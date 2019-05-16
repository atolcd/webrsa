<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Valeurparsoussujetcer93.id',
				'Valeurparsoussujetcer93.name',
				'Valeurparsoussujetcer93.soussujetcer93_id' => array( 'empty' => true ),
				'Valeurparsoussujetcer93.dreesactionscer_id' => array( 'type' => 'select', 'empty' => true ),
				'Valeurparsoussujetcer93.isautre' => array( 'type' => 'checkbox' ),
				'Valeurparsoussujetcer93.actif' => array( 'type' => 'checkbox', 'required' => false )
			)
		)
	);
?>