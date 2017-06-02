<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Role.name',
				'Categorieactionrole.name',
				'Actionrole.name',
				'Actionrole.description',
				'/Actionroles/edit/#Actionrole.id#' => array(
					'title' => true
				),
				'/Actionroles/delete/#Actionrole.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Actionrole.has_linkedrecords#"'
				)
			)
		)
	);
?>