<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Role.name',
				'Role.actif' => array( 'type' => 'boolean' ),
				'/Roles/edit/#Role.id#' => array(
					'title' => true
				),
				'/Roles/delete/#Role.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Role.has_linkedrecords#"'
				)
			)
		)
	);
?>