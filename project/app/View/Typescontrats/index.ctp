<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typecontrat.libelle',
				'/typescontrats/edit/#Typecontrat.id#' => array(
					'title' => true
				),
				'/typescontrats/delete/#Typecontrat.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typecontrat.has_linkedrecords#"'
				)
			)
		)
	);