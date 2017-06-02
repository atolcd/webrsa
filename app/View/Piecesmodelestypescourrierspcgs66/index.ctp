<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typecourrierpcg66.name',
				'Modeletypecourrierpcg66.name',
				'Piecemodeletypecourrierpcg66.name',
				'Piecemodeletypecourrierpcg66.isautrepiece' => array( 'type' => 'boolean' ),
				'Piecemodeletypecourrierpcg66.isactif' => array( 'type' => 'boolean' ),
				'/Piecesmodelestypescourrierspcgs66/edit/#Piecemodeletypecourrierpcg66.id#' => array(
					'title' => true
				),
				'/Piecesmodelestypescourrierspcgs66/delete/#Piecemodeletypecourrierpcg66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Piecemodeletypecourrierpcg66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#courrierspcgs66'
		)
	);
?>