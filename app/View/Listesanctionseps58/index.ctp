<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Listesanctionep58.rang',
				'Listesanctionep58.sanction',
				'Listesanctionep58.duree',
				'/Listesanctionseps58/edit/#Listesanctionep58.id#' => array(
					'title' => true
				),
				'/Listesanctionseps58/delete/#Listesanctionep58.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Listesanctionep58.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#eps'
		)
	);
?>