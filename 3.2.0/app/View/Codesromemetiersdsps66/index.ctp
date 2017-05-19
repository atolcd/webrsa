<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Coderomemetierdsp66.code',
				'Coderomemetierdsp66.name',
				'Coderomesecteurdsp66.intitule',
				'/Codesromemetiersdsps66/edit/#Coderomemetierdsp66.id#' => array(
					'title' => true
				),
				'/Codesromemetiersdsps66/delete/#Coderomemetierdsp66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Coderomemetierdsp66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#dsps'
		)
	);
?>