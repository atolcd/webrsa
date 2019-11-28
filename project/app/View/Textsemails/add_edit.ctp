<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Textemail.id',
				'Textemail.name',
				'Textemail.sujet',
				'Textemail.contenu',
				'Textemail.actif' => array( 'type' => 'checkbox' ),
			)
		)
	);
?>