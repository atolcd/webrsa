<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Sujetcer.libelle',
				'Soussujetcer.libelle',
				'/Soussujetscers/edit/#Soussujetcer.id#' => array(
					'title' => true
				),
				'/Soussujetscers/delete/#Soussujetcer.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Soussujetcer.has_linkedrecords#"'
				)
			)
		)
	);