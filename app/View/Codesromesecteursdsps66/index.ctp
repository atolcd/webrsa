<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Coderomesecteurdsp66.code',
				'Coderomesecteurdsp66.name',
				'/Codesromesecteursdsps66/edit/#Coderomesecteurdsp66.id#' => array(
					'title' => true
				),
				'/Codesromesecteursdsps66/delete/#Coderomesecteurdsp66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Coderomesecteurdsp66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#dsps'
		)
	);
?>