<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typersapcg66.name',
				'/Typesrsapcgs66/edit/#Typersapcg66.id#' => array(
					'title' => true
				),
				'/Typesrsapcgs66/delete/#Typersapcg66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typersapcg66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>