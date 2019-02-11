<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Action.code',
				'Action.libelle',
				'Typeaction.libelle',
				'/Actions/edit/#Action.id#' => array(
					'title' => true
				),
				'/Actions/delete/#Action.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Action.has_linkedrecords#"'
				)
			)
		)
	);
?>