<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Progfichecandidature66.name',
				'Progfichecandidature66.isactif' => array( 'type' => 'boolean' ),
				'/Progsfichescandidatures66/edit/#Progfichecandidature66.id#' => array(
					'title' => true
				),
				'/Progsfichescandidatures66/delete/#Progfichecandidature66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Progfichecandidature66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#actionscandidats'
		)
	);
?>