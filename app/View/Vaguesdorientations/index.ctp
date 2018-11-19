<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Vaguedorientation.datedebut',
				'Vaguedorientation.datefin',
				'/Vaguesdorientations/edit/#Vaguedorientation.id#' => array(
					'title' => true
				),
				'/Vaguesdorientations/delete/#Vaguedorientation.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Vaguedorientation.has_linkedrecords#"'
				)
			)
		)
	);
?>