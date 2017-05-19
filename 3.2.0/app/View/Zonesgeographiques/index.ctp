<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Zonegeographique.libelle',
				'Zonegeographique.codeinsee',
				'/Zonesgeographiques/edit/#Zonegeographique.id#' => array(
					'title' => true
				),
				'/Zonesgeographiques/delete/#Zonegeographique.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Zonegeographique.has_linkedrecords#"'
				)
			)
		)
	);
?>