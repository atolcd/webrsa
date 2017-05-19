<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Piececomptable66.name',
				'/Piecescomptables66/edit/#Piececomptable66.id#' => array(
					'title' => true
				),
				'/Piecescomptables66/delete/#Piececomptable66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Piececomptable66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#apres'
		)
	);
?>