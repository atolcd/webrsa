<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typecontratcui66.name',
				'Typecontratcui66.actif' => array( 'type' => 'boolean' ),
				'/Typescontratscuis66/edit/#Typecontratcui66.id#' => array(
					'title' => true
				),
				'/Typescontratscuis66/delete/#Typecontratcui66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typecontratcui66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#cuis'
		)
	);
?>