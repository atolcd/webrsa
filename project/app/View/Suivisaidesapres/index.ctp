<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Suiviaideapre.qual',
				'Suiviaideapre.nom',
				'Suiviaideapre.prenom',
				'Suiviaideapre.numtel',
				'/Suivisaidesapres/edit/#Suiviaideapre.id#' => array(
					'title' => true
				),
				'/Suivisaidesapres/delete/#Suiviaideapre.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Suiviaideapre.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#apres'
		)
	);
?>