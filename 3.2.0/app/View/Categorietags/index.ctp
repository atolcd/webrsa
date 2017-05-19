<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Categorietag.name',
				'/Categorietags/edit/#Categorietag.id#' => array(
					'title' => true
				),
				'/Categorietags/delete/#Categorietag.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Categorietag.has_linkedrecords#"'
				)
			)
		)
	);
?>