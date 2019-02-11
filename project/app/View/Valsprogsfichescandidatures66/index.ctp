<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Progfichecandidature66.name',
				'Progfichecandidature66.isactif' => array( 'type' => 'boolean' ),
				'Valprogfichecandidature66.name',
				'Valprogfichecandidature66.actif' => array( 'type' => 'boolean' ),
				'/Valsprogsfichescandidatures66/edit/#Valprogfichecandidature66.id#' => array(
					'title' => true
				),
				'/Valsprogsfichescandidatures66/delete/#Valprogfichecandidature66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Valprogfichecandidature66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#actionscandidats'
		)
	);
?>