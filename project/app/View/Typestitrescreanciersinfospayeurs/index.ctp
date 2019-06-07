<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typetitrecreancierinfopayeur.id',
				'Typetitrecreancierinfopayeur.nom',
				'Typetitrecreancierinfopayeur.actif' => array( 'type' => 'boolean' ),
				'/Typestitrescreanciersinfospayeurs/edit/#Typetitrecreancierinfopayeur.id#' => array(
					'title' => true
				),
				'/Typestitrescreanciersinfospayeurs/delete/#Typetitrecreancierinfopayeur.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typetitrecreancierinfopayeur.has_linkedrecords#"'
				)
			)
		)
	);