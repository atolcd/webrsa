<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Sujetcer.libelle',
				'Sujetcer.champtexte',
				'/Sujetscers/edit/#Sujetcer.id#' => array(
					'title' => true
				),
				'/Sujetscers/delete/#Sujetcer.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Sujetcer.has_linkedrecords#"'
				)
			)
		)
	);
?>