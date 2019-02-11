<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Sitecov58.name',
				'/Sitescovs58/edit/#Sitecov58.id#' => array(
					'title' => true
				),
				'/Sitescovs58/delete/#Sitecov58.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Sitecov58.has_linkedrecords#"'
				)
			)
		)
	);
?>