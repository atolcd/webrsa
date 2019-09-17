<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Originerecoursgracieux.nom',
				'Originerecoursgracieux.actif' => array( 'type' => 'boolean' ),
				'/Originesrecoursgracieux/edit/#Originerecoursgracieux.id#' => array(
					'title' => true
				),
				'/Originesrecoursgracieux/delete/#Originerecoursgracieux.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Originerecoursgracieux.has_linkedrecords#"'
				)
			)
		)
	);