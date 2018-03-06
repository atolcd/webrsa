<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Valeurtag.name',
				'Categorietag.name',
				'Valeurtag.actif' => array( 'type' => 'boolean' ),
				'/Valeurstags/edit/#Valeurtag.id#' => array(
					'title' => true
				),
				'/Valeurstags/delete/#Valeurtag.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Valeurtag.has_linkedrecords#"'
				)
			)
		)
	);
?>