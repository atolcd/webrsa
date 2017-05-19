<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Naturecontrat.name',
				'Naturecontrat.isduree' => array( 'type' => 'boolean' ),
				'/Naturescontrats/edit/#Naturecontrat.id#' => array(
					'title' => true
				),
				'/Naturescontrats/delete/#Naturecontrat.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Naturecontrat.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#contratsinsertion'
		)
	);
?>