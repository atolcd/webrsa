<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Valeurtag.name',
				'Categorietag.name',
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