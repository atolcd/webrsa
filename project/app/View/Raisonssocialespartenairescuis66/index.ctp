<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Raisonsocialepartenairecui66.name',
				'/Raisonssocialespartenairescuis66/edit/#Raisonsocialepartenairecui66.id#' => array(
					'title' => true
				),
				'/Raisonssocialespartenairescuis66/delete/#Raisonsocialepartenairecui66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Raisonsocialepartenairecui66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#actionscandidats'
		)
	);
?>