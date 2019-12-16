<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Emaildestinataire.nom',
				'Emaildestinataire.prenom',
				'Emaildestinataire.email',
				'Emaildestinataire.structure',
				'Emaildestinataire.actif' => array( 'type' => 'boolean' ),
				'/Emailsdestinataires/edit/#Emaildestinataire.id#' => array(
					'title' => true
				),
				'/Emailsdestinataires/delete/#Emaildestinataire.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Emaildestinataire.has_linkedrecords#"'
				)
			)
		)
	);