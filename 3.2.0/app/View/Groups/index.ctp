<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Group.name',
				'ParentGroup.name',
				'/Groups/edit/#Group.id#' => array(
					'title' => true
				),
				'/Groups/delete/#Group.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Group.has_linkedrecords#"'
				)
			)
		)
	);
?>