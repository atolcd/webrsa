<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typetitrecreancier.name',
				'Typetitrecreancier.actif' => array( 'type' => 'boolean' ),
				'/Typestitrescreanciers/edit/#Typetitrecreancier.id#' => array(
					'title' => true
				),
				'/Typestitrescreanciers/delete/#Typetitrecreancier.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typetitrecreancier.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#creances'
		)
	);
?>