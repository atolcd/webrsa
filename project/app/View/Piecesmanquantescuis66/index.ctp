<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Piecemanquantecui66.name',
				'Piecemanquantecui66.actif' => array( 'type' => 'boolean' ),
				'/Piecesmanquantescuis66/edit/#Piecemanquantecui66.id#' => array(
					'title' => true
				),
				'/Piecesmanquantescuis66/delete/#Piecemanquantecui66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Piecemanquantecui66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#cuis'
		)
	);
?>