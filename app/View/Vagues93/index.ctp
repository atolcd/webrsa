<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Vague93.datedebut',
				'Vague93.datefin',
				'/Vagues93/edit/#Vague93.id#' => array(
					'title' => true
				),
				'/Vagues93/delete/#Vague93.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Vague93.has_linkedrecords#"'
				)
			)
		)
	);
?>