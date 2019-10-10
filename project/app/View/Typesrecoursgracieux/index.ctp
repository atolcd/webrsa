<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typerecoursgracieux.name',
				'Typerecoursgracieux.usage',
				'Typerecoursgracieux.actif' => array( 'type' => 'boolean' ),
				'/Typesrecoursgracieux/edit/#Typerecoursgracieux.id#' => array(
					'title' => true
				),
				'/Typesrecoursgracieux/delete/#Typerecoursgracieux.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typerecoursgracieux.has_linkedrecords#"'
				)
			)
		)
	);