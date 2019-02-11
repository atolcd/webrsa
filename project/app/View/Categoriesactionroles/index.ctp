<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Categorieactionrole.name',
				'/Categoriesactionroles/edit/#Categorieactionrole.id#' => array(
					'title' => true
				),
				'/Categoriesactionroles/delete/#Categorieactionrole.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Categorieactionrole.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#dashboards'
		)
	);
?>