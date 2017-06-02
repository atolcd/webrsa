<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Service66.name',
				'Service66.interne' => array( 'type' => 'boolean' ),
				'Service66.actif' => array( 'type' => 'boolean' ),
				'/Services66/edit/#Service66.id#' => array(
					'title' => true
				),
				'/Services66/delete/#Service66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Service66.has_linkedrecords#"'
				)
			)
		)
	);
?>