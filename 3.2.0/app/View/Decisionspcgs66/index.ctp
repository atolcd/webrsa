<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Decisionpcg66.name',
				'Decisionpcg66.nbmoisecheance',
				'Decisionpcg66.courriernotif',
				'Decisionpcg66.actif',
				'/Decisionspcgs66/edit/#Decisionpcg66.id#' => array(
					'title' => true
				),
				'/Decisionspcgs66/delete/#Decisionpcg66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Decisionpcg66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#decisionsdossierspcgs66'
		)
	);
?>