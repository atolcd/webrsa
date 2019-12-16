<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Textemail.name',
				'Textemail.sujet',
				'Textemail.contenu',
				'Textemail.actif' => array( 'type' => 'boolean' ),
				'/Textsemails/edit/#Textemail.id#' => array(
					'title' => true
				),
				'/Textsemails/delete/#Textemail.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Textemail.has_linkedrecords#"'
				)
			)
		)
	);