<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Themeapre66.name',
				'Typeaideapre66.name',
				'Typeaideapre66.isincohorte',
				'Typeaideapre66.actif'  => array( 'type' => 'boolean' ),
				'/Typesaidesapres66/edit/#Typeaideapre66.id#' => array(
					'title' => true
				),
				'/Typesaidesapres66/delete/#Typeaideapre66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typeaideapre66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#apres'
		)
	);
?>