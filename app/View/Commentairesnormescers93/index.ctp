<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Commentairenormecer93.name',
				'Commentairenormecer93.isautre' => array( 'type' => 'boolean' ),
				'/Commentairesnormescers93/edit/#Commentairenormecer93.id#' => array(
					'title' => true
				),
				'/Commentairesnormescers93/delete/#Commentairenormecer93.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Commentairenormecer93.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#contratsinsertion'
		)
	);
?>