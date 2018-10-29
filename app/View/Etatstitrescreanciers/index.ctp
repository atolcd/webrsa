<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Etattitrecreancier.name',
				'Etattitrecreancier.actif' => array( 'type' => 'boolean' ),
				'/Etatstitrescreanciers/edit/#Etattitrecreancier.id#' => array(
					'title' => true
				),
				'/Etatstitrescreanciers/delete/#Etattitrecreancier.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Etattitrecreancier.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#creances'
		)
	);
?>