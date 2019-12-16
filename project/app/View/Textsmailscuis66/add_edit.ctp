<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Textmailcui66.id',
				'Textmailcui66.name',
				'Textmailcui66.sujet',
				'Textmailcui66.contenu',
				'Textmailcui66.actif' => array( 'type' => 'checkbox' ),
			)
		)
	);