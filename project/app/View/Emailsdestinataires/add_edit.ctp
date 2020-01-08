<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Emaildestinataire.id',
				'Emaildestinataire.nom',
				'Emaildestinataire.prenom',
				'Emaildestinataire.email',
				'Emaildestinataire.structure',
				'Emaildestinataire.actif' => array( 'type' => 'checkbox', 'value' => 1 ),
			)
		)
	);