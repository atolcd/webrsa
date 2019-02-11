<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Relance.relancesupport',
				'Relance.relancetype',
				'Relance.relancemode',
				'Relance.nombredejour',
				'Relance.contenu',
				'Relance.actif' => array( 'type' => 'boolean' ),
				'/Relances/edit/#Relance.id#' => array(
					'title' => true
				),
				'/Relances/delete/#Relance.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Relance.has_linkedrecords#"'
				)
			)
		)
	);
?>