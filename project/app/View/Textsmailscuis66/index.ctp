<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Textmailcui66.name',
				'Textmailcui66.sujet',
				'Textmailcui66.contenu',
				'Textmailcui66.actif' => array( 'type' => 'boolean' ),
				'/Textsmailscuis66/edit/#Textmailcui66.id#' => array(
					'title' => true
				),
				'/Textsmailscuis66/delete/#Textmailcui66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Textmailcui66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#cuis'
		)
	);
?>