<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typetitrecreancierinfopayeur.id',
				'Typetitrecreancierinfopayeur.nom',
				'Typetitrecreancierinfopayeur.actif' => array( 'type' => 'checkbox' ),
			)
		)
	);
