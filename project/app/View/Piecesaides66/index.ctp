<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Pieceaide66.name',
				'Pieceaide66.actif' => array( 'type' => 'boolean' ),
				'/Piecesaides66/edit/#Pieceaide66.id#' => array(
					'title' => true
				),
				'/Piecesaides66/delete/#Pieceaide66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Pieceaide66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#apres'
		)
	);
?>