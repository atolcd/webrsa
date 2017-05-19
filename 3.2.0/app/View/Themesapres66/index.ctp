<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Themeapre66.name',
				'/Themesapres66/edit/#Themeapre66.id#' => array(
					'title' => true
				),
				'/Themesapres66/delete/#Themeapre66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Themeapre66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#apres'
		)
	);
?>